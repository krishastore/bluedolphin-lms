#!/usr/bin/env python3
"""
checkstyle-to-sonar.py — Convert a Checkstyle XML report (as produced by
PHP_CodeSniffer `--report=checkstyle` or Laravel Pint `--format=checkstyle`)
into the SonarQube Generic Issue Import Format.

WHY THIS EXISTS
---------------
SonarQube has NO native importer for PHP_CodeSniffer or Laravel Pint. The only
PHP external analyzers it understands are PHPStan (sonar.php.phpstan.reportPaths)
and Psalm (sonar.php.psalm.reportPaths). The property `sonar.php.phpcs.reportPaths`
does not exist and is silently ignored — so phpcs/pint findings never reach the
dashboard.

The Generic Issue Import Format (sonar.externalIssuesReportPaths) is the supported
way to push arbitrary linter findings in as *external* issues, tagged with an
engineId we choose ("phpcs" or "pint").

USAGE
-----
  # WordPress (phpcs)
  vendor/bin/phpcs --standard=phpcs.xml --report=checkstyle \
      --report-file=reports/phpcs-report.xml --extensions=php,inc || true
  python3 checkstyle-to-sonar.py reports/phpcs-report.xml reports/phpcs-sonar.json --engine phpcs

  # Laravel (pint)
  vendor/bin/pint --test --format=checkstyle > reports/pint-report.xml || true
  python3 checkstyle-to-sonar.py reports/pint-report.xml reports/pint-sonar.json --engine pint

Then in sonar-project.properties:
  sonar.externalIssuesReportPaths=reports/phpcs-sonar.json   # (or pint-sonar.json)
"""
import argparse
import json
import os
import sys
import xml.etree.ElementTree as ET

# checkstyle severity -> (legacy type, legacy severity, impact severity)
SEVERITY_MAP = {
    "error":   ("CODE_SMELL", "MAJOR", "MEDIUM"),
    "warning": ("CODE_SMELL", "MINOR", "LOW"),
    "info":    ("CODE_SMELL", "INFO", "LOW"),
}


def to_relative(path, base):
    """phpcs emits absolute runner paths; pint emits repo-relative paths.
    SonarQube wants paths relative to sonar.projectBaseDir (the repo root)."""
    path = path.replace("\\", "/")
    if os.path.isabs(path):
        try:
            rel = os.path.relpath(path, base)
            if not rel.startswith(".."):
                return rel.replace(os.sep, "/")
        except ValueError:
            pass
    return path


def convert(xml_path, base, engine):
    tree = ET.parse(xml_path)
    root = tree.getroot()
    rules = {}
    issues = []
    for file_el in root.iter("file"):
        rel_path = to_relative(file_el.get("name", ""), base)
        if not rel_path:
            continue
        for err in list(file_el):
            if err.tag not in ("error", "warning"):
                continue
            source = err.get("source") or f"{engine}.Unknown"
            sev = (err.get("severity") or "warning").lower()
            legacy_type, legacy_sev, impact_sev = SEVERITY_MAP.get(
                sev, SEVERITY_MAP["warning"])

            if source not in rules:
                rules[source] = {
                    "id": source,
                    "name": source,
                    "description": source,
                    "engineId": engine,
                    "cleanCodeAttribute": "CONVENTIONAL",
                    "type": legacy_type,
                    "severity": legacy_sev,
                    "impacts": [
                        {"softwareQuality": "MAINTAINABILITY",
                         "severity": impact_sev}
                    ],
                }

            try:
                line = max(int(err.get("line") or 1), 1)
            except ValueError:
                line = 1
            issues.append({
                "ruleId": source,
                "primaryLocation": {
                    "message": (err.get("message") or source).strip(),
                    "filePath": rel_path,
                    # startLine only — checkstyle columns are 1-based char
                    # positions that don't map to Sonar's 0-based offsets, and
                    # out-of-range columns cause the whole issue to be dropped.
                    "textRange": {"startLine": line},
                },
            })
    return {"rules": list(rules.values()), "issues": issues}


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("input", help="Checkstyle XML report")
    ap.add_argument("output", help="SonarQube generic issue JSON to write")
    ap.add_argument("--engine", default="phpcs",
                    help="engineId shown as the issue tag (phpcs | pint)")
    ap.add_argument("--base", default=os.getcwd(),
                    help="project base dir for relative paths (default: cwd)")
    args = ap.parse_args()

    empty = {"rules": [], "issues": []}
    if not os.path.exists(args.input) or os.path.getsize(args.input) == 0:
        json.dump(empty, open(args.output, "w"))
        print("checkstyle report missing/empty -> wrote empty generic report")
        return

    try:
        result = convert(args.input, args.base, args.engine)
    except ET.ParseError as e:
        print(f"WARNING: could not parse {args.input}: {e}", file=sys.stderr)
        json.dump(empty, open(args.output, "w"))
        return

    json.dump(result, open(args.output, "w"), indent=2)
    print(f"Converted {len(result['issues'])} {args.engine} issues "
          f"({len(result['rules'])} rules) -> {args.output}")


if __name__ == "__main__":
    main()
