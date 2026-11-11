from pathlib import Path
import json
import math
import re
import xml.etree.ElementTree as ET
import argparse
import shutil

root = Path(__file__).resolve().parent.parent
parser = argparse.ArgumentParser()
parser.add_argument('--source', type=Path)
parser.add_argument('--manifest', type=Path)
parser.add_argument('--output', type=Path)
parser.add_argument('--pack')
args = parser.parse_args()
project = any((args.source, args.manifest, args.output))
if project and not all((args.source, args.manifest, args.output)):
    parser.error('--source, --manifest and --output must be supplied together')
if args.pack and (not project or not re.fullmatch(r'[a-z][a-z0-9-]{0,29}', args.pack) or args.pack in {'general', 'social'}):
    parser.error('--pack requires project output and a non-reserved lowercase pack name')
source = args.source if project else root / 'shared/icons/images'
manifest = json.loads((args.manifest if project else root / 'tools/icons.json').read_text(encoding='utf-8'))
tags = {'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse'}
attributes = {'d', 'cx', 'cy', 'r', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'width', 'height', 'rx', 'ry', 'points', 'fill-rule', 'clip-rule', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin'}

def quote(value):
    return "'" + value.replace('\\', '\\\\').replace("'", "\\'") + "'"

rows = []
entries = dict(manifest['icons'])
for path in sorted((source / 'custom').glob('*.svg')):
    name = (args.pack or 'custom') + '-' + path.stem
    if not re.fullmatch(r'[a-z][a-z0-9-]{0,70}', name):
        raise ValueError(f'Invalid or duplicate custom icon name: {name}')
    if name in entries:
        if entries[name]['file'] != 'custom/' + path.name:
            raise ValueError(f'Duplicate custom icon name: {name}')
        continue
    entries[name] = {'file': 'custom/' + path.name, 'directional': False}
for name, entry in entries.items():
    if not re.fullmatch(r'[a-z][a-z0-9-]{0,70}', name):
        raise ValueError(f'Invalid icon name: {name}')
    if args.pack and not name.startswith(args.pack + '-'):
        raise ValueError(f'Icon must use the {args.pack}- prefix: {name}')
    pack = args.pack or ('social' if entry['file'].startswith('tabler/') else 'general')
    path = (source / entry['file']).resolve()
    if not path.is_relative_to(source.resolve()) or not path.is_file():
        raise ValueError(f'Invalid icon path: {name}')
    raw = path.read_text(encoding='utf-8')
    if len(raw) > 20000 or '<!DOCTYPE' in raw.upper() or '<!ENTITY' in raw.upper():
        raise ValueError(f'Unsupported SVG: {name}')
    svg = ET.fromstring(raw)
    viewbox = svg.attrib.get('viewBox', '')
    values = [float(number) for number in viewbox.split()]
    if svg.tag.rsplit('}', 1)[-1] != 'svg' or len(values) != 4 or not all(math.isfinite(number) for number in values) or not 0 < values[2] <= 1024 or not 0 < values[3] <= 1024:
        raise ValueError(f'Unsupported viewBox: {name}')
    fill = svg.attrib.get('fill', 'currentColor')
    stroke = svg.attrib.get('stroke', 'none')
    if args.pack and stroke != 'none' and values != [0, 0, 24, 24]:
        raise ValueError(f'Line icons in a pack must use the shared 24x24 viewBox: {name}')
    for color in (fill, stroke):
        if color not in {'none', 'currentColor'} and not re.fullmatch(r'#[a-fA-F0-9]{3,8}', color):
            raise ValueError(f'Unsupported root color: {name}')
    if set(svg.attrib) - {'class', 'width', 'height', 'viewBox', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'version'}:
        raise ValueError(f'Unsupported root attributes: {name}')
    parts = []
    for node in svg:
        tag = node.tag.rsplit('}', 1)[-1]
        if tag not in tags or len(node) or set(node.attrib) - attributes or (node.text or '').strip():
            raise ValueError(f'Unsupported SVG content: {name}')
        for value in node.attrib.values():
            if any(character in value for character in '<>"&') or 'url(' in value.lower():
                raise ValueError(f'Unsupported SVG attribute: {name}')
        node.attrib.pop('stroke-width', None)
        attrs = ' '.join(f'{key}="{value}"' for key, value in node.attrib.items())
        parts.append(f'<{tag} {attrs}/>')
    label = entry.get('label', name.replace('-', ' ').title())
    if not isinstance(label, str) or not label.isascii():
        raise ValueError(f'Use an English technical label and translation resources: {name}')
    rows.append(f"\t{quote(name)} => array( 'pack' => {quote(pack)}, 'directional' => {'true' if entry['directional'] else 'false'}, 'label' => {quote(label)}, 'viewbox' => {quote(viewbox)}, 'fill' => {quote(fill)}, 'stroke' => {quote(stroke)}, 'body' => {quote(''.join(parts))} ),")
header = "<?php\n// Generated trusted SVG catalog. Licenses: images/icons/*/LICENSE.\n\ndefined( 'ABSPATH' ) || exit;\n\nreturn array(\n"
outputs = [args.output] if project else [root / package / 'inc/icons/catalog.php' for package in ('radmanct', 'radmanct-components')]
for output in outputs:
    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(header + '\n'.join(rows) + '\n);\n', encoding='utf-8')
if not project:
    for package in ('radmanct', 'radmanct-components'):
        target = root / package
        shutil.copyfile(root / 'shared/icons/engine.php', target / 'inc/icons/engine.php')
        shutil.copyfile(root / 'shared/icons/elementor.php', target / 'inc/icons/elementor.php')
        shutil.copyfile(root / 'shared/icons/elementor-transformer.php', target / 'inc/icons/elementor-transformer.php')
        shutil.copytree(source, target / 'images/icons', dirs_exist_ok=True)
print(f'Generated {len(rows)} trusted SVG definitions.')
