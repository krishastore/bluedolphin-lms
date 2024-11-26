### Description

<!-- Provide a concise description of what this pull request addresses. -->

### Related Issues

<!-- Reference any related issues, to-do, e.g., "Closes #123" -->

### Checklist

**Code Quality**

- [ ] PHP, HTML, CSS/SCSS, and JavaScript code is properly indented and formatted.
- [ ] Properties, Methods and hooks should be descriptive.
- [ ] Proper comments and documentation are included for non-trivial code blocks.
- [ ] The code must follow the WordPress coding guideline.
- [ ] Error handling and validation are implemented where necessary.
- [ ] Logs are implemented where necessary.

**Performance**

- [ ] CSS and JS should be minified and must include a single file of CSS and JS.
- [ ] Image should be optimized and must restrict the use of SVG images.
- [ ] The code changes do not introduce performance bottlenecks.
- [ ] WP-Queries and operations are optimized for performance.

**Security**

- [ ] Input data is properly sanitized and validated to prevent security vulnerabilities.
- [ ] Sensitive data is handled securely.
- [ ] No direct database queries are made, instead, queries must be written in the WordPress standard.
- [ ] No Code deprecation and vulnerable packages.

**Other**

- [ ] Cross-browser compatibility and responsiveness are ensured.
- [ ] Any configuration changes or environment setup instructions are documented.

### Reviewer's Checklist

**Reviewers, please make sure to address the following:**

- [ ] Review the code changes according to the above checklist.
- [ ] Provide constructive feedback and suggestions for improvement.
- [ ] Approve the PR if everything is satisfactory, or request revisions if needed.
