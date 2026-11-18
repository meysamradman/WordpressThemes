---
name: wordpress-performance-review
description: Diagnose a requested WordPress performance, GTmetrix, asset-loading or cache issue on an authorized target, for custom PHP or Elementor sites.
---

1. Identify the target project and open its profile and the relevant owner in the [standards index](../../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.2.md). Read [Performance and Cache](../../../WEB-DEVELOPMENT-STANDARDS/shared/09-PERFORMANCE-AND-CACHE.md) for the observed symptom.
2. Separate initial HTML waiting, LCP, load, late resource requests and repeat-visit cache. Read the waterfall or public response headers. Identify the affected asset and its owning route, widget or server rule before proposing a change.
3. For custom PHP, trace consumption from template to enqueue and manifest. For Elementor, distinguish frontend, preview iframe, editor panel and widget cache. Reuse the selected project workflow if implementation is requested.
4. Preserve the approved UI and behavior. Use comparable measurements when available, report the tested environment and avoid unsupported claims about production. Change live cache, server policy or deployment only within the user's authorized scope.
