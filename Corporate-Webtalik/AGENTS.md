# Radman CT custom corporate theme

Scope: Corporate-Webtalik only. Corporate-Catalog remains an independent product.

- This is the custom corporate/service/portfolio edition of **Radman CT** for client websites: theme folder/text domain `radmanct`, PHP namespace `RadmanCT`, DOM/CSS prefix `rdc-`. Read `docs/naming.md` before adding or renaming identifiers. Use short documented module names and readable parts; update PHP/CSS/JS together. Preserve legacy stored meta/options and the independent local Docker identity. Elementor and Corporate-Catalog dependencies must not be introduced.
- Support current WordPress on both MySQL and MariaDB through native WordPress data APIs; use the existing `$wpdb` only when necessary, with prepared queries and dynamic table names. No separate database connection, vendor-specific SQL, hardcoded table prefix or automatic database conversion. See `docs/wordpress.md`; distinguish source compatibility from actual engine-specific testing.

Runtime workflow: `.agents/skills/webtalik-work/SKILL.md`. Read `docs/architecture.md` first; `docs/remaining.md` only for unfinished work. Do not preload sibling projects or third-party reference themes.

- Standalone PHP theme in `radmanct/`; no Child, Elementor, theme settings panel, header/footer builder, product catalog or dependency on Radman Components.
- Header/footer, page layouts, UI primitives and effects are modular inside the theme. Reuse consumed frontend behavior; do not copy entire component registries, schemas, editors or unused presets.
- Posts and portfolio are permanent site features, with no module toggles or separate content plugin. WordPress owns posts; `radmanct/inc/content/` registers portfolio and shared card metadata. Preserve stored IDs/meta. Do not add products.
- Local Hafez for H1 only; Peyda for body and H2-H6; define palette/fonts once in tokens.css and shared tag typography in base.css. This site has no dark mode: fixed dark sections use rdc-inv; page styles only override actual design differences. Responsive logical CSS, intrinsic images, accessible semantics and reduced motion are required.
- Name files by role, not a repeated brand prefix. The project CPT is `project`; preserve stored IDs and meta keys when refactoring. Add effects only when requested; keep a single selected button behavior.
- Custom frontend identifiers use `rdc-` and the fixed short vocabulary in `docs/naming.md` (for example `tx`, `ttl`, `desc`, `lbl`, `itm`, `wrp`, `inr`, `scr`). Apply changes across PHP/CSS/JS/dataset/ARIA/tests together; no runtime mapping or legacy CSS aliases. Preserve WordPress/library names, dynamic renderer contracts, stored keys, folder/text domain and PHP namespace. `Radman` remains the brand; do not modify the separate Elementor checkout here.
- GSAP stays local and shared; load only selected effects on their consuming pages. No duplicate engines, global bundles of unused modules or Node on hosting. All runtime assets must be local: no Google Fonts, CDN, telemetry, external font/icon/vendor requests or remote runtime downloads.
- Keep only active source, required assets and tooling here. The separate original Corporate-Catalog is read-only reference when requested; never change its source or data from this project.
- Local Docker project `webtalik`, URL `127.0.0.1:8095`, dedicated database/network/volumes and credentials. Never use radman-local commands/volumes here or copy its database. Start/Stop preserve data; no reset by default.
- Preserve existing user edits, licenses, asset provenance and WordPress password/private-content protections. Escape output; capability + nonce for writes. Use short focused checks; no long benchmarks, new browser tools, releases or deployment without request.
- Git belongs to the parent WordpressThemes repository, remote https://github.com/meysamradman/WordpressThemes.git. Save only Corporate-Webtalik/ via ../tools/Save-Project.ps1 for a project request; no nested Git, mixed-project save, automatic push or Releases. No routine reports in docs; only rules and remaining work.
- Use Context7 resolve then query for library/API/config questions; plain refactoring needs no library lookup.

- Each custom page composes named sections under template-parts/sections/<page>/; keep PHP data/helpers in the page owner and load its CSS/JS only on consuming routes. Use one h1 for the page, h2 for main sections, h3 for service/card headings; decorative labels/counters use p/span. Content images come from WordPress attachment IDs with intrinsic dimensions/srcset, not copied theme files.

- Shared UI means one renderer/style/motion owner with explicit per-use options, not identical content/icons everywhere. CTA icons are selected through the shared button renderer; use the local icon catalog with the shared 1.5 stroke and mirror only directional symbols for RTL. Do not create per-page button or tooltip implementations.

- Keep reusable Radman CT behavior independent of customer identity. `inc/client.php` owns this project's editorial data through small native WordPress filters; preserve its current output when refactoring. New customers replace that data and the relevant scoped tokens/templates, without changing assets, GSAP or stored legacy keys. No client registry, settings framework, automatic migration or mandatory Child theme.
- Extract a shared module only for actual repeated use or a clear independent responsibility. Different customer markup may have its own template; do not force it into a large variant system. Add a transparent/solid header modifier only when both are needed, using the same markup and no extra JS. Preserve frontend asset count/bytes and short verification boundaries.

- Browser logs, snapshots and screenshots under .playwright-mcp/ are temporary local artifacts; keep this directory ignored by Git and out of releases.

- Git ownership: this folder is part of the parent WordpressThemes repository (https://github.com/meysamradman/WordpressThemes.git). Follow ../AGENTS.md; normal saves stage/commit only this project path via ../tools/Save-Project.ps1. Never initialize nested Git or include sibling changes in a project save. No automatic push or Releases.
