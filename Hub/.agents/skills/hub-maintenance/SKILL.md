---
name: hub-maintenance
description: Maintain this Hub WordPress distribution, remove activation gates while preserving vendor update discovery and Hub Collections, and compare new vendor packages in update. Use only for this Hub project and its Docker environment.
---

# Hub maintenance

Read the project's `AGENTS.md` and `docs/license-local-build.md` before changing
the distribution. For a new vendor release also read `update/README.md`,
`docs/change-manifest.json` and `docs/license-changes.patch`.

The user's accepted boundaries:

- Remove activation, domain/revoke locks, visitor 503 gates and fabricated
  purchase/email option writes. Preserve GPL notices and attribution.
- Keep vendor update discovery, native WordPress notices and user-initiated
  installation. Removing licensing must not disable updates.
- The user accepts that an original vendor update replaces local fixes. They
  will put the new packages in `update/` for comparison and reapplication.
- Check vendor versions periodically, cache success for 12 hours and back off
  failures. Never perform update HTTP on every page view or badge read. Manual
  checking may contact the vendor from WordPress's dedicated Updates screen.
- Hub's updater is `hub/liquid/admin/updater/liquid-updater-class.php`. It uses
  the original vendor endpoint without a fabricated key. Keep its upstream
  comparison version separate from the local package suffix.
- Preserve native plugin checks and original TGMPA bundled-plugin checks.
  Bundled versions come from the theme's definitions; do not invent a remote
  per-plugin version feed or assume all plugins share the theme's version.
- Preserve Hub Collections API, API version, UI, source registration, controls
  and imports. Its sole local change removes the purchase-status guard. Do not
  replace its catalog or convert it into an offline library.
- Updates and the library are allowed external connections. Do not add global
  HTTP/CDN/CSP blockers. Report fonts/maps/feeds separately unless implementation
  is requested.
- Keep maintenance docs, this skill and `update/` outside installation ZIPs and
  out of product screens.

Compare each new release semantically against backed-up originals. Update the
manifest, diff, release notes and ZIPs after code changes; preserve unrelated work.

Docker is a writable copy in `hub_hub_wordpress`, distinct from host source.
Compare target hashes before synchronization; do not overwrite plugins the user
updated in Docker. Run WordPress PHP checks as `www-data`. Keep uploads writable
(directories 755, files 644), without chmod 777. Do not touch other Docker projects.

Use focused PHP/update-response checks and a short editor/library check when
requested. Distinguish mocks from live vendor responses, and catalog display
from importing all designs. Avoid long suites and unrequested browser checks.
