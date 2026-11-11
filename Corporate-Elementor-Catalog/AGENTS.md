# Radman CT workspace

Scope: this WordPress project only. Do not import Django/RealEstate rules or dependencies.

## Product direction and naming boundary

- Shared brand/logo: **Radman**. This checkout's confirmed target is the separate Elementor edition with custom frontend prefix `rde-`; the independent custom PHP edition uses `rdc-`. Catalog/commerce wording does not belong in that technical prefix.
- This is a documentation-only direction: the current source still uses legacy `rdct-`, existing packages and optional Elementor/custom PHP contracts. Do not convert the architecture, rename source identifiers, migrate data or rebuild packages until explicitly requested. Existing user/concurrent changes remain untouched.
- Before that future naming task, read `radman-doc/engineering/naming.md`: use its fixed short vocabulary and change PHP/CSS/JS/editor preview/dataset/ARIA together. Preserve WordPress/Elementor identifiers, saved widget/control IDs, metadata, helpers and package identities; no runtime rename layer or duplicate CSS aliases. Documentation-only tasks run documentation checks only.

## Read the minimum

1. Read `radman-doc/product/essentials.md` once. Reuse known context.
2. If the owner is unknown, `rg` the topic in the single index `radman-doc/README.md`; read the matching owner heading only. Do not read the whole index by default. Do not preload docs, archives or reference themes.
3. Runtime/feasibility: `.agents/skills/radman-theme-work/SKILL.md`. Documentation: `.agents/skills/radman-documentation/SKILL.md`.

## Ownership and documentation

- Scenarios define behavior; engineering defines current technical contracts; skills define procedure. Keep CONFIRMED/PROPOSED/OPEN/DEFERRED distinct. Current user corrections override old text; discussion alone is not approval.
- Read the affected confirmed contract before implementation. Correct material decisions in their existing owner; link instead of copying. Explanatory docs belong in `radman-doc/`, not source comments/JSDoc. Preserve machine-readable headers, licenses and metadata; code identifiers/strings are English and Persian UI belongs in translations.
- Do not add routine task reports, completed-work lists or build/test logs to active docs. `radman-doc/product/roadmap.md` contains only remaining work; update/remove its existing item when the actual gap changes. `foundation.md` and archives are history, not startup reading. Technical inventories may stay detailed; open only the relevant section.

## Product and implementation boundaries

- Continued theme, header/footer and independent catalog/portfolio completion is authorized within confirmed scenarios. Do not repeatedly ask. Commerce, submission workflows, licensing, deployment and publishing are not implied; deferred work stays deferred.
- Follow `radman-doc/engineering/architecture.md`: PHP rendering, Vanilla frontend, React only in approved administration, optional Elementor and no Node runtime on hosting. Components is required by the theme even for custom PHP; Content and MCP are optional. Keep working Classic/V3 alongside Atomic and preserve stored IDs/data/public helpers; no automatic migration.
- Folder ownership: `radmanct/` owns site shell, settings, builders and archive integration; `radmanct-child/` owns customer designs only; `radmanct-components/` owns reusable frontend renderers, UI primitives, assets and PHP/Elementor adapters; `radmanct-content/` owns optional catalog/portfolio data and queries; `radmanct-mcp/` owns optional AI access. `shared/` is build-time source shared between independent packages, never a required hosting path. `themes/` contains read-only third-party references, not our theme source.
- Custom PHP, Classic/V3 and Atomic consume one component renderer/schema and its selected requirements. Elementor editor controls remain native to that editor; frontend Input/Select/ComboBox are a separate layer. Atomic needs its own typed props, template/preview and asset adapter; registering a Classic widget does not add Atomic support. General components must work without Child, Content, Elementor or the Radman theme; unavailable optional data must fall back safely.
- Keep a small shared base and load only consumed module/component/preset assets. Admin/editor assets stay separate from frontend. No empty modules, duplicate vendors, unnecessary global assets, new frameworks or external runtime sources. Follow `radman-doc/engineering/assets.md`, `radman-doc/scenarios/local-assets.md` and `radman-doc/engineering/markup-css.md` for asset ownership, semantic minimal DOM, Flex/Grid, logical RTL/LTR, intrinsic images and cleanup.
- Elementor controls/popups use the target editor's native UI first; custom exceptions need a documented missing capability in `radman-doc/engineering/components.md`. Preserve accessibility, responsive values and Global/Dynamic behavior. Check installed versions and supported APIs; do not infer Atomic parity from Classic references.
- Security owner: `radman-doc/scenarios/security-access.md`. Validate inputs and allowed public data, authorize writes with capability + nonce, escape output, and preserve password/private-content boundaries. Read its affected section for endpoints, storage or uploads; do not copy security checklists into every feature document.
- PHP must meet the agreed minimum and PHP 8.x/8.4 target; do not hide warnings/deprecations. Use Context7 for library/framework/API/CLI questions: resolve, then query the relevant concept. Pure refactoring/document organization needs no library lookup.
- Supplied `themes/` are read-only references excluded from product/Git. Before implementing a subsystem, inspect only its matching reference via `research/reference-map.md`; adapt behavior, not entire frameworks/assets.

## Local work and verification

- Preserve user assets and concurrent edits. Use the persistent `radman-local` installation per `radman-doc/engineering/local-development.md`; do not create another WordPress/DB by default or reset content/settings/volumes. Source is mounted directly; the owner handles later server transfer.
- All five source packages remain `1.1.1` until completion or explicit version instruction. Source builds may verify changes; keep content hashes. Do not bump versions or create/rebuild/relabel ZIPs unless packaging is explicitly requested.
- Use short relevant checks: PHP lint, source build and affected asset/contracts. No long suites, benchmarks or lengthy runtime checks unless requested; do not repeatedly ask. No Chrome/visual browser checks unless requested; prefer the built-in browser then, with no new browser MCP or full CDP by default.
- Compare delivered behavior with the current contract. Report actual checks and material limits briefly; build/schema/source inspection is not browser, editor, WPML, production or performance acceptance. Documentation-only work runs documentation checks only.
- Git is owned by the parent repository, remote `https://github.com/meysamradman/WordpressThemes.git`. Save only Corporate-Elementor-Catalog/ for a project request, through ../tools/Save-Project.ps1. No nested Git, mixed-project save, automatic push, workflows, Releases or deployment.

- Git ownership: this folder is part of the parent WordpressThemes repository (https://github.com/meysamradman/WordpressThemes.git). Follow ../AGENTS.md; normal saves stage/commit only this project path via ../tools/Save-Project.ps1. Never initialize nested Git or include sibling changes in a project save. No automatic push or Releases.
