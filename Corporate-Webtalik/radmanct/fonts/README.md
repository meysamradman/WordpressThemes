# Local font sources

PeydaWebVF.woff2 and Hafez-FD-Regular.woff2 are the existing owner-supplied font files copied from Corporate-Catalog/radmanct/fonts/persian. This project does not grant additional font licensing rights. Preserve the original provenance and owner licensing when deploying or redistributing. The shared faces in `src/css/tokens.css` and existing preloads use the same URLs.

Runtime font files live in this directory. The build references them without copying them into `dist`.

`InstrumentSans-SemiBold-Latin.woff2` is the weight 600 subset from [Google Fonts](https://fonts.google.com/specimen/Instrument+Sans), with Latin letters, digits and punctuation for technology names. See the [SIL Open Font License](InstrumentSans-OFL.txt). The face is defined once in `src/css/pages/service-technology.css` and used by technology headings. Persian glyphs use the body font. Instrument Sans loads on use without a preload.
