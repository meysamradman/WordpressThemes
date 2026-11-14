# Collection layouts
Native collection metadata and media/lightbox behavior selected from Corporate-Music/radmanct on 2026-11-15. Presentation reuses the existing library blog featured/tile card and pagination owners, with the library tokens; the customer palette and page styling are excluded. Source code only; no customer database, credentials, portrait, logo or media-library copy. Existing music samples are unchanged.

Demo album/clip records are independently created by tools/register-collections.php. Media are existing local library attachment IDs (including the media-mosaic video); their original provenance and licenses remain unchanged. Demo quality downloads are enabled only when the selected file metadata matches that resolution. Replace fixtures with licensed customer media when transferring.

Four independent module entry points: gallery-archive, gallery-single, video-archive, video-single. Shared small owners: native collection metadata/media validation, library tokens, blog cards/pagination and album grid styles, archive enhancement, gallery dialog controller and delegated video pause behavior. No module imports another module.
