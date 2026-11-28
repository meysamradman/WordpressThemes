# Local font sources

PeydaWebVF.woff2 is the existing owner-supplied font copied unchanged from Corporate-Music/radmanct/fonts, originally from Corporate-Catalog/radmanct/fonts/persian. Preserve the owner licensing when deploying or redistributing.

The canonical font lives in this directory. Store Radman defines font-face once in assets/tokens.css and uses it for body, headings and form controls. No external font requests or extra copies are required.

The source font's GSUB ss02/ss03 substitutions support Persian display of Latin and Arabic digits. Glyphs, axes and original name/license tables were retained by the source project. Store Radman inherits these font features through base.css; it does not convert DOM text, URLs, download filenames or stored values.
