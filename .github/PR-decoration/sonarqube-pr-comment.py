import os
import json
import requests
import time

# rstrip("/") is load-bearing: every API URL below is built as
# f"{SONAR_HOST_URL}/api/...", so a secret stored as "https://sonar.example.com/"
# yields "...//api/..." — and SonarQube answers any path that misses an API route
# with its SPA index.html under HTTP 200, which then explodes inside .json().
# The scanner's own report-task.txt URL is used verbatim and so is unaffected,
# which is why the CE poll succeeds while every metric fetch fails.
SONAR_HOST_URL = (os.getenv("SONAR_HOST_URL") or "").rstrip("/")
SONAR_PROJECT_KEY = os.getenv("SONAR_PROJECT_KEY")
SONAR_TOKEN = os.getenv("SONAR_TOKEN")
PR_NUMBER = os.getenv("PR_NUMBER")
MERGE_BRANCH = os.getenv("MERGE_BRANCH")
IS_PR = os.getenv("GITHUB_EVENT_NAME") == "pull_request"
CURRENT_BRANCH = os.getenv("CURRENT_BRANCH")
GITHUB_ACTOR = os.getenv("GITHUB_ACTOR", "contributor")
PR_TITLE = os.getenv("PR_TITLE", "Code Merge")

# Head branch of the PR. PR_BRANCH is what the workflow passes explicitly;
# GITHUB_HEAD_REF is GitHub's own value on pull_request events; CURRENT_BRANCH is
# the last resort. Without these fallbacks the header renders "From `None`"
# whenever PR_BRANCH isn't exported.
PR_BRANCH = (
    os.getenv("PR_BRANCH")
    or os.getenv("GITHUB_HEAD_REF")
    or CURRENT_BRANCH
    or "unknown"
)

if not IS_PR:
    MERGE_BRANCH = CURRENT_BRANCH

# ─── Tunable waits ───────────────────────────────────────────────────────────
# Env-overridable so a rerun (or a local dry run) needn't sit through the whole CI
# budget. ANALYSIS_* covers the compute-engine task poll, MEASURE_* the
# measures-readiness poll. Defaults are the previous hardcoded values.
def _env_int(name, default):
    raw = os.getenv(name)
    if raw is None or str(raw).strip() == "":
        return default
    try:
        val = int(float(raw))
    except (ValueError, TypeError):
        return default
    return val if val >= 0 else default

ANALYSIS_MAX_RETRIES     = _env_int("ANALYSIS_MAX_RETRIES", 30)
ANALYSIS_WAIT_SECONDS    = _env_int("ANALYSIS_WAIT_SECONDS", 10)
ANALYSIS_PREPOLL_SECONDS = _env_int("ANALYSIS_PREPOLL_SECONDS", 15)
MEASURE_MAX_RETRIES      = _env_int("MEASURE_MAX_RETRIES", 24)
MEASURE_WAIT_SECONDS     = _env_int("MEASURE_WAIT_SECONDS", 5)
# How many consecutive empty fetches to require before concluding "this scope
# really has no measures" once the analysis is already confirmed SUCCESS. Small
# on purpose: it only guards the CE-done-but-measures-lagging window.
NO_MEASURE_CONFIRM_ATTEMPTS = _env_int("NO_MEASURE_CONFIRM_ATTEMPTS", 3)

# ─── Metric chunks (split to stay under SonarQube's 15-metric API limit) ─────
# Server runs in MQR (Multi-Quality Rule) mode, so the legacy bugs/vulnerabilities/
# code_smells metrics are decoupled from what the dashboard & quality gate use.
# We source counts from the MQR software-quality issue metrics instead:
#   software_quality_reliability_issues   (was: bugs)
#   software_quality_security_issues      (was: vulnerabilities)
#   software_quality_maintainability_issues (was: code_smells)
# and the *_issues distribution metrics (reliability_issues, ...) give the
# per-severity breakdown used for the priority label.
if IS_PR:
    m_chunks = [
        "new_software_quality_reliability_issues,new_software_quality_security_issues,new_software_quality_maintainability_issues,new_security_hotspots,new_lines,new_violations,new_coverage,new_duplicated_lines_density,new_software_quality_maintainability_remediation_effort",
        "new_reliability_issues,new_security_issues,new_maintainability_issues,ncloc,sqale_index"
    ]
else:
    m_chunks = [
        "software_quality_reliability_issues,software_quality_security_issues,software_quality_maintainability_issues,security_hotspots,coverage,duplicated_lines_density,violations,security_hotspots_reviewed",
        "reliability_issues,security_issues,maintainability_issues,ncloc,sqale_index"
    ]

# ─── Defensive JSON parsing ──────────────────────────────────────────────────
def safe_json(res):
    """Parsed JSON body, or None when the body is not JSON at all.

    SonarQube serves its single-page frontend with HTTP 200 for unmatched paths,
    and reverse proxies emit HTML error pages. An unguarded .json() turns either
    into a hard crash that kills the whole reporting step; returning None lets
    each caller use its own "could not read this" path instead.
    """
    try:
        return res.json()
    except (ValueError, TypeError):
        return None

# ─── Wait for SonarQube analysis ─────────────────────────────────────────────
def wait_for_analysis(max_retries=ANALYSIS_MAX_RETRIES, wait_seconds=ANALYSIS_WAIT_SECONDS):
    print("Waiting for SonarQube analysis to complete...")
    
    task_url = None
    if os.path.exists(".scannerwork/report-task.txt"):
        with open(".scannerwork/report-task.txt", "r") as f:
            for line in f:
                if line.startswith("ceTaskUrl="):
                    task_url = line.strip().split("=", 1)[1]
                    break
                    
    if task_url:
        print(f"Found task URL: {task_url}")
        for i in range(max_retries):
            res = requests.get(task_url, auth=(SONAR_TOKEN, ""))
            status = "UNKNOWN"
            if res.status_code == 200:
                status = (safe_json(res) or {}).get("task", {}).get("status", "UNKNOWN")
                if status == "SUCCESS":
                    print(f"Analysis complete after {i + 1} attempt(s).")
                    return True
                elif status in ("FAILED", "CANCELED"):
                    print(f"Analysis stopped with status: {status}")
                    return False
            time.sleep(wait_seconds)
    else:
        print(f"report-task.txt not found, waiting {ANALYSIS_PREPOLL_SECONDS}s before component queue polling...")
        time.sleep(ANALYSIS_PREPOLL_SECONDS)
        for i in range(max_retries):
            ce_url = f"{SONAR_HOST_URL}/api/ce/component"
            ce_res = requests.get(ce_url, auth=(SONAR_TOKEN, ""), params={"component": SONAR_PROJECT_KEY})
            if ce_res.status_code == 200:
                data = safe_json(ce_res) or {}
                if len(data.get("queue", [])) == 0 and data.get("current", {}).get("status") not in ("PENDING", "IN_PROGRESS"):
                    return True
            time.sleep(wait_seconds)
            
    print("Warning: Analysis did not complete in time. Proceeding anyway.")
    return False

ANALYSIS_CONFIRMED = wait_for_analysis()

# ─── Fetch metrics (with readiness retry) ──────────────────────────────────────
# Race guard: right after the CE task reports done, SonarQube may not have the
# new-code / component measures queryable yet, so a single fetch can come back
# empty and every metric would default to 0 — the table then shows 0 while the
# quality gate (read a moment later) shows the real numbers. We retry the fetch
# until measures are actually ready: ncloc must be populated (proves the component
# measures are loaded) and, for PRs, new_lines must be present too.
def fetch_measures_once():
    # fetch_errors collects HTTP failures (401/403/5xx...) that are NOT "this
    # scope is empty". Without this, a bad token reads exactly like a clean PR
    # and we would publish a table of zeros as if it were fact.
    d, present, fetch_errors = {}, set(), []
    for m_chunk in m_chunks:
        params = {"component": SONAR_PROJECT_KEY, "metricKeys": m_chunk, "additionalFields": "period"}
        if IS_PR and PR_NUMBER:
            params["pullRequest"] = PR_NUMBER
        elif CURRENT_BRANCH:
            params["branch"] = CURRENT_BRANCH

        res = requests.get(f"{SONAR_HOST_URL}/api/measures/component", auth=(SONAR_TOKEN, ""), params=params)
        if res.status_code == 404 and "branch" in params:
            del params["branch"]
            res = requests.get(f"{SONAR_HOST_URL}/api/measures/component", auth=(SONAR_TOKEN, ""), params=params)

        # Resilience: SonarQube 404s the WHOLE request if any single metric key is
        # unknown on this version. Strip the offending key(s) and retry so one bad
        # key can't wipe out every other metric in the chunk.
        if res.status_code == 404:
            try:
                msg = " ".join(e.get("msg", "") for e in res.json().get("errors", []))
            except (ValueError, TypeError):
                msg = ""
            if "not found:" in msg:
                bad = {k.strip() for k in msg.split("not found:", 1)[1].split(",")}
                keys = [k for k in params["metricKeys"].split(",") if k.strip() not in bad]
                if keys:
                    params["metricKeys"] = ",".join(keys)
                    res = requests.get(f"{SONAR_HOST_URL}/api/measures/component", auth=(SONAR_TOKEN, ""), params=params)

        if res.status_code == 200:
            payload = safe_json(res)
            if payload is None:
                # HTTP 200 with a non-JSON body means we never actually reached the
                # API (SPA fallback / proxy page). Record it as a failure rather
                # than "empty scope", so the report is flagged unverified instead
                # of publishing a table of zeros as if it were fact.
                fetch_errors.append("HTTP 200 (non-JSON body — check SONAR_HOST_URL)")
                continue
            for m in payload.get("component", {}).get("measures", []):
                val = m.get("value")
                if val is None:
                    if "period" in m and isinstance(m["period"], dict):
                        val = m["period"].get("value")
                    elif "periods" in m and isinstance(m["periods"], list) and len(m["periods"]) > 0:
                        val = m["periods"][0].get("value")
                if val is not None:
                    d[m["metric"]] = val
                    present.add(m["metric"])
        elif res.status_code != 404:
            # 404 after the fallbacks above legitimately means "scope/metric not
            # there". Anything else (401 bad token, 403, 5xx) is our failure.
            fetch_errors.append(f"HTTP {res.status_code}")
    return d, present, fetch_errors

def measures_populated(present):
    # "Did this scope return usable numbers?" — deliberately scope-aware.
    # PR scope is described by its new-code measures; branch scope by ncloc.
    if IS_PR:
        return any(k in present for k in
                   ("new_lines", "new_violations", "new_coverage", "new_duplicated_lines_density"))
    return "ncloc" in present

# Readiness signal: the authoritative "this analysis is final" answer is the CE
# task reporting SUCCESS (ANALYSIS_CONFIRMED above) — NOT the presence of any
# particular metric key.
#
# Why this matters: SonarQube returns "value": null for zero-valued new-code
# measures, and a PR component has NO ncloc/new_lines entry at all when the diff
# touches no analyzed files (e.g. a .py/.yml-only PR against a PHP-only scan).
# The previous gate required ncloc AND new_lines to be present, which no PR on
# this server ever satisfies — it failed 100% of PRs (verified on PRs 739 & 742,
# whose measures are still absent hours after a SUCCESS analysis).
#
# So we retry only to cover the genuine short window where the CE task is done
# but measures are not queryable yet, then accept the empty result as real.
data, _present, _fetch_errors = {}, set(), []
_empty_streak = 0
for _attempt in range(MEASURE_MAX_RETRIES):
    data, _present, _fetch_errors = fetch_measures_once()
    if measures_populated(_present):
        if _attempt:
            print(f"Measures ready after {_attempt + 1} attempt(s).", flush=True)
        break
    if _fetch_errors:
        # Transport/auth failure: retrying may help, but we must never conclude
        # "no new code" from it. Keep retrying, then fall through to STALE.
        print(f"Measures fetch failed ({', '.join(sorted(set(_fetch_errors)))}) "
              f"(attempt {_attempt + 1}/{MEASURE_MAX_RETRIES}); "
              f"waiting {MEASURE_WAIT_SECONDS}s...", flush=True)
        time.sleep(MEASURE_WAIT_SECONDS)
        continue
    _empty_streak += 1
    if ANALYSIS_CONFIRMED and _empty_streak >= NO_MEASURE_CONFIRM_ATTEMPTS:
        # Analysis is final and the scope still reports nothing: this is a real
        # "no new analyzed code" result, not a race. Stop burning CI minutes.
        print(f"Analysis is final and this scope reports no measures after "
              f"{_empty_streak} check(s) — treating as a no-new-code result.", flush=True)
        break
    print(f"Measures not ready yet (attempt {_attempt + 1}/{MEASURE_MAX_RETRIES}); "
          f"waiting {MEASURE_WAIT_SECONDS}s...", flush=True)
    time.sleep(MEASURE_WAIT_SECONDS)

# ─── Label an empty scope instead of hiding it ───────────────────────────────
# The PR comment is posted in every condition so the TL always has a report to
# read at merge time. But an unlabelled table of zeros is what made the earlier
# comments look broken, so we mark *why* it is zero:
#   NO_ANALYZED_CHANGES — analysis is final, the PR genuinely changed no analyzed
#                         code. Zeros are the truth; say so.
#   MEASURES_STALE      — we could NOT confirm the analysis and got nothing back.
#                         Zeros may be fiction; warn loudly in the comment.
# A fetch error is never allowed to read as "no new code" — it forces STALE.
_MEASURES_EMPTY = not measures_populated(_present)
NO_ANALYZED_CHANGES = (IS_PR and ANALYSIS_CONFIRMED and _MEASURES_EMPTY
                       and not _fetch_errors)
MEASURES_STALE = _MEASURES_EMPTY and (not ANALYSIS_CONFIRMED or bool(_fetch_errors))
if MEASURES_STALE:
    _scope = f"PR {PR_NUMBER}" if IS_PR and PR_NUMBER else f"branch {CURRENT_BRANCH}"
    _why = (f"the measures API kept failing ({', '.join(sorted(set(_fetch_errors)))})"
            if _fetch_errors else "could not confirm the analysis")
    print(f"WARNING: {_why} and {_scope} returned no measures "
          f"after {MEASURE_MAX_RETRIES} attempt(s); the report will carry a stale-data "
          f"warning instead of presenting zeros as fact.", flush=True)

# ─── MQR severity distributions (per software quality) ─────────────────────────
# The *_issues metrics carry a per-severity breakdown, e.g.
#   {"INFO":0,"LOW":7,"MEDIUM":0,"HIGH":0,"BLOCKER":0,"total":7}
# We parse them to derive each row's priority label using MQR severities
# (BLOCKER / HIGH / MEDIUM / LOW / INFO) — consistent with the dashboard.
def parse_dist(raw):
    if isinstance(raw, dict):
        return raw
    if isinstance(raw, str):
        try:
            return json.loads(raw)
        except (ValueError, TypeError):
            return {}
    return {}

_dist_pfx = "new_" if IS_PR else ""
dist_map = {
    "RELIABILITY":     parse_dist(data.get(f"{_dist_pfx}reliability_issues")),
    "SECURITY":        parse_dist(data.get(f"{_dist_pfx}security_issues")),
    "MAINTAINABILITY": parse_dist(data.get(f"{_dist_pfx}maintainability_issues")),
}

# ─── Quality Gate ────────────────────────────────────────────────────────────
# Both paths read SonarQube's OWN evaluated gate via api/qualitygates/project_status
# (works with project-analysis tokens; no admin/browse permission required):
#   PR           -> new-code conditions, exactly as SonarQube reports them
#                   (Clean as You Code).
#   Merge/branch -> the gate's OVERALL (whole-codebase) conditions; new-code
#                   conditions are filtered out so the message reflects the state
#                   of the entire codebase.
# Changing the gate in the SonarQube UI updates both messages automatically.

# Read SonarQube's OWN evaluated gate status via project_status.
# This works with project-analysis tokens (no admin/browse needed) and always
# reflects the gate currently assigned to the project in the SonarQube UI.
qg_params = {"projectKey": SONAR_PROJECT_KEY}
if IS_PR and PR_NUMBER:
    qg_params["pullRequest"] = PR_NUMBER
elif CURRENT_BRANCH:
    qg_params["branch"] = CURRENT_BRANCH

qg_res = requests.get(f"{SONAR_HOST_URL}/api/qualitygates/project_status", auth=(SONAR_TOKEN, ""), params=qg_params)
if qg_res.status_code == 404 and "branch" in qg_params:
    del qg_params["branch"]
    qg_res = requests.get(f"{SONAR_HOST_URL}/api/qualitygates/project_status", auth=(SONAR_TOKEN, ""), params=qg_params)

qg_data = (safe_json(qg_res) or {}).get("projectStatus", {}) if qg_res.status_code == 200 else {}
all_conditions = qg_data.get("conditions", [])

if IS_PR:
    # PR: show the new-code gate exactly as SonarQube evaluated it (Clean as You Code).
    qg_conditions = all_conditions
    qg_status = qg_data.get("status", "UNKNOWN")
else:
    # Merge/branch: whole-codebase verdict = the gate's OVERALL (non new_) conditions,
    # evaluated by SonarQube on the full branch. New-code conditions are filtered out
    # so the message reflects the state of the entire codebase.
    qg_conditions = [c for c in all_conditions if not c.get("metricKey", "").startswith("new_")]
    # Derive the verdict from the overall conditions we actually display, so the
    # headline always agrees with the rows underneath it.
    # EXCEPTION: if the gate has no overall conditions at all (e.g. it was edited
    # to be new-code-only), there is nothing to derive from -- an empty any() would
    # silently report "Passed" while SonarQube is failing the gate. Defer to the
    # server's own verdict in that case instead of inventing one.
    if qg_conditions:
        qg_status = "ERROR" if any(c.get("status") == "ERROR" for c in qg_conditions) else "OK"
    else:
        qg_status = qg_data.get("status", "UNKNOWN")

failed_conditions = [c for c in qg_conditions if c.get("status") == "ERROR"]

# ─── Gate conditions SonarQube did not evaluate ──────────────────────────────
# project_status only returns a condition when its metric actually has a measure.
# A PR that changed no analyzable code has no new-code duplication figure, so
# `new_duplicated_lines_density` is omitted; a project with zero hotspots has no
# "% reviewed" figure, so `*_security_hotspots_reviewed` is omitted (on branch
# runs too). Those conditions are still part of the gate, so read the gate
# definition and list them instead of letting them silently vanish from the block.
def fetch_gate_name():
    """Name of the quality gate assigned to this project.

    Layered on purpose: a CI-scoped analysis token gets 403 on
    api/qualitygates/get_by_project AND on api/navigation/component, so the exact
    assignment is unreadable from CI. api/qualitygates/list IS readable by such a
    token, and every project on this instance inherits the instance default.
    """
    override = os.getenv("SONAR_QUALITY_GATE", "").strip()
    if override:
        return override
    try:
        r = requests.get(f"{SONAR_HOST_URL}/api/qualitygates/get_by_project",
                         auth=(SONAR_TOKEN, ""), params={"project": SONAR_PROJECT_KEY})
        if r.status_code == 200:
            name = (r.json().get("qualityGate") or {}).get("name")
            if name:
                return name
        r = requests.get(f"{SONAR_HOST_URL}/api/qualitygates/list", auth=(SONAR_TOKEN, ""))
        if r.status_code == 200:
            for g in r.json().get("qualitygates", []):
                if g.get("isDefault"):
                    return g.get("name") or ""
    except requests.RequestException:
        pass
    return ""

def fetch_gate_conditions():
    """Every condition configured on the gate. [] on any failure, which simply
    degrades to showing only what SonarQube evaluated."""
    name = fetch_gate_name()
    if not name:
        return []
    try:
        r = requests.get(f"{SONAR_HOST_URL}/api/qualitygates/show",
                         auth=(SONAR_TOKEN, ""), params={"name": name})
        if r.status_code == 200:
            return r.json().get("conditions", [])
    except requests.RequestException:
        pass
    return []

def passing_value(metric, comparator, threshold):
    """A value that satisfies this condition, for a metric that has no measure."""
    measured = data.get(metric)
    if measured not in (None, ""):
        return measured
    # GT fails ABOVE its threshold and LT fails BELOW it, so the threshold itself
    # always passes. Ratings reuse that (GT 1 -> "1" -> renders as A).
    if comparator == "LT" or "rating" in metric:
        return threshold
    return "0"

# Display list only. qg_status and failed_conditions above stay derived purely
# from what SonarQube evaluated, so an unmeasured row can never flip the verdict
# or inflate the "N conditions not met" count.
qg_display_conditions = list(qg_conditions)
_evaluated_metrics = {c.get("metricKey") for c in qg_conditions}
unmeasured_metrics = []
for _g in fetch_gate_conditions():
    _m = _g.get("metric", "")
    if not _m or _m in _evaluated_metrics:
        continue
    # Keep each message in its own scope: new-code conditions on PRs, overall
    # conditions on branch/merge runs — same split applied to evaluated ones.
    if _m.startswith("new_") != IS_PR:
        continue
    _op, _err = _g.get("op", ""), _g.get("error", "")
    qg_display_conditions.append({
        "metricKey": _m,
        "comparator": _op,
        "errorThreshold": _err,
        "actualValue": passing_value(_m, _op, _err),
        "status": "OK",
    })
    unmeasured_metrics.append(_m)

# ─── Dynamic metric display names (fallback for any gate metric) ─────────────
def fetch_metric_names():
    names = {}
    try:
        p = 1
        while True:
            r = requests.get(f"{SONAR_HOST_URL}/api/metrics/search", auth=(SONAR_TOKEN, ""), params={"ps": 500, "p": p})
            if r.status_code != 200:
                break
            j = r.json()
            for m in j.get("metrics", []):
                if m.get("key") and m.get("name"):
                    names[m["key"]] = m["name"]
            if p * 500 >= j.get("total", 0):
                break
            p += 1
    except requests.RequestException:
        pass
    return names

METRIC_NAMES = fetch_metric_names()

# ─── Helpers ─────────────────────────────────────────────────────────────────
def to_grade(val):
    val_str = str(val).strip()
    if val_str in ("1", "1.0", "A"): return "A"
    if val_str in ("2", "2.0", "B"): return "B"
    if val_str in ("3", "3.0", "C"): return "C"
    if val_str in ("4", "4.0", "D"): return "D"
    if val_str in ("5", "5.0", "E"): return "E"
    return "—"

def format_debt(minutes_str):
    try:
        minutes = int(float(minutes_str))
        if minutes < 60:
            return f"{minutes}min"
        hours = minutes // 60
        remaining = minutes % 60
        if hours < 24:
            return f"{hours}h {remaining}min" if remaining else f"{hours}h"
        days = hours // 8
        remaining_hours = hours % 8
        return f"{days}d {remaining_hours}h" if remaining_hours else f"{days}d"
    except (ValueError, TypeError):
        return "0min"

def is_debt_metric(metric_key):
    """True for gate metrics whose raw value is a duration in MINUTES.

    Covers sqale_index / new_technical_debt / *_remediation_effort /
    effort_to_reach_*. Deliberately excludes *_debt_ratio, which is a percentage
    and must not be run through format_debt.
    """
    k = str(metric_key)
    if "ratio" in k:
        return False
    return (
        k in ("sqale_index", "new_sqale_index", "new_technical_debt", "development_cost")
        or "remediation_effort" in k
        or k.startswith("effort_to_reach")
    )

def is_percent_metric(metric_key):
    """True for gate metrics expressed as a percentage (0-100)."""
    k = str(metric_key)
    return (
        "density" in k
        or "coverage" in k
        or "reviewed" in k
        or "ratio" in k
        or k.endswith("_percent")
    )

def format_ncloc(val):
    try:
        n = int(val)
        if n >= 1000:
            return f"{n/1000:.1f}k"
        return str(n)
    except (ValueError, TypeError):
        return val

def condition_name(metric_key):
    names = {
        "new_violations": "Issues (Total Violations)",
        "new_reliability_rating": "Reliability Rating (Bugs)",
        "new_security_rating": "Security Rating (Vulnerabilities)",
        "new_maintainability_rating": "Maintainability Rating (Code Smells)",
        "new_duplicated_lines_density": "Duplicated Lines (Density)",
        "new_security_hotspots_reviewed": "Security Hotspots (Reviewed)",
        "new_bugs": "Bugs (Overall)",
        "new_vulnerabilities": "Vulnerabilities (Overall)",
        "new_code_smells": "Code Smells (Maintainability)",
        "violations": "Issues (Total Violations)",
        "reliability_rating": "Reliability Rating (Bugs)",
        "security_rating": "Security Rating (Vulnerabilities)",
        "maintainability_rating": "Maintainability Rating (Code Smells)",
        "sqale_rating": "Maintainability Rating (Code Smells)",
        "duplicated_lines_density": "Duplicated Lines (Density)",
        "security_hotspots_reviewed": "Security Hotspots (Reviewed)",
        "bugs": "Bugs (Overall)",
        "vulnerabilities": "Vulnerabilities (Overall)",
        "code_smells": "Code Smells (Maintainability)",
    }
    if metric_key in names:
        return names[metric_key]
    # Fall back to SonarQube's own metric name, then a prettified key.
    # Coverage is deliberately NOT hardcoded above so it renders with SonarQube's
    # own name ("Coverage on New Code" / "Coverage").
    return METRIC_NAMES.get(metric_key, metric_key.replace("_", " ").title())

# ─── Deterministic display order for quality gate conditions ─────────────────
# The order is DERIVED FROM THE METRIC KEY, never from a hardcoded list of
# conditions. So if the quality gate is edited in the SonarQube UI (conditions
# added, removed, or re-thresholded) this message re-sorts itself automatically
# with no code change -- including metrics this script has never seen before.
#
# Family priority: Security -> Reliability -> Maintainability -> Duplications
#                  -> Coverage -> anything else.
# Within one family: issue/violation counts, then ratings, then %-style measures.
_FAMILY_ORDER = (
    ("security",        0),
    ("reliability",     1),
    ("maintainability", 2),
    ("sqale",           2),   # sqale_rating = legacy maintainability rating
    ("duplicat",        3),
    ("coverage",        4),
)

def condition_sort_key(metric_key):
    key = (metric_key or "").lower()
    family = len(_FAMILY_ORDER)  # unknown metrics sort last, alphabetically
    for token, rank in _FAMILY_ORDER:
        if token in key:
            family = rank
            break
    if "issues" in key or "violations" in key:
        within = 0
    elif "rating" in key:
        within = 1
    else:
        within = 2
    return (family, within, condition_name(metric_key))

def condition_comparator_text(comparator, threshold, metric_key):
    is_rating = "rating" in metric_key
    if is_rating:
        threshold_grade = to_grade(threshold)
        # GT n fails only when the rating is WORSE than n, so n itself still passes:
        # GT 2 => A and B both pass => "≤ B" (never "better than B").
        if comparator == "GT":
            return "= A" if threshold_grade == "A" else f"≤ {threshold_grade}"
        return f"≤ {threshold_grade}"

    if is_debt_metric(metric_key):
        if comparator == "GT":
            return "= 0" if str(threshold).strip() in ("0", "0.0") else f"≤ {format_debt(threshold)}"
        return f"≥ {format_debt(threshold)}"

    if is_percent_metric(metric_key):
        if comparator == "GT":
            return f"< {threshold}%"
        if comparator == "LT":
            return f"≥ {threshold}%"
        return f"≥ {threshold}%"
    
    if comparator == "GT":
        return f"= {threshold}" if threshold == "0" else f"≤ {threshold}"
    return f"≥ {threshold}"

def format_condition_value(value, metric_key):
    is_rating = "rating" in metric_key
    if is_rating:
        return to_grade(value)
    if is_debt_metric(metric_key):
        return format_debt(value)
    # Keep this percent test in sync with condition_comparator_text below, or the
    # "actual" cell loses the % that the "required" cell shows.
    if is_percent_metric(metric_key):
        try:
            return f"{float(value):.1f}%"
        except (ValueError, TypeError):
            return f"{value}%"
    return value

# ─── URLs ────────────────────────────────────────────────────────────────────
if IS_PR and PR_NUMBER:
    dashboard_url = f"{SONAR_HOST_URL}/dashboard?id={SONAR_PROJECT_KEY}&pullRequest={PR_NUMBER}"
else:
    dashboard_url = f"{SONAR_HOST_URL}/dashboard?id={SONAR_PROJECT_KEY}&codeScope=overall"

# ─── Extract values ──────────────────────────────────────────────────────────
ncloc = format_ncloc(data.get('ncloc', '0'))
tech_debt = format_debt(data.get('sqale_index', '0'))
new_tech_debt = format_debt(data.get('new_software_quality_maintainability_remediation_effort', '0'))

def format_pct(val):
    try:
        return f"{float(val):.1f}%"
    except:
        return f"{val}%"

def get_severity_label(quality, count):
    # Show the MQR severity breakdown (only non-zero buckets), e.g.
    #   1234 (🔴97 🟠362 🟡775)
    # Icons: 🔴 Blocker · 🟠 High · 🟡 Medium · 🔵 Low · ⚪ Info
    if count == 0:
        return ""
    s = dist_map.get(quality, {})
    parts = []
    for sev, icon in (("BLOCKER", "🔴"), ("HIGH", "🟠"), ("MEDIUM", "🟡"),
                      ("LOW", "🔵"), ("INFO", "⚪")):
        n = s.get(sev, 0)
        if n:
            parts.append(f"{icon}{n}")
    return f" ({' '.join(parts)})" if parts else ""

if IS_PR:
    new_lines     = data.get('new_lines', '0')
    new_bugs      = int(float(data.get('new_software_quality_reliability_issues', '0')))
    new_vulns     = int(float(data.get('new_software_quality_security_issues', '0')))
    new_hotspots  = int(float(data.get('new_security_hotspots', '0')))
    new_smells    = int(float(data.get('new_software_quality_maintainability_issues', '0')))
    new_dups      = format_pct(data.get('new_duplicated_lines_density', '0.0'))

    header_text = f"On Pull Request : From `{PR_BRANCH}` To `{MERGE_BRANCH}` by `@{GITHUB_ACTOR}`"
    if NO_ANALYZED_CHANGES:
        # Zeros below are real, not a failed lookup — state it plainly so the TL
        # can merge with confidence instead of suspecting a broken pipeline.
        lines_scanned_text = (
            f"No new lines of code to analyse in `{PR_BRANCH}` — this PR changes no files "
            f"inside the SonarQube analysis scope, so there is no new code to report on."
        )
    else:
        lines_scanned_text = f"Total `{new_lines}` New Lines of Code Scanned in `{PR_BRANCH}`"
    has_issues = (qg_status in ("ERROR", "WARN") or new_bugs > 0 or new_vulns > 0 or new_hotspots > 0 or new_smells > 0)
    display_tech_debt = new_tech_debt
    tech_debt_label = "New Tech Debt"
else:
    new_lines     = ncloc
    new_bugs      = int(float(data.get('software_quality_reliability_issues', '0')))
    new_vulns     = int(float(data.get('software_quality_security_issues', '0')))
    new_hotspots  = int(float(data.get('security_hotspots', '0')))
    new_smells    = int(float(data.get('software_quality_maintainability_issues', '0')))
    new_dups      = format_pct(data.get('duplicated_lines_density', '0.0'))

    header_text = f"On Code Merge : With `{MERGE_BRANCH}` by `@{GITHUB_ACTOR}`"
    lines_scanned_text = f"Total `{ncloc}` Lines of Code Scanned in `{MERGE_BRANCH}`"
    has_issues = (qg_status in ("ERROR", "WARN") or new_bugs > 0 or new_vulns > 0 or new_hotspots > 0 or new_smells > 0)
    display_tech_debt = tech_debt
    tech_debt_label = "Tech Debt"

if qg_status == "OK":
    qg_status_text = "✅ Passed"
elif qg_status == "ERROR":
    n = len(failed_conditions)
    qg_status_text = f"❌ Failed ({n} condition{'s' if n != 1 else ''} not met)"
elif qg_status == "WARN":
    qg_status_text = "⚠️ Warning"
else:
    qg_status_text = qg_status

# When we never confirmed the analysis, every number below is unverified. Say so
# above the table rather than letting a wall of zeros read as a clean bill of health.
if MEASURES_STALE:
    _stale_scope = f"PR #{PR_NUMBER}" if IS_PR and PR_NUMBER else f"`{CURRENT_BRANCH}`"
    stale_banner = (
        f"\n⚠️ **Unverified report** — SonarQube did not confirm the analysis for "
        f"{_stale_scope} in time, so the numbers below could not be read back and may be "
        f"incomplete. Check the dashboard link before relying on this report.\n"
    )
else:
    stale_banner = ""

# Build Markdown Output
# Sort by the key-derived priority so the block order stays stable and meaningful
# no matter what order SonarQube returns the conditions in.
qg_display_conditions = sorted(qg_display_conditions, key=lambda c: condition_sort_key(c.get("metricKey", "")))

qg_rows_text = ""
for c in qg_display_conditions:
    metric = c.get("metricKey", "")
    actual = c.get("actualValue", "—")
    threshold = c.get("errorThreshold", "")
    comparator = c.get("comparator", "")
    name = condition_name(metric)
    actual_fmt = format_condition_value(actual, metric)
    required = f"(required {condition_comparator_text(comparator, threshold, metric)})"
    status_icon = "❌" if c.get("status") == "ERROR" else "✅"
    qg_rows_text += f"    {status_icon} {name:<40} : {actual_fmt:<7} {required}\n"

# NOTE: the block above is the gate's own condition list — SonarQube's evaluated
# values where it has them, the gate definition where it does not. Nothing is
# invented: add or remove a condition in the UI and this block follows, with no
# code change. Coverage is no longer on the gate, so it no longer appears here.

bug_count = f"{new_bugs}{get_severity_label('RELIABILITY', new_bugs)}"
vuln_count = f"{new_vulns}{get_severity_label('SECURITY', new_vulns)}"
smell_count = f"{new_smells}{get_severity_label('MAINTAINABILITY', new_smells)}"

report_content = f"""`{SONAR_PROJECT_KEY}`: Code Analysis Report

{header_text}
{stale_banner}
{lines_scanned_text}

```
Type                                     | Count
-----------------------------------------|------
{'Security (Vulnerabilities)':<40} | {vuln_count}
{'Security Hotspots':<40} | {new_hotspots}
{'Reliability (Bugs)':<40} | {bug_count}
{'Maintainability (Code Smells)':<40} | {smell_count}
{'Duplications':<40} | {new_dups}
{tech_debt_label:<40} | {display_tech_debt}
```

Code Quality Gate: {qg_status_text}

📋 Quality Gate Conditions:

```
{qg_rows_text.rstrip()}
```

🔗 [View Full Report on SonarQube]({dashboard_url})
"""

# PR Markdown block (wrapped in single backticks so it renders properly in GH, but using the exact format requested)
pr_output = report_content

# Slack format block. Built whenever there is something to act on — issues, a
# failed/warned gate, or an unverified (stale) report. A genuinely clean run
# produces no Slack message at all.
if not (has_issues or MEASURES_STALE):
    slack_output = ""
else:
    slack_output = f"""*`{SONAR_PROJECT_KEY}`: Code Analysis Report*

{header_text}
{stale_banner.replace('**', '*')}
*{lines_scanned_text}*

```
Type                                     | Count
-----------------------------------------|------
{'Security (Vulnerabilities)':<40} | {vuln_count}
{'Security Hotspots':<40} | {new_hotspots}
{'Reliability (Bugs)':<40} | {bug_count}
{'Maintainability (Code Smells)':<40} | {smell_count}
{'Duplications':<40} | {new_dups}
{tech_debt_label:<40} | {display_tech_debt}
```

*Code Quality Gate: {qg_status_text}*

*📋 Quality Gate Conditions:*

```
{qg_rows_text.rstrip()}
```

🔗 <{dashboard_url}|*View Full Report on SonarQube*>"""

# ─── Write to files ───────────────────────────────────────────────────────────
with open("pr-output.txt", "w") as f:
    f.write(pr_output.strip())

# Slack is the "someone must act" channel: only notify when the gate failed or
# there are issues to fix. A clean run stays silent (empty file -> the workflow's
# Slack step skips it). The PR comment above is always written, in every
# condition, so the TL always has the report in front of them when merging.
with open("slack-output.txt", "w") as f:
    if has_issues or MEASURES_STALE:
        f.write(slack_output.strip())
    else:
        f.write("")

print(f"✅ PR comment generated (gate={qg_status}"
      f"{', no analyzed changes' if NO_ANALYZED_CHANGES else ''}"
      f"{', STALE MEASURES' if MEASURES_STALE else ''}). "
      f"Slack: {'sent' if (has_issues or MEASURES_STALE) else 'suppressed (clean run)'}.")
if unmeasured_metrics:
    # Not a warning: these are real gate conditions SonarQube had no measure for,
    # listed as passing. Printed so a surprising row in the block is traceable.
    print(f"   Listed from gate definition (unevaluated): {', '.join(unmeasured_metrics)}")