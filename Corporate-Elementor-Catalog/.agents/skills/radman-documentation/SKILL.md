---
name: radman-documentation
description: Maintain concise Radman CT scenarios, technical contracts, remaining work and agent routing; exclude runtime implementation and routine task reports.
---

# Radman CT documentation

## Locate the owner

Follow root AGENTS. Read essentials once; `rg` the topic in `radman-doc/README.md` only to locate an unknown owner, then read its relevant heading; do not read the whole index. Never preload the documentation tree or archives.

- Scenario: expected behavior and product scope.
- Engineering: current technical contract and necessary reference detail.
- Skill: short repeatable procedure; no copied product specifications.
- Roadmap: only remaining work/acceptance and its owner.
- Archive: superseded decisions and historical evidence, never current authority.

For edition naming, engineering/naming.md owns the confirmed target (Radman brand, Elementor rde-, independent custom rdc-) separately from current rdct- implementation. Keep that distinction explicit in essentials and AGENTS; updating documentation does not authorize source renaming, architectural conversion, package rebuilds or data migration. Link the vocabulary owner instead of copying its full tables into skills.

## Make the smallest useful change

1. Update documentation only for a material decision, corrected rule, public contract/API, ownership change or changed remaining gap. Routine code edits do not require a documentation entry.
2. Correct the existing owner in place. Preserve CONFIRMED/PROPOSED/OPEN/DEFERRED; do not append a dated competing rule or copy it into several owners.
3. Keep current requirements, storage/API boundaries, relevant examples and precise unresolved items. Remove repeated prose; retain specialized inventories when needed and make their sections directly discoverable.
4. Do not add task reports, build/test output, completed-work lists or daily logs to active docs, foundation or roadmap. Report checks in the task response. Update/remove an existing roadmap row only when its actual gap changes.
5. For cleanup, preserve useful original evidence in an explicit archive before substantial replacement. Repair links; keep archived content out of normal reading. Do not invent current acceptance from historical tests.
6. Change AGENTS/index/skills only when routing or repeatable workflow changes. Keep one routing index; link instead of duplicating catalogs.

## Validate

Check changed links/anchors, routing, skill frontmatter and UTF-8. Preserve user/concurrent edits. Run the skill validator when changing skills if available. Do not run app builds, servers, runtime tests or framework browsing for documentation-only work.
