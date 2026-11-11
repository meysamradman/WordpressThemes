from copy import deepcopy
from pathlib import Path
import unittest
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
CONFIG = ET.parse(ROOT / 'radmanct-components/wpml-config.xml')


def widget(name):
    return CONFIG.find(f'./elementor-widgets/widget[@name="{name}"]')


def at(value, path):
    for key in path:
        if isinstance(value, list):
            if not str(key).isdigit() or int(key) >= len(value):
                return None
            value = value[int(key)]
        elif isinstance(value, dict) and key in value:
            value = value[key]
        else:
            return None
    return value


def strings(name, settings):
    node = widget(name)
    result = []
    for field in node.findall('./fields/field'):
        path = field.text.split('>')
        if isinstance(at(settings, path), str) and at(settings, path) not in ('', '0'):
            result.append((path, field.attrib['editor_type']))
    for group in node.findall('./fields-in-item'):
        base = group.attrib['items_of'].split('>')
        rows = at(settings, base)
        if not isinstance(rows, list):
            continue
        for index, row in enumerate(rows):
            for field in group.findall('field'):
                path = field.text.split('>')
                if isinstance(at(row, path), str) and at(row, path) not in ('', '0'):
                    result.append((base + [index] + path, field.attrib['editor_type']))
    return result


def translated(name, settings):
    result = deepcopy(settings)
    for path, editor in strings(name, settings):
        target = result
        for key in path[:-1]:
            target = target[int(key) if isinstance(target, list) else key]
        key = int(path[-1]) if isinstance(target, list) else path[-1]
        target[key] = '/en/work/' if editor == 'LINK' else 'Translated ' + target[key]
    return result


def prop(kind, value):
    return {'$$type': kind, 'value': value}


class WpmlComponentsContract(unittest.TestCase):
    def test_classic_table_headers_and_cells_preserve_structure_and_settings(self):
        original = {'caption': 'Specifications', 'data': {'columns': ['Feature', 'Value'], 'rows': [{'cells': ['Material', '<strong>Steel</strong>'], '_id': 'row-a'}, {'cells': ['Width', '120 cm'], '_id': 'row-b'}]}, 'header': True, 'variant': 'striped', 'row_headers': True, 'line_color': '#ffffff'}
        result = translated('rdct-table', original)
        self.assertEqual(len(strings('rdct-table', original)), 7)
        self.assertEqual(result['data']['columns'], ['Translated Feature', 'Translated Value'])
        self.assertEqual(result['data']['rows'][0]['cells'][1], 'Translated <strong>Steel</strong>')
        self.assertEqual(result['data']['rows'][1]['_id'], 'row-b')
        self.assertEqual(original['data']['columns'][0], 'Feature')
        for key in ['header', 'variant', 'row_headers', 'line_color']:
            self.assertEqual(result[key], original[key])

    def test_atomic_table_translation_preserves_every_typed_envelope(self):
        def text(value):
            return prop('string', value)
        original = {'caption': text('Specifications'), 'data': prop('rdct-table-data', {'columns': prop('rdct-table-strings', [text('Feature'), text('Value')]), 'rows': prop('rdct-table-rows', [prop('rdct-table-row', {'cells': prop('rdct-table-strings', [text('Material'), text('<em>Steel</em>')])})])}), 'variant': text('grid')}
        result = translated('rdct-atomic-table', original)
        self.assertEqual(len(strings('rdct-atomic-table', original)), 5)
        expected = deepcopy(original)
        expected['caption']['value'] = 'Translated Specifications'
        for item in expected['data']['value']['columns']['value']:
            item['value'] = 'Translated ' + item['value']
        for item in expected['data']['value']['rows']['value'][0]['value']['cells']['value']:
            item['value'] = 'Translated ' + item['value']
        self.assertEqual(result, expected)

    def test_table_mapping_covers_all_twelve_columns_and_hundred_rows(self):
        matrix = {'columns': ['Header'] * 12, 'rows': [{'cells': ['Cell'] * 12} for _ in range(100)]}
        classic = {'data': matrix}
        atomic = {'data': prop('rdct-table-data', {'columns': prop('rdct-table-strings', [prop('string', value) for value in matrix['columns']]), 'rows': prop('rdct-table-rows', [prop('rdct-table-row', {'cells': prop('rdct-table-strings', [prop('string', value) for value in row['cells']])}) for row in matrix['rows']])})}
        for name, settings in [('rdct-table', classic), ('rdct-atomic-table', atomic)]:
            self.assertEqual(len(strings(name, settings)), 1212)
        result = translated('rdct-table', classic)
        self.assertEqual(result['data']['rows'][99]['cells'][11], 'Translated Cell')
        self.assertEqual(strings('rdct-table', {'data': {'columns': [], 'rows': [{'cells': [None, 12]}]}}), [])
        self.assertEqual(translated('rdct-table', {'data': {'columns': [''], 'rows': [{'cells': ['0']}]}}), {'data': {'columns': [''], 'rows': [{'cells': ['0']}]}})

    def test_classic_carousel_translation_preserves_technical_settings(self):
        row = {'_id': 'slide-1', 'title': 'Project', 'content': '<p>Details</p>', 'link': '/work/', 'image_id': {'id': 7}, 'template_id': 12, 'width': 240}
        original = {'label': 'Projects', 'items': [row], 'gallery': [{'id': 21}], 'duration': 500, 'rm_motion_parallax_start_custom': 'top bottom-=25%'}
        result = translated('rdct-carousel', original)
        self.assertEqual(len(strings('rdct-carousel', original)), 4)
        self.assertEqual(result['items'][0]['title'], 'Translated Project')
        self.assertEqual(result['items'][0]['content'], 'Translated <p>Details</p>')
        self.assertEqual(result['items'][0]['link'], '/en/work/')
        for key in ['_id', 'image_id', 'template_id', 'width']:
            self.assertEqual(result['items'][0][key], row[key])
        for key in ['gallery', 'duration', 'rm_motion_parallax_start_custom']:
            self.assertEqual(result[key], original[key])
        self.assertEqual(original['items'][0]['title'], 'Project')

    def test_atomic_carousel_updates_only_string_leaves(self):
        row = prop('rdct-carousel-item', {'title': prop('string', 'Project'), 'content': prop('escaped-html', '<p>Details</p>'), 'link': prop('string', '/work/'), 'image_id': prop('number', 7), 'template_id': prop('number', 12), 'width': prop('number', 240)})
        original = {'label': prop('string', 'Projects'), 'items': prop('rdct-carousel-items', [row]), 'mode': prop('string', 'slider')}
        result = translated('rdct-atomic-carousel', original)
        self.assertEqual(len(strings('rdct-atomic-carousel', original)), 4)
        self.assertEqual(result['items']['value'][0]['value']['content']['$$type'], 'escaped-html')
        self.assertEqual(result['items']['value'][0]['value']['link']['value'], '/en/work/')
        self.assertEqual(result['items']['$$type'], 'rdct-carousel-items')
        for key in ['image_id', 'template_id', 'width']:
            self.assertEqual(result['items']['value'][0]['value'][key], row['value'][key])
        self.assertEqual(result['mode'], original['mode'])

    def test_sticky_modes_preserve_templates_and_extract_only_content(self):
        for mode in ['sections', 'stack', 'overlap', 'services', 'zoom', 'split']:
            row = prop('rdct-sticky-section-item', {'title': prop('string', 'Service'), 'content': prop('escaped-html', '<p>Details</p>'), 'template_id': prop('number', 12)})
            original = {'aside_content': prop('escaped-html', '<p>Fixed</p>'), 'items': prop('rdct-sticky-section-items', [row]), 'aside_template_id': prop('number', 14)}
            name = 'rdct-atomic-sticky-' + mode
            result = translated(name, original)
            self.assertEqual(len(strings(name, original)), 3)
            self.assertEqual(result['aside_template_id'], original['aside_template_id'])
            self.assertEqual(result['items']['value'][0]['value']['template_id'], row['value']['template_id'])

    def test_missing_or_non_string_fields_are_not_extracted(self):
        self.assertEqual(strings('rdct-carousel', {'items': []}), [])
        self.assertEqual(strings('rdct-atomic-carousel', {'label': prop('string', None), 'items': prop('rdct-carousel-items', [prop('rdct-carousel-item', {'title': prop('number', 3)})])}), [])

    def test_widget_names_are_unique_and_rich_box_text_uses_visual_editor(self):
        names = [node.attrib['name'] for node in CONFIG.findall('./elementor-widgets/widget')]
        self.assertEqual(len(names), len(set(names)))
        for name, path in [('rdct-box', 'text'), ('rdct-atomic-box', 'text>value')]:
            field = next(field for field in widget(name).findall('./fields/field') if field.text == path)
            self.assertEqual(field.attrib['editor_type'], 'VISUAL')


if __name__ == '__main__':
    unittest.main()
