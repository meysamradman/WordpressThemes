# Corporate Music

Independent customer edition of Radman CT in the parent WordpressThemes repository. Only change this project. No nested Git, source-project database access, automatic synchronization, commit/push or deployment.

- Read `.agents/skills/music-work/SKILL.md` and `docs/architecture.md` before implementation. `docs/naming.md` and `docs/wordpress.md` retain Radman CT contracts.
- Theme folder/text domain `radmanct`, PHP namespace `RadmanCT`, DOM/CSS prefix `rdc-`. No Elementor or library shell dependencies.
- Local Docker project `corporate-music`, port 8098, dedicated database/network/volumes. Start/Stop preserve data. Never use Webtalik/library volumes or copy their databases.
- Shared fonts/colors/radius belong to `src/css/tokens.css`, tag typography to `base.css`; reuse button, icons, lifecycle and blog cards. CSS/JS entries load only on their consumers. Keep audio native, local and without autoplay.
- `inc/content/music.php` owns track registration and editor writes; `inc/music.php` owns frontend data/privacy/card helpers. Home composes named sections with data owned by `inc/pages/music-home.php`. Workshop UI and demo-query adapters never ship.
- Yoast SEO owns metadata/canonical/schema; preserve the theme fallback's plugin detection. No duplicate tags, title-specific H2 hacks or plugin-field overrides. One H1 per page; H2 sections, H3 cards, decorative text p/span.
- All runtime assets are local. Preserve stored metadata, media/privacy guards, native APIs, escaping, nonce/capabilities and author/license notices. No Node runtime on hosting.
- Credentials remain in ignored `.env.local-wordpress` and `.local-admin.txt`; never commit them. Run short targeted checks only. No long tests, release ZIP, server migration or deployment unless requested.

- Client homepage palette/layout follows the supplied artist mockup, not Webtalik styling: plum/charcoal, mint #16cc9f. Shared tokens remain authoritative. Video is a shared temporary section/page, no video CPT until requested. Theme thumbnail is the original Radman image from Webtalik; never overwrite it with a browser screenshot.
