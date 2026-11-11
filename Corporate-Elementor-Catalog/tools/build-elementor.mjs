import { readFile, writeFile } from 'node:fs/promises';

const root = new URL('../', import.meta.url);
const { pattern } = JSON.parse(await readFile(new URL('shared/elementor/attributes.json', root), 'utf8'));
const source = await readFile(new URL('shared/elementor/attributes.php', root), 'utf8');
const php = source.replace("'{{pattern}}'", "'" + pattern.replaceAll('\\', '\\\\').replaceAll("'", "\\'") + "'");
const twig = `{% macro render(settings) %}
{% import 'elementor/macros' as m %}
{% set attributes = settings.attributes | default('') %}
{{ m.render_custom_attributes(settings | merge({'attributes': attributes matches ${JSON.stringify(pattern)} ? attributes : ''})) }}
{% endmacro %}
`;
for (const name of ['radmanct', 'radmanct-components']) {
  const directory = new URL(`${name}/inc/integrations/elementor/`, root);
  await writeFile(new URL('safe-attributes.php', directory), php);
  await writeFile(new URL('safe-attributes.html.twig', directory), twig);
}
