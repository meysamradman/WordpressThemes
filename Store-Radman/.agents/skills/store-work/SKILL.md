---
name: store-work
description: Build and maintain Store Radman, the independent custom PHP WooCommerce storefront and native WordPress blog.
---
Read project AGENTS.md, docs/architecture.md and docs/frontend-standard.md; then select only the relevant owner in ../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md. These paths are relative to the project root.
Read docs/wordpress.md before commerce, routes, asset-loading, SEO, analytics or gateway changes. It owns the current native product renderer, cart/checkout Blocks, request exceptions and ZarinPal sandbox preparation. Appearance still follows the user's supplied design.
- Use only store-radman / MySQL store_radman_local / localhost:8100. Start/Stop preserve volumes; never use sibling databases, credentials or customer media.
- Native WooCommerce owns commerce and orders. Prefer hooks and Woo CRUD; preserve checkout/account hooks, notices, variation stock handling and HPOS compatibility. Do not copy vendor templates before a concrete layout requires it.
- Blog is native post/category/tag. No project/track/video/gallery CPT, portfolio UI, Elementor or child theme.
- Shared standards are reused by reference. Within this theme tokens, base typography, blog cards, pagination renderer and pagination JS each have one owner. Reuse actual repeated behavior; do not import another theme's whole runtime.
- Theme namespace RadmanStore; prefix rds-. PHP templates and inc own rendering/data; local assets under assets. Peyda variable font and inherited ss02/ss03 cover body, headings and controls.
- Preserve radmanstore/screenshot.png, the approved Radman theme-preview artwork. Check its presence when creating or packaging the theme; Appearance must not show an empty preview. See shared standard 17, Theme preview artwork.
- Blog uses numbered pagination; Woo archives use more-on-scroll, with native next-page links and retry on failure. Both use inc/pagination.php. Do not put infinite scroll on account/cart/checkout.
- Yoast owns SEO; retain wp_head/wp_footer and title-tag. All-in-One WP Migration is backup/migration, not a second SEO engine. Local remains noindex.
- Add/update plugins manually through native WordPress; keep plugins writable and update discovery intact. No vendor patches or paid package claims.
- SnappPay remains installed inactive until its documented TLS, activation telemetry and checkout Blocks blockers are resolved. Its vendor manual-update exception does not apply to other plugins; bootstrap must not auto-activate it.
- Inc owners are setup, assets, woocommerce and pagination; keep functions.php a bootstrap. Native content-product.php is the current reusable card for catalog/related and future PHP home sections; add no competing card renderer or full vendor-template copy before the approved design needs it.
- Keep commerce assets on actual consumers, including embedded Woo shortcodes/blocks and declared PHP sections via radmanstore/commerce/assets. Keep payment/Store API/account/session scripts and service requests. Remove only identified optional telemetry/marketing/remote assets, never all outbound HTTP or all Woo CSS at once. Use the root performance skill for requested performance reviews.
- Yoast Site connections owns webmaster verification; Analytics needs a separate configured tag owner. Preserve native Product schema and one breadcrumb graph; no duplicate theme tags or tracking placeholders. One local font-face also serves wp-admin without overriding icon fonts.
- Use focused route/template/media checks. Payment, email, shipping, tax and migration end-to-end tests require their actual configured scenario; base setup does not establish production readiness.
