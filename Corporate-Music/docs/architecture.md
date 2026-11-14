# Architecture

Standalone Radman CT Music, copied from Corporate-Webtalik as a source baseline. Blog and native track module were selected from the independent Radman Design Library and remapped to local data. No runtime access or synchronization to either source project.

| Owner | Responsibility |
|---|---|
| `radmanct/inc/content/music.php` | Native `track`, `music_category`, `_rdc_track`, admin fields and nonce/capability-checked saves |
| `radmanct/inc/music.php` | Normalized metadata, local audio/privacy guards, one track card renderer |
| `radmanct/template-parts/music/` | Music archive/category and single content; same main query |
| `radmanct/inc/pages/music-home.php` | Home editorial filter and bounded native queries |
| `radmanct/template-parts/sections/home/{music-intro,latest-tracks,latest-videos}.php` | Named home composition; shared track/blog card renderers |
| `radmanct/inc/client.php` | Current customer copy, footer links and selected fixed dark sections; no settings framework |
| `radmanct/inc/assets.php` | Conditional entry loading and shared static/dynamic module graph |
| `radmanct/src/css/tokens.css`, `base.css` | Single palette/font/radius definitions and tag typography; Hafez H1, Peyda body/H2–H6 |
| `radmanct/src/css/ui/music.css` | Shared archive/single/home music UI |
| `radmanct/src/css/pages/music{,-home}.css` | Small page entries selecting needed UI; home reuses shared track cards, video cards and the button renderer |
| `radmanct/src/js/admin/music-editor.js` | Native media picker, only on track editor; frontend audio uses native controls |
| `radmanct/inc/seo.php` | Fallback yields to Yoast SEO; never duplicate canonical/metadata/schema |
| `tools/local-wordpress/` | Isolated Docker runtime and guarded first-install provisioning |

WordPress owns editorial content, attachment IDs, menus, users and configuration. No customer source database or user accounts were copied. The optional audio download defaults to the playback attachment. Audio/image guards preserve private/password-protected parents. Demo content is explicitly replaceable, including an original eight-second synthetic playback test file.

Local runtime: `http://127.0.0.1:8098`, project `corporate-music`, volumes `corporate-music-database` and `corporate-music-wordpress`. Start/Stop never reset credentials or content. Yoast files are installed in this runtime, with no source-project settings copied.

For a future server move, transfer this theme including consumed `dist/`, local fonts/languages, WordPress uploads/database and the selected plugins through a normal WordPress migration. Replace local URL references using a serialization-aware migration, supply production database configuration/HTTPS and remove local-only MU restrictions/environment/debug settings from the hosting setup. No deployment is performed by this project setup.

The client homepage follows the supplied artist mockup: charcoal/plum surfaces, pink actions and mint/green brand/hero. All palette values remain in tokens.css. The shared header/navigation/footer are client layouts, not Webtalik clones. Header search targets native track search. Primary destinations are /, /music/, /videos/, /gallery/, /about-us/ and /contact-us/. The biography/contact editor content remains explicitly provisional until supplied; never invent contact details or artist history.

inc/videos.php + template-parts/ui/video-grid.php + src/css/ui/video-grid.css own the temporary video cards used by home and /videos/. This is not a new video CPT. Client media IDs/copy come from radmanct/videos/data in inc/client.php. Gallery data belongs to inc/pages/gallery.php and respects attachment privacy. Home JS is a small native feature/rail controller plus one shared ui/music-player.js audio owner. No autoplay; multiple audio players are not instantiated. The original Radman screenshot.png is the approved theme thumbnail and must not be replaced by a page screenshot.
