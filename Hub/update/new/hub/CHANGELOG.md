# Changelog

## 7.0.1 (2026-11-12)

- New: All-Access card in the theme dashboard, setup wizard and updates, for administrators.
- Security: the setup wizard and its import steps require an administrator and a valid security token.
- Security: a Liquid Portal activation is accepted only from an administrator who started it from this site.
- Security: the header search only searches public post types.
- Security: SVG files are cleaned when they are uploaded, and SVG icons are cleaned before they are printed in a page.
- Security: connections to Liquid Themes servers use HTTPS, and certificate checks are no longer switched off.
- Security: the shop filter no longer returns internal query details, and the cache action checks the user's permissions and a security token.
- Security: the update popup escapes the text it gets from the update server, and only users who can update themes see or dismiss it.
- Security: dismissing the update notices needs a valid security token.
- Security: newsletter forms send the audience signature that Hub Core and Hub Elementor Addons 7.0.1 check.
- Fixed: a license revoke sent back from the Liquid Portal no longer shows an activation error.
- Fixed: opening the site through Hostinger's preview address no longer removes the theme's license as if the site had moved to a new domain.
- Requires Hub Core 7.0.1 and Hub Elementor Addons 7.0.1.
