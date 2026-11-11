# Radman Design Library

Independent local WordPress design workshop. Never modify Corporate-Webtalik data or its Docker project from here.

Runtime workflow: `.agents/skills/library-work/SKILL.md`. Read the relevant `docs/scenario.md` section; use `docs/architecture.md`, `docs/naming.md` and `docs/catalog.md` only as needed. These adapt the original theme's modularity, short naming and animation-preservation contracts to this independent workshop.

- Custom PHP theme `radman-library`, namespace `RadmanLibrary`, short DOM prefix `rdl-`. No Elementor, builder or settings framework required.
- Native `rdl_design` entries and `rdl_group` categories provide the searchable, paginated library. Every design has its own preview URL; do not execute all samples on the index.
- Each reusable design owns `modules/<slug>/view.php` and optional CSS/JS. Register trusted module paths explicitly in `inc/modules.php`; no paths supplied by users. Enqueue module files only on their matching preview.
- Module views must be usable independently of the library navigation. Keep editorial data separate from effects; escape output. No duplication of animation engines. Use local GSAP only when a sample actually needs it, with cleanup and reduced motion.
- Local fonts/assets only. Preserve asset licenses and provenance. No copying full customer databases, media libraries or entire themes into this workshop.
- Local Docker project `radman-design-library`, port 8097, dedicated database/network/volumes. Start/Stop preserve data. Never use `down -v` or reset without explicit request.
- Keep native wp_head/wp_body_open/wp_footer, semantic markup, keyboard access and responsive RTL/LTR. Admin writes require nonce/capability checks.
- Run short affected checks. No commit, publish or external tracking without request. Credentials stay in ignored local files.

Imported Webtalik samples follow `docs/catalog.md`. Preserve their existing `rdc-` / `RadmanCT` contract inside the scoped snapshot; the library shell uses `rdl-`. Do not rename one side of PHP/CSS/JS or rewrite animation timing during transfer. Shared source lives once in `shared/radmanct`; modules select their own CSS/JS entries. Default previews use the entire page, with the compact shared header and one scroll owner, never an iframe. Preserve source gutters and full-bleed media. Keep sample GSAP off the catalogue index, info and FAQ. Sample content/media belongs to the independent library database and its remapped attachment IDs; never reconnect a preview to 8095. Read source-snapshot provenance before explicit resync, preserving library edits.

- This is the permanent master catalogue for customer work: classify every sample by rdl_group / rdl_kind / rdl_fx. Preserve hero/carousel/marquee distinctions, native pagination/search/category tree and AJAX fallbacks. Independent modules have explicit dependency maps; selected customer builds omit workshop UI. Keep scenario/skill/docs concise; no long tests or repeated benchmarks. See docs/scenario.md.

Scope: keep existing home/services sections, but do not add full home/services pages or a kit composition without an explicit request. Current full-page work is archive/detail layouts. Do not expand examples into unrelated pages.

- Product catalog is native and independent: radman_product / inc/catalog.php with archive/detail modules. Reuse the selected source model, not Elementor, Content/Components plugins or a settings framework. Keep product metadata keys and public/private media boundaries; see docs/catalog-products.md.

- Browser logs, snapshots and screenshots under .playwright-mcp/ are temporary local artifacts; keep this directory ignored by Git and out of releases.

- Git ownership: this folder is part of the parent WordpressThemes repository (https://github.com/meysamradman/WordpressThemes.git). Follow ../AGENTS.md; normal saves stage/commit only this project path via ../tools/Save-Project.ps1. Never initialize nested Git or include sibling changes in a project save. No automatic push or Releases.
