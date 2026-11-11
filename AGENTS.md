# WordpressThemes source archive

Repository root: this folder. Active remote: https://github.com/meysamradman/WordpressThemes.git.

- All immediate project folders belong to this single repository; never initialize a nested Git repository or restore the old project remotes. Project runtime, databases and architecture remain independent. Read the selected project's AGENTS/skill before implementation.
- Work on only the requested project. Preserve all other working-tree and staged changes. A normal save stages/commits only that project's path; never run a root-wide add/commit for a single-project request.
- Use `tools/Save-Project.ps1 -Project <folder> -Message <message>` for an explicitly authorized save. Add `-Push` only when the user authorizes publishing. The script refuses mixed staged changes and refuses a push containing unpublished commits for other projects. `-CheckOnly` is read-only.
- Git pushes commits, not folders. If other projects have unpublished commits, do not include them implicitly, force-push, reset, stash or rewrite history to hide them; report the condition.
- New project folders are ordinary subdirectories, covered by this repository. Add their source/docs with a path-scoped commit when requested; no submodules/gitlinks.
- Keep original author/license notices, source, docs, tests, package manifests/lockfiles, local runtime assets and consumed dist files. Ignore local dependencies, browser output, private environment/credentials, databases, archives, backups and unused reference distributions. Do not use a blanket vendor/dist exclusion that removes required hosting code.
- No Releases, CI deployment, automatic watcher/push or runtime synchronization is requested. Commit/push only on explicit user instructions. This archive operation does not deploy sites or change Docker data.
