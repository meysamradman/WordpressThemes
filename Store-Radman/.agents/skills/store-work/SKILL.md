---
name: store-work
description: Build and maintain Store Radman, the independent custom PHP WooCommerce storefront and native WordPress blog.
---
Read project AGENTS.md, docs/architecture.md and docs/frontend-standard.md; then select only the relevant owner in ../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md. These paths are relative to the project root.
- Use only store-radman / MySQL store_radman_local / localhost:8100. Start/Stop preserve volumes; never use sibling databases, credentials or customer media.
- Native WooCommerce owns commerce and orders. Prefer hooks and Woo CRUD; preserve checkout/account hooks, notices, variation stock handling and HPOS compatibility. Do not copy vendor templates before a concrete layout requires it.
- Blog is native post/category/tag. No project/track/video/gallery CPT, portfolio UI, Elementor or child theme.
- Shared standards are reused by reference. Within this theme tokens, base typography, blog cards, pagination renderer and pagination JS each have one owner. Reuse actual repeated behavior; do not import another theme's whole runtime.
- Theme namespace RadmanStore; prefix rds-. PHP templates and inc own rendering/data; local assets under assets. Peyda variable font and inherited ss02/ss03 cover body, headings and controls.
- Preserve radmanstore/screenshot.png, the approved Radman theme-preview artwork. Check its presence when creating or packaging the theme; Appearance must not show an empty preview. See shared standard 17, Theme preview artwork.
- Blog uses numbered pagination; Woo archives use more-on-scroll, with native next-page links and retry on failure. Both use inc/pagination.php. Do not put infinite scroll on account/cart/checkout.
- Yoast owns SEO; retain wp_head/wp_footer and title-tag. All-in-One WP Migration is backup/migration, not a second SEO engine. Local remains noindex.
- Add/update plugins manually through native WordPress; keep plugins writable and update discovery intact. No vendor patches or paid package claims.
- Use focused route/template/media checks. Payment, email, shipping, tax and migration end-to-end tests require their actual configured scenario; base setup does not establish production readiness.
