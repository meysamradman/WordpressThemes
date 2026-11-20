<?php
if (PHP_SAPI !== 'cli') exit(1);

require '/var/www/html/wp-load.php';
if (home_url() !== 'http://127.0.0.1:8097' || !get_option('rdl_local_ready') || get_stylesheet() !== 'radman-library') {
    throw new RuntimeException('Wrong destination.');
}

require_once ABSPATH . 'wp-admin/includes/image.php';
$fixtureDir = '/opt/library/fixtures/process-rows/';
$files = json_decode(file_get_contents($fixtureDir . 'media.json'), true, flags: JSON_THROW_ON_ERROR);
$media = [];
foreach ($files as $key => $name) {
    if (basename($name) !== $name || !is_file($fixtureDir . $name)) {
        throw new RuntimeException('Missing process fixture.');
    }
    $reference = 'webtalik/service/process/' . $name;
    $ids = get_posts([
        'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids',
        'posts_per_page' => 1, 'meta_key' => '_rdl_reference_url', 'meta_value' => $reference,
    ]);
    if ($ids && is_file(get_attached_file($ids[0]))) {
        $media[$key] = $ids[0];
        continue;
    }
    $upload = wp_upload_bits('process-' . $name, null, file_get_contents($fixtureDir . $name));
    if ($upload['error']) throw new RuntimeException($upload['error']);
    $id = wp_insert_attachment([
        'post_title' => pathinfo($name, PATHINFO_FILENAME),
        'post_status' => 'inherit',
        'post_mime_type' => 'image/webp',
    ], $upload['file'], 0, true);
    if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
    $metadata = wp_generate_attachment_metadata($id, $upload['file']);
    if (empty($metadata['width'])) throw new RuntimeException('Missing image metadata.');
    wp_update_attachment_metadata($id, $metadata);
    update_post_meta($id, '_rdl_reference_url', $reference);
    $media[$key] = $id;
}
update_option('rdl_process_rows_media', $media, false);

foreach (['portfolio-rows' => 'ردیف نمونه‌کار', 'process-rows' => 'مراحل تصویری'] as $slug => $label) {
    $module = RadmanLibrary\modules()[$slug];
    $family = get_term_by('slug', $module['group'], 'rdl_group');
    if (!$family) throw new RuntimeException('Missing parent category.');
    $subgroup = get_term_by('slug', $module['subgroup'], 'rdl_group');
    if (!$subgroup) {
        $term = wp_insert_term($label, 'rdl_group', ['slug' => $module['subgroup'], 'parent' => (int) $family->term_id]);
        if (is_wp_error($term)) throw new RuntimeException($term->get_error_message());
    } elseif ((int) $subgroup->parent !== (int) $family->term_id) {
        throw new RuntimeException('Existing category has a different parent.');
    }

    $post = get_page_by_path($slug, OBJECT, 'rdl_design');
    if ($post && get_post_meta($post->ID, '_rdl_module', true) !== $slug) {
        throw new RuntimeException('Design slug already belongs to another module.');
    }
    $id = $post ? $post->ID : wp_insert_post([
        'post_type' => 'rdl_design', 'post_status' => 'publish', 'post_name' => $slug,
        'post_title' => $module['title'], 'post_excerpt' => $module['motion'],
    ], true);
    if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
    update_post_meta($id, '_rdl_module', $slug);
    foreach ([
        'rdl_group' => [$module['group'], $module['subgroup']],
        'rdl_kind' => [$module['kind']],
        'rdl_fx' => $module['effects'],
    ] as $taxonomy => $terms) {
        $result = wp_set_object_terms($id, $terms, $taxonomy);
        if (is_wp_error($result)) throw new RuntimeException($result->get_error_message());
    }
    echo get_permalink($id), PHP_EOL;
}

