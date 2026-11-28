# Store Radman
Independent custom classic PHP WooCommerce theme: radmanstore, namespace RadmanStore, prefix rds-. Scope: blog and shop; no portfolio, Elementor, child theme or copied client data.
Read README.md and docs/architecture.md. Shared rules: ../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md, custom route.
Only Store-Radman source and dedicated store-radman Docker project may be changed here. No sibling databases/volumes. Do not copy Corporate-Music or Webtalik database or wholesale theme source.
Native WooCommerce owns products, variations, stock, cart, checkout, customer accounts and orders. Use Woo CRUD for orders; preserve HPOS compatibility. Yoast owns SEO metadata; no second SEO plugin or duplicate schema engine. All-in-One WP Migration owns backup/migration.
No final archive/product/account redesign authorized yet. Prefer hooks before vendor template copies. Keep assets scoped and runtime local. Never patch plugin vendor code or suppress update notices.
Use short focused checks. No checkout/payment/order/email integration is configured or verified by base setup.
Credentials, local backups and runtime files stay ignored. Start/Stop preserve volumes. No reset, deployment, commit or push without explicit request.
