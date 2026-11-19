---
name: wordpress-performance-review
description: Diagnose a requested WordPress performance, GTmetrix, asset-loading or cache issue on an authorized target, for custom PHP or Elementor sites.
---

1. Identify the target project and open its profile and the relevant owner in the [standards index](../../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md). Read [Performance and Cache](../../../WEB-DEVELOPMENT-STANDARDS/shared/09-PERFORMANCE-AND-CACHE.md) for the observed symptom.
2. Separate initial HTML waiting, LCP, load, late resource requests and repeat-visit cache. Read the waterfall or public response headers. Identify the affected asset and its owning route, widget or server rule before proposing a change.
3. For custom PHP, trace consumption from template to enqueue and manifest. For Elementor, distinguish frontend, preview iframe, editor panel and widget cache. Reuse the selected project workflow if implementation is requested.
4. Preserve the approved UI and behavior. Use comparable measurements when available, report the tested environment and avoid unsupported claims about production. Change live cache, server policy or deployment only within the user's authorized scope.

5. For images and media read [17](../../../WEB-DEVELOPMENT-STANDARDS/shared/17-IMAGES-AND-MEDIA-v1.0.md); for server/query cost read [18](../../../WEB-DEVELOPMENT-STANDARDS/shared/18-DATABASE-QUERY-OPTIMIZATION-v1.0.md). Use [19](../../../WEB-DEVELOPMENT-STANDARDS/shared/19-CODE-QUALITY-REFACTORING-v1.0.md) before deleting assets. Review only the needed owner. Report currentSrc, LCP element, initiator, bytes and tested URL/device where relevant. Do not treat documentation review as runtime performance evidence.
