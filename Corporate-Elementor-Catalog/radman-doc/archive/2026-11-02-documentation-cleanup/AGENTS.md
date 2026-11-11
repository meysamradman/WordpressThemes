> آرشیو تاریخی پیش از جمع‌وجورکردن اسناد؛ مرجع قواعد جاری نیست. لینک‌ها به محل اصلی مقصدها اشاره می‌کنند.

# Radman CT project instructions

Scope: this WordPress Radman CT workspace only. RealEstate was a structure reference; do not import its Django rules, shared-product authorization or runtime dependencies.

## Read only what the task needs

1. Read `radman-doc/product/essentials.md` once on initial orientation; reuse it during continuing discussion.
2. Follow the matching owner below. Use `radman-doc/README.md` only when the owner is unclear. Do not preload all docs, skills, reference themes or archives.

| Concern | Canonical owner |
|---|---|
| Asset folders, images/fonts/source/dist and module ownership | `radman-doc/engineering/assets.md` |
| Modern CSS, semantic/minimal DOM, image sizing, Flex/Grid and scoped style review | `radman-doc/engineering/markup-css.md` |
| Product identity, short prefixes and legacy naming compatibility | `radman-doc/engineering/naming.md` |
| Classic/Atomic container animation and independent PHP motion | `radman-doc/engineering/container-motion.md` |
| PHP-rendered pages, Gutenberg, optional Elementor and custom designs | `radman-doc/scenarios/pages.md` |
| Header/footer builder | `radman-doc/scenarios/header-footer.md` |
| Initial builder schema, storage, rendering and limits | `radman-doc/engineering/builder.md` |
| Mobile sidebar, device-specific menus and navigation lifecycle | `radman-doc/engineering/navigation.md` |
| Optional Elementor integration, supplied versions and runtime evidence | `radman-doc/engineering/elementor.md` |
| Reusable blocks, builder navigation and WPML editing | `radman-doc/scenarios/reusable-blocks.md` |
| Optional scroll motion and smooth scrolling | `radman-doc/scenarios/motion.md` |
| Shared PHP/Elementor components and RTL text effects | `radman-doc/scenarios/components.md` |
| Shared PHP/Elementor component renderers and asset lifecycle | `radman-doc/engineering/components.md` |
| Text effects, native controls, rotating words and Hub parity gaps | `radman-doc/engineering/text.md` |
| Shared animated hover border on widgets, containers and PHP wrappers | `radman-doc/engineering/component-effects.md` |
| Shared carousel, arbitrary template content, item height and RTL controls | `radman-doc/engineering/carousel.md` |
| Sticky service panels, stacking/zooming cards and fixed/scrolling columns | `radman-doc/engineering/sticky-sections.md` |
| Shared Particles, Hub field coverage, canvas lifecycle and native editor controls | `radman-doc/engineering/particles.md` |
| Elementor field appearance, native-control priority and custom-control exceptions | `radman-doc/scenarios/components.md#elementor-native-controls`, `radman-doc/engineering/components.md` |
| Local SVG icons, generator and custom icon extension | `radman-doc/engineering/icons.md` |
| Site logo/favicon, responsive builder images and optional sanitized SVG uploads | `radman-doc/engineering/media.md` |
| Cursor, progress bar and preloader implementation | `radman-doc/engineering/effects.md` |
| Native shared-pattern storage and rendering | `radman-doc/engineering/content-blocks.md` |
| Optional future Claude/Codex MCP access | `radman-doc/scenarios/ai-mcp.md` |
| Optional MCP abilities, content/layout writes and Elementor ownership | `radman-doc/engineering/mcp.md` |
| Blog, catalog, portfolio | `radman-doc/scenarios/content.md` |
| Native widget areas, custom content sidebars and per-view sidebar settings | `radman-doc/engineering/sidebars.md` |
| Custom blog/catalog AJAX filtering, search, pagination and History lifecycle | `radman-doc/engineering/archive-ajax.md` |
| Core settings, module cards, bounded inquiry destination search and admin assets | `radman-doc/engineering/core-admin.md` |
| Optional Core modules, metadata, catalog query and portfolio | `radman-doc/engineering/content-modules.md` |
| Custom/Elementor Pro selection for content single/archive | `radman-doc/engineering/renderers.md` |
| RTL/LTR, WPML | `radman-doc/scenarios/languages.md` |
| Fonts and Elementor font ownership | `radman-doc/scenarios/typography.md` |
| Font registry, supplied metadata and local file paths | `radman-doc/engineering/typography.md` |
| Settings panel and supplied screenshot | `radman-doc/scenarios/settings.md` |
| Global/content/header/footer widths, spacing inheritance and Elementor boundaries | `radman-doc/scenarios/layout-spacing.md` |
| Layout schema, runtime ownership, controls and focused evidence | `radman-doc/engineering/layout.md` |
| Component gallery, isolated previews and Elementor sample insertion | `radman-doc/engineering/component-library.md` |
| Shared admin controls, palette, minimal markup and RTL/LTR styling | `radman-doc/engineering/admin-ui.md` |
| Optional plugin cards, native installation and bundled Core ZIP | `radman-doc/engineering/plugin-installer.md` |
| CSS variants and plugin compatibility | `radman-doc/scenarios/compatibility.md` |
| Modular JS, GSAP and no external runtime connections | `radman-doc/scenarios/local-assets.md` |
| Security and no public registration/account features | `radman-doc/scenarios/security-access.md` |
| Final technology decisions and remaining technical details | `radman-doc/engineering/architecture.md` |
| Client-specific styles, templates and child theme | `radman-doc/engineering/client-customization.md` |
| Current foundation files, build and actual verification | `radman-doc/implementation/foundation.md` |
| Shared persistent local WordPress, Docker start/stop, credentials and source mounts | `radman-doc/engineering/local-development.md` |
| Future phases and validation | `radman-doc/product/roadmap.md` |
| Documentation/agent maintenance | `.agents/skills/radman-documentation/SKILL.md` |
| Authorized theme work or technical feasibility | `.agents/skills/radman-theme-work/SKILL.md` |
| Fast lookup of supplied theme/plugin implementation references | `radman-doc/research/reference-map.md` |

## Working rules

- The user authorized continued theme completion, header/footer work and the independent catalog/portfolio modules. Continue within those confirmed scenarios without repeated permission stops. Unspecified inquiry submission workflows, commerce, deployment and publishing are not implied.
- Scenarios own product behavior; engineering owns technical choices; skills own repeatable workflow. Preserve CONFIRMED/PROPOSED/OPEN/DEFERRED status. A discussion is not implementation approval.
- The final architecture in `radman-doc/engineering/architecture.md` supersedes earlier React-frontend/no-Core decisions. Follow its confirmed stack and Theme/Core boundary; only explicitly open details remain undecided. Do not revive excluded commerce or Node runtime requirements from history.
- User corrections override older drafts. Keep one canonical owner per requirement; link instead of copying. Archive is historical evidence, never current authority.
- Before implementation, read the affected owner's confirmed requirements and current gaps. Record important user decisions/corrections in that owner as part of the same task, even without a separate documentation request; update contradictory current wording instead of appending a competing rule. Update routing/skills only when needed for discovery or repeatable procedure. Before reporting completion, compare the delivered behavior with those requirements and record unmet items explicitly; a build or document edit does not prove UI/runtime acceptance. Keep this check brief and scoped to changed work.
- Search affected headings with `rg`, then read the relevant section. Keep tool output bounded; do not rescan unchanged context after every message.
- Use Context7 for library/framework/API/CLI documentation: resolve the library first, then query the specific concept. It is unnecessary for pure document organization, recording user scenarios or general programming reasoning. Verify current manifests before version-dependent work.
- `radmanct/` is the theme, `radmanct-child/` the optional child theme, `radmanct-content/` the optional content plugin, `radmanct-mcp/` the optional MCP plugin, and `radmanct-components/` the optional shared PHP/Classic/Atomic component plugin. Supplied folders under `themes/` are read-only references. Before implementing a subsystem, inspect its matching reference using `radman-doc/research/reference-map.md`; adapt useful patterns without importing whole third-party frameworks/source/assets. Do not repeatedly rescan unrelated references.
- Do not open Chrome or perform browser visual checks unless requested. Supplied local images may be inspected with the image viewer.
- Keep changes local, preserve user assets/concurrent edits, and report actual checks concisely. Do not invent build commands, successful runtime checks or performance results.
- For ordinary WordPress runtime work use the shared persistent `radman-local` environment in `radman-doc/engineering/local-development.md`; do not create another temporary WordPress/DB installation by default or reset this site's user content/settings. Start is idempotent and source edits are mounted directly.
- During ongoing development, do not bump package versions or create/rebuild ZIPs. Prepare versioned installable packages only when the user explicitly requests preparation; a focused source build is allowed for verifying code changes.
- Owner correction 2026-10-28: all five Radman source packages stay at version `1.1.1` until theme completion or another explicit version instruction. Work and site construction happen together on the shared local WordPress; the owner will transfer the site to the server later. Keep content-hashed builds; do not relabel old bundled ZIPs as the new source version.
- Use short, focused checks for changed behavior: build, asset contracts and PHP lint. Per the owner's 2026-10-27 correction, do not run long suites, performance scans or lengthy browser/runtime checks unless the owner explicitly requests that check. Do not repeatedly ask to run them. Keep requested browser work bounded to the reported behavior and report verification limits honestly.
- Git remote: `https://github.com/meysamradman/wp-radman.git`. No GitHub Actions workflows, automatic builds, releases or deployments. Current Git use is source/file backup via explicitly requested commits/pushes. Exclude bundled third-party reference themes from tracking; keep them locally.
- The user handles Git saves/publishing. Do not stage, commit or push automatically during discussion or edits; do so only when explicitly requested for that action. Read-only Git checks remain allowed.
- Keep admin CSS/JS separate from frontend assets and enqueue only on relevant screens/pages. Keep modules focused; avoid duplicate dependencies and unused global assets.
- Follow `radman-doc/engineering/markup-css.md` for HTML/PHP/React/CSS changes: justify wrappers and sizing, reuse Flex/Grid and gap, preserve native hooks/accessibility, and report focused DOM/CSS reductions without claiming unverified visual acceptance.
- Component and preset isolation is mandatory for existing and new work: keep a small shared base, independent feature/style assets, and load only what the current page uses. Follow `radman-doc/scenarios/local-assets.md` and the component modularity/folder contract in `radman-doc/engineering/assets.md`; verify the changed requirements/import graph before claiming completion. Do not create empty PHP/CSS/JS modules or duplicate engines for folder symmetry.
- PHP must target the confirmed PHP 8.x/8.4 compatibility requirement with clean WordPress conventions. Check the agreed minimum before newer syntax; do not hide warnings/deprecations to claim compatibility. Verify stable Elementor/Pro APIs and exact tested versions, not a bundled reference theme's assumptions.
- Preserve working V3/Classic alongside Atomic; a stable Atomic release does not imply complete capability coverage. Elementor widget controls and popups must use the editor's native UI first, with documented exceptions only for missing capabilities. Follow `radman-doc/scenarios/components.md#elementor-native-controls` and the technical inventory in `radman-doc/engineering/components.md`.
- Keep explanatory documentation in `radman-doc/`, not source folders or embedded explanatory docblocks/JSDoc. Technical identifiers/comments and source strings are English; Persian interface translations belong in translation resources. Retain required machine-readable headers, licenses and metadata.
