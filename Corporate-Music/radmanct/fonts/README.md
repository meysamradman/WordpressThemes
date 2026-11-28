# Local font sources

PeydaWebVF.woff2 is the existing owner-supplied font file copied from Corporate-Catalog/radmanct/fonts/persian. The theme uses it for both headings and body. Preserve the original provenance and owner licensing when deploying or redistributing. Preload and CSS use the same local font URL.

Canonical font files live only in this directory. Build references them without copying into dist; font-face is defined once in src/css/tokens.css.

The existing GSUB `ss02` and `ss03` substitutions are also registered for Latin script runs so CSS can display Latin and Arabic digits as Persian. Glyphs, axes and original name/license tables are retained. The matching `?digits=1` URL in tokens.css and the preload prevents reuse of the earlier font cache. Keep one local font and inherited font-feature-settings in base.css; no DOM text, URL, download filename or stored data conversion.
