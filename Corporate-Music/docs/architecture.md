# Architecture

Standalone Radman CT Music, copied from Corporate-Webtalik as a source baseline. Blog and native track module were selected from the independent Radman Design Library and remapped to local data. No runtime access or synchronization to either source project.

| Owner | Responsibility |
|---|---|
| `radmanct/inc/content/music.php` | Native `track`, `music_category`, `_rdc_track`, artist/poet/arranger/composer/release fields and nonce/capability-checked saves |
| `radmanct/inc/music.php` | Normalized metadata, local audio/privacy guards, one track card renderer |
| `radmanct/template-parts/music/` | Music archive/category and single content; same main query |
| `radmanct/inc/pages/music-home.php` | Home editorial filter and bounded native queries |
| `radmanct/template-parts/sections/home/{music-intro,latest-tracks,latest-videos}.php` | Named home composition; shared track/blog card renderers |
| `radmanct/inc/client.php` | Current customer copy, footer links and selected fixed dark sections; no settings framework |
| `radmanct/inc/assets.php` | Conditional entry loading and shared static/dynamic module graph |
| `radmanct/src/css/tokens.css`, `base.css` | Single palette/font/radius definitions and tag typography; Hafez H1, Peyda body/H2–H6 |
| `radmanct/src/css/ui/music.css` | Shared archive/single/home music UI |
| `radmanct/src/css/pages/music{,-home}.css` | Small page entries selecting needed UI; home reuses shared track cards, video cards and the button renderer |
| `radmanct/src/js/admin/music-editor.js` | Native media picker, only on track editor; frontend audio uses the native audio engine with a progressive custom control bar on detail pages |
| `radmanct/inc/seo.php` | Fallback yields to Yoast SEO; never duplicate canonical/metadata/schema |
| `tools/local-wordpress/` | Isolated Docker runtime and guarded first-install provisioning |

WordPress owns editorial content, attachment IDs, menus, users and configuration. No customer source database or user accounts were copied. The optional audio download defaults to the playback attachment. Audio/image guards preserve private/password-protected parents. Demo content is explicitly replaceable, including an original eight-second synthetic playback test file.

Local runtime: `http://127.0.0.1:8098`, project `corporate-music`, volumes `corporate-music-database` and `corporate-music-wordpress`. Start/Stop never reset credentials or content. Yoast files are installed in this runtime, with no source-project settings copied.

For a future server move, transfer this theme including consumed `dist/`, local fonts/languages, WordPress uploads/database and the selected plugins through a normal WordPress migration. Replace local URL references using a serialization-aware migration, supply production database configuration/HTTPS and remove local-only MU restrictions/environment/debug settings from the hosting setup. No deployment is performed by this project setup.

The client homepage follows the supplied artist mockup: charcoal/plum surfaces, mint #16cc9f actions/brand and a green hero. All palette values remain in tokens.css. The shared header/navigation/footer are client layouts, not Webtalik clones. Header search targets native track search. Primary destinations are /, /music/, /videos/, /gallery/, /about-us/ and /contact-us/. The biography/contact editor content remains explicitly provisional until supplied; never invent contact details or artist history.


Music archives use eight entries per native main query and the shared pages/archive.js AJAX owner used by blog/project archives; ordinary pagination links work without JavaScript. The list variant of Music\card reuses its metadata/credit renderer; home retains the compact variant. Single tracks compose a cover/credit/download row, template-parts/music/player.php and optional editorial/lyrics sections. src/js/ui/audio-controls.js owns only the native audio control UI, selected by pages/music.js on track singles; native browser controls remain the no-JavaScript fallback. No autoplay or second audio engine. ui/music-archive.css, music-detail.css and audio-player.css belong to the music page entry, not the homepage. Metadata is never fabricated for empty credits.

Biography uses page-about-us.php with route-only pages/biography.css. The native page editor owns the supplied biography paragraphs and semantic works table; its featured attachment owns the replaceable portrait/composite. Keep the page password guard before either content or image. The current local featured image is temporary until the artist supplies the original composite; do not manufacture logos or social URLs.

Native gallery/video ownership: inc/content/collections.php registers album (/gallery/) and clip (/videos/), independent category/tag taxonomies and guarded metadata saves. inc/admin/collections.php extends the native list tables without replacing author, taxonomy, date, quick/bulk editing or Yoast columns. The old placeholder WordPress pages are retained as drafts; their public routes now belong to native archives.

inc/albums.php owns validated ordered attachment IDs and sixteen-photo paging inside albums. inc/videos.php owns local video validation, real quality-download IDs and the latest three clips on home. A missing 1080/720/480 file produces a disabled button; never label a different source as another resolution. Customer fields belong in the native editor: album image order/featured cover, clip primary video/quality files/featured cover/title/body/excerpt, and track credits/date/lyrics/audio/download. Metadata and media protections must survive quick edits and draft saves.

archive-album.php and archive-clip.php use the native main query, shared category-tree renderer and pages/archive.js. Album archives show sixteen covers; video archives show four inline players. Single albums page photos sixteen at a time through gallery_page and the same AJAX pagination helper, without intercepting unrelated taxonomy links. Lightbox markup/style/motion has one owner shared by albums and existing projects; native dialog, keyboard/RTL arrows, touch and focus return are required. Without JavaScript, image links and ordinary pagination still work.

Gallery/video styles follow the supplied references: four square photo columns with a fine accent border, two video columns, full-width detail player and separate quality download actions. Category controls collapse above the grid. Attachments remain intrinsic/responsive/lazy; native video uses preload=none and no autoplay. ui/video-player.js pauses other videos through one delegated listener, including after AJAX swaps. No second playback engine, remote service, new runtime dependency or invented real artist media is introduced. Temporary demo albums/videos use existing local media. The approved Radman screenshot.png remains the theme thumbnail.

Local database runtime uses pinned MySQL 8.4.13 LTS and a separate MySQL volume. WordPress APIs remain portable to MariaDB; no engine-specific theme dependency is permitted. Keep utf8mb4_unicode_520_ci as the shared default collation and preserve existing table collations. Start/Stop preserves data and credentials. Retained MariaDB volumes are recovery data, not MySQL data directories; engine changes require explicit backup/restore into a different volume. Private logical/physical/filesystem backups and restore manifests live under ignored backups/.

Artist identity uses the native WordPress custom_logo theme mod and site_icon option with media attachments. Preserve the original supplied wordmark PNG unchanged when vector tracing alters its lettering. The six supplied social marks live as separate 24-unit SVG files in images/icons/social/ and are selected by the existing icon renderer through an explicit filename allowlist and per-request cache. They render inline with currentColor; do not add a font/icon CDN, sprite bitmap or additional JavaScript. Keep missing social URLs noninteractive until the owner provides actual addresses. Native WordPress owns 32/180/192/270 site-icon variants and header output; do not duplicate favicon tags. Theme screenshot remains the approved Radman thumbnail.
