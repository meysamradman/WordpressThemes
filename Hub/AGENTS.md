# Hub local maintenance

Before modifying this distribution, read `docs/license-local-build.md`,
`docs/external-connections.md` and `update/README.md`.
For Hub maintenance also apply `.agents/skills/hub-maintenance/SKILL.md`.

- Current scope: remove Hub's active license/domain/revoke/visitor gates and fake
  activation writes. Keep GPL notices, author attribution and original assets.
- Preserve vendor update discovery and native update notices independently of
  licensing. Vendor version checks are allowed: cron/twicedaily, success cache
  12 hours, failure backoff 30 minutes; no update HTTP on frontend/badge reads.
- The user accepts that installing original vendor updates overwrites local fixes;
  they will put those releases in update/ to compare and reapply our changes.
- Preserve Hub Collections, its original API version, UI, catalog, import behavior,
  Elementor source registration, widget controls and native saved templates.
  The only library change is removal of the local purchase-status guard.
- Do not install a blanket HTTP/CSP/CDN blocker. External-connection optimization
  is a report until the user separately requests implementation.
- `hub-core` and `elementor` are unchanged upstream copies. Keep unrelated files intact.
- Future upstream releases are supplied in `update/incoming/` (or directly in
  `update/`). Compare first; preserve the supplied original, then reapply only
  applicable changes. Use the change manifest and focused diff in `docs/`.
- Use bounded PHP and Docker checks. Do not run long test suites or open a browser
  unless requested. Distinguish mocked library API checks from live vendor proof.
- Docker project is `hub`, exposed only on `localhost:8096`; other Docker projects
  and their databases must not be modified. Credentials are in `.env`, never in ZIPs.
- Docker now runs writable copies inside `hub_hub_wordpress`, without Windows bind
  mounts. Host source edits do not automatically reach the running copy. Preserve
  user updates in Docker; do not overwrite them during a source synchronization.
- Run WordPress PHP smoke scripts as `www-data`, not root. Keep uploads owned by
  www-data with directories 755 and files 644; never use chmod 777.
- Maintenance `update/` and `docs/` stay outside installation packages and product UI.

- Git ownership: this folder is part of the parent WordpressThemes repository (https://github.com/meysamradman/WordpressThemes.git). Follow ../AGENTS.md; normal saves stage/commit only this project path via ../tools/Save-Project.ps1. Never initialize nested Git or include sibling changes in a project save. No automatic push or Releases.
