# Corporate Music

Independent customer edition of Radman CT in the parent WordpressThemes repository. Only change this project. No nested Git, source-project database access, automatic synchronization, commit/push or deployment.

- Read `.agents/skills/music-work/SKILL.md` and `docs/architecture.md` before implementation. `docs/naming.md` and `docs/wordpress.md` retain Radman CT contracts.
- Theme folder/text domain `radmanct`, PHP namespace `RadmanCT`, DOM/CSS prefix `rdc-`. No Elementor or library shell dependencies.
- Local Docker project `corporate-music`, port 8098, dedicated database/network/volumes. Start/Stop preserve data. Never use Webtalik/library volumes or copy their databases.
- Shared fonts/colors/radius belong to `src/css/tokens.css`, tag typography to `base.css`; reuse button, icons, lifecycle and blog cards. CSS/JS entries load only on their consumers. Keep audio native, local and without autoplay.
- `inc/content/music.php` owns track registration and editor writes; `inc/music.php` owns frontend data/privacy/card helpers. Home composes named sections with data owned by `inc/pages/music-home.php`. Workshop UI and demo-query adapters never ship.
- Yoast SEO owns metadata/canonical/schema; preserve the theme fallback's plugin detection. No duplicate tags, title-specific H2 hacks or plugin-field overrides. One H1 per page; H2 sections, H3 cards, decorative text p/span.
- Fixed design artwork for custom pages such as home and biography is owned by their templates and packaged as WebP under `radmanct/images/pages/`. Do not bind it to a page featured image. Editorial covers for posts, tracks, projects, videos and albums keep their native attachment IDs.
- All runtime assets are local. Preserve stored metadata, media/privacy guards, native APIs, escaping, nonce/capabilities and author/license notices. No Node runtime on hosting.
- Credentials remain in ignored `.env.local-wordpress` and `.local-admin.txt`; never commit them. Read [Ali Asadi hosting and login rules](docs/wordpress.md#hosting-packages) before packaging this site or changing login data. Run short targeted checks only. No long tests, release ZIP, server migration or deployment unless requested.

- Client homepage palette/layout follows the supplied artist mockup, not Webtalik styling: plum/charcoal, mint #16cc9f. Shared tokens remain authoritative. Gallery and videos use native album/clip post types with separate hierarchical categories and tags. Keep their editor, archive and detail owners modular; reuse the native media picker and shared AJAX/lightbox/video behaviors. Theme thumbnail is the original Radman image from Webtalik; never overwrite it with a browser screenshot.

- Audio/video/gallery and archive/detail media changes must follow `docs/media.md`: defer playback media, preserve bounded queries and native responsive images, and keep approved decorative-video guards.
- Explicit external-media exception: use only validated Aparat embeds through the shared renderer in docs/media.md. Home prepares the provider player for visible cards to enable one-click play. The archive opens one player on selection. Details retain the provider's poster and Play. Local videos keep preload=none.

## Customer frontend standard

Use [the project frontend profile](docs/frontend-standard.md), [the affected scenario](docs/scenarios.md) and the matching section in [the shared standards index](../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md). Preserve approved motion, local noindex and Yoast ownership. Check only the affected scope.
