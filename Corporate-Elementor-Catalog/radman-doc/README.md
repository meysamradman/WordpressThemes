# راهنمای مستندات رادمان

شروع: [AGENTS.md](../AGENTS.md) و یک‌بار [خلاصهٔ محصول](product/essentials.md). این فهرست فقط مسیر‌یاب است؛ سندها را کامل و پشت‌سرهم نخوانید. با `rg` عنوان مرتبط را پیدا کنید و همان بخش را بخوانید.

| محل | محتوای مجاز |
|---|---|
| `scenarios/` | رفتار و دامنهٔ مورد انتظار محصول |
| `engineering/` | قرارداد فنی جاری، API و ساختار لازم برای پیاده‌سازی |
| `.agents/skills/` | روش کار کوتاه و تکرارپذیر agent |
| `product/roadmap.md` | تنها فهرست کارها و پذیرش‌های باقی‌مانده |
| `research/` | مسیر و جزئیات مرجع؛ مطالعه فقط برای قابلیت مرتبط |
| `archive/` | تصمیم‌ها و گزارش‌های قدیمی؛ خارج از مسیر مطالعهٔ معمول و فاقد اولویت بر قرارداد جاری |

هر تصمیم یک مالک دارد. `CONFIRMED` تأییدشده، `PROPOSED` پیشنهادی، `OPEN` نیازمند تصمیم/کار، و `DEFERRED` موکول‌شده است. گزارش روزانه، لیست کارهای انجام‌شده و خروجی build/test به اسناد فعال اضافه نشود؛ نتیجهٔ بررسی در پاسخ همان کار کافی است.

## مالک هر موضوع

| موضوع | سند |
|---|---|
| Asset folders, images/fonts/source/dist and module ownership | [assets.md](engineering/assets.md) |
| Modern CSS, semantic/minimal DOM, image sizing, Flex/Grid and scoped style review | [markup-css.md](engineering/markup-css.md) |
| Product identity, short prefixes and legacy naming compatibility | [naming.md](engineering/naming.md) |
| Classic/Atomic container animation and independent PHP motion | [container-motion.md](engineering/container-motion.md) |
| PHP-rendered pages, Gutenberg, optional Elementor and custom designs | [pages.md](scenarios/pages.md) |
| Page title, per-page/view overrides, title typography and shared background gradients | [page-title.md](engineering/page-title.md) |
| Header/footer builder | [header-footer.md](scenarios/header-footer.md) |
| Initial builder schema, storage, rendering and limits | [builder.md](engineering/builder.md) |
| Mobile sidebar, device-specific menus and navigation lifecycle | [navigation.md](engineering/navigation.md) |
| Optional Elementor integration, supplied versions and runtime evidence | [elementor.md](engineering/elementor.md) |
| Reusable blocks, builder navigation and WPML editing | [reusable-blocks.md](scenarios/reusable-blocks.md) |
| Optional scroll motion and smooth scrolling | [motion.md](scenarios/motion.md) |
| Shared PHP/Elementor components and RTL text effects | [components.md](scenarios/components.md) |
| Shared PHP/Elementor component renderers and asset lifecycle | [components.md](engineering/components.md) |
| پیش‌نمایش ویدئویی اختیاری کارت‌های وبلاگ/محصول/نمونه‌کار، حالت هاور/دید و Shortcode المنتور | [موتور مشترک](engineering/components.md#پیش‌نمایش-ویدئویی-کارت) |
| کنترل‌های فرانت: Input/Select/ComboBox، درخت دسته و ویجت‌های Taxonomy/Search در PHP/Classic/Atomic | [کامپوننت‌ها](engineering/components.md#کنترلها-و-داده-مشترک) |
| Text effects, native controls, rotating words and Hub parity gaps | [text.md](engineering/text.md) |
| Shared animated hover border on widgets, containers and PHP wrappers | [component-effects.md](engineering/component-effects.md) |
| Shared carousel, arbitrary template content, item height and RTL controls | [carousel.md](engineering/carousel.md) |
| Sticky service panels, stacking/zooming cards and fixed/scrolling columns | [sticky-sections.md](engineering/sticky-sections.md) |
| Shared Particles, Hub field coverage, canvas lifecycle and native editor controls | [particles.md](engineering/particles.md) |
| Local SVG icons, generator and custom icon extension | [icons.md](engineering/icons.md) |
| Site logo/favicon, responsive builder images and optional sanitized SVG uploads | [media.md](engineering/media.md) |
| Cursor, progress bar and preloader implementation | [effects.md](engineering/effects.md) |
| Native shared-pattern storage and rendering | [content-blocks.md](engineering/content-blocks.md) |
| Optional future Claude/Codex MCP access | [ai-mcp.md](scenarios/ai-mcp.md) |
| Optional MCP abilities, content/layout writes and Elementor ownership | [mcp.md](engineering/mcp.md) |
| Blog, catalog, portfolio | [content.md](scenarios/content.md) |
| Native widget areas, custom content sidebars and per-view sidebar settings | [sidebars.md](engineering/sidebars.md) |
| Custom blog/catalog AJAX filtering, search, pagination and History lifecycle | [archive-ajax.md](engineering/archive-ajax.md) |
| Core settings, module cards, bounded inquiry destination search and admin assets | [core-admin.md](engineering/core-admin.md) |
| Optional Core modules, metadata, catalog query and portfolio | [content-modules.md](engineering/content-modules.md) |
| Custom/Elementor Pro selection for content single/archive | [renderers.md](engineering/renderers.md) |
| RTL/LTR, WPML | [languages.md](scenarios/languages.md) |
| Fonts and Elementor font ownership | [typography.md](scenarios/typography.md) |
| Font registry, supplied metadata and local file paths | [typography.md](engineering/typography.md) |
| Settings panel and supplied screenshot | [settings.md](scenarios/settings.md) |
| Global/content/header/footer widths, spacing inheritance and Elementor boundaries | [layout-spacing.md](scenarios/layout-spacing.md) |
| Layout schema, runtime ownership, controls and focused evidence | [layout.md](engineering/layout.md) |
| Component gallery, isolated previews and Elementor sample insertion | [component-library.md](engineering/component-library.md) |
| Shared admin controls, palette, minimal markup and RTL/LTR styling | [admin-ui.md](engineering/admin-ui.md) |
| Optional plugin cards, native installation and bundled Core ZIP | [plugin-installer.md](engineering/plugin-installer.md) |
| CSS variants and plugin compatibility | [compatibility.md](scenarios/compatibility.md) |
| Modular JS, GSAP and no external runtime connections | [local-assets.md](scenarios/local-assets.md) |
| Security and no public registration/account features | [security-access.md](scenarios/security-access.md) |
| Final technology decisions and remaining technical details | [architecture.md](engineering/architecture.md) |
| طراحی اختصاصی خانهٔ مشتری | [خانه](engineering/client-home.md) |
| طراحی وبلاگ، کارت و cover مقاله در Child | [وبلاگ](engineering/client-blog.md) |
| Client-specific styles, templates and child theme | [client-customization.md](engineering/client-customization.md) |
| Shared persistent local WordPress, Docker start/stop, credentials and source mounts | [local-development.md](engineering/local-development.md) |
| Future phases and validation | [roadmap.md](product/roadmap.md) |
| Documentation/agent maintenance | [SKILL.md](../.agents/skills/radman-documentation/SKILL.md) |
| Authorized theme work or technical feasibility | [SKILL.md](../.agents/skills/radman-theme-work/SKILL.md) |
| Fast lookup of supplied theme/plugin implementation references | [reference-map.md](research/reference-map.md) |
| وضعیت و مرز محصول | [خلاصه پروژه](product/essentials.md) |
| نصب و ترتیب بازبینی مالک روی سرور تست | [راهنمای سرور تست](engineering/test-server.md) |
| نتیجه بررسی قبلی و منابع | [یادداشت تحقیق](research/references.md) |

تاریخچهٔ پیشین در [آرشیو پاک‌سازی](archive/2026-11-02-documentation-cleanup/README.md) محفوظ است. فایل قدیمی [foundation](implementation/foundation.md) فقط نشانی تاریخچه است و برای شروع کار خوانده نمی‌شود.
