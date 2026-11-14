# Architecture

Standalone Radman CT Music, copied from Corporate-Webtalik as a source baseline. Blog and native track module were selected from the independent Radman Design Library and remapped to local data. No runtime access or synchronization to either source project.

| Owner | Responsibility |
|---|---|
| `radmanct/inc/content/music.php` | Native `track`, `music_category`, `_rdc_track`, admin fields and nonce/capability-checked saves |
| `radmanct/inc/music.php` | Normalized metadata, local audio/privacy guards, one track card renderer |
| `radmanct/template-parts/music/` | Music archive/category and single content; same main query |
| `radmanct/inc/pages/music-home.php` | Home editorial filter and bounded native queries |
| `radmanct/template-parts/sections/home/{music-intro,latest-tracks,latest-posts}.php` | Named home composition; shared track/blog card renderers |
| `radmanct/inc/client.php` | Current customer copy, footer links and selected fixed dark sections; no settings framework |
| `radmanct/inc/assets.php` | Conditional entry loading and shared static/dynamic module graph |
| `radmanct/src/css/tokens.css`, `base.css` | Single palette/font/radius definitions and tag typography; Hafez H1, Peyda body/H2–H6 |
| `radmanct/src/css/ui/music.css` | Shared archive/single/home music UI |
| `radmanct/src/css/pages/music{,-home}.css` | Small page entries selecting needed UI; home reuses blog card CSS |
| `radmanct/src/js/admin/music-editor.js` | Native media picker, only on track editor; frontend audio uses native controls |
| `radmanct/inc/seo.php` | Fallback yields to Yoast SEO; never duplicate canonical/metadata/schema |
| `tools/local-wordpress/` | Isolated Docker runtime and guarded first-install provisioning |

WordPress owns editorial content, attachment IDs, menus, users and configuration. No customer source database or user accounts were copied. The optional audio download defaults to the playback attachment. Audio/image guards preserve private/password-protected parents. Demo content is explicitly replaceable, including an original eight-second synthetic playback test file.

Local runtime: `http://127.0.0.1:8098`, project `corporate-music`, volumes `corporate-music-database` and `corporate-music-wordpress`. Start/Stop never reset credentials or content. Yoast files are installed in this runtime, with no source-project settings copied.

For a future server move, transfer this theme including consumed `dist/`, local fonts/languages, WordPress uploads/database and the selected plugins through a normal WordPress migration. Replace local URL references using a serialization-aware migration, supply production database configuration/HTTPS and remove local-only MU restrictions/environment/debug settings from the hosting setup. No deployment is performed by this project setup.
