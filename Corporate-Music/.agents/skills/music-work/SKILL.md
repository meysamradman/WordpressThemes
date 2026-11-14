---
name: music-work
description: Maintain the independent Corporate Music Radman CT customer theme, native tracks and editorial pages.
---
Read AGENTS.md and the touched owner in docs/architecture.md. Work only in Corporate-Music. Source references Corporate-Webtalik and Radman-Design-Library are read-only unless explicitly requested.
Reuse shared tokens/base/button/icons/blog cards and native WordPress APIs. Split only actual page/module responsibilities; no duplicate fonts, palette, player engine or workshop adapters. Load CSS/JS only on consuming routes. Music uses native controls and local attachment IDs; preserve private/password-protected content and stored keys. Keep metadata with Yoast and don't duplicate or override its outputs.
Use local project corporate-music/8098 only; Start/Stop preserve all data. No commit, deploy, long test matrix or destructive reset by default. Report local checks separately from production proof.
