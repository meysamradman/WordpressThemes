<?php
namespace RadmanCT\Content\Projects;
defined( 'ABSPATH' ) || exit;
function register(): void {
    register_post_type( 'project', array(
        'labels' => array( 'name' => __( 'Projects', 'radmanct' ), 'singular_name' => __( 'Project', 'radmanct' ) ),
        'public' => true, 'show_in_rest' => true, 'has_archive' => 'projects', 'rewrite' => array( 'slug' => 'projects', 'with_front' => false ),
        'menu_icon' => 'dashicons-portfolio', 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
    ) );
    register_taxonomy( 'project_category', 'project', array(
        'labels' => array( 'name' => __( 'Project categories', 'radmanct' ), 'singular_name' => __( 'Project category', 'radmanct' ) ),
        'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => array( 'slug' => 'project-category', 'with_front' => false ),
    ) );
}
add_action( 'init', __NAMESPACE__ . '\register' );
add_action('after_switch_theme',static function():void{register();flush_rewrite_rules();});

function year(string $value): string {
    $value = strtr(trim($value), array('۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'));
    return preg_match('/^[1-9][0-9]{3}$/D', $value) ? $value : '';
}

function website(string $value): string {
    $value = esc_url_raw(trim($value), array('http', 'https'));
    $parts = wp_parse_url($value);
    return is_array($parts) && !empty($parts['host']) && in_array($parts['scheme'] ?? '', array('http', 'https'), true) ? $value : '';
}

function fields(\WP_Post $post): void {
    $meta = get_post_meta($post->ID, '_webtalik_project', true);
    if (!is_array($meta)) { $meta = array(); }
    wp_nonce_field('rdc-prj-info', 'rdct_project_info_nonce');
    $values = array('client'=>$meta['client'] ?? '', 'website'=>$meta['website'] ?? $meta['url'] ?? '', 'year'=>$meta['year'] ?? '');
    $labels = array('client'=>__('Website / client name', 'radmanct'), 'website'=>__('Website URL', 'radmanct'), 'year'=>__('Year built', 'radmanct'));
    foreach ($labels as $key=>$label) {
        $value = is_scalar($values[$key]) ? (string)$values[$key] : '';
        echo '<p><label for="rdc-prj-'.esc_attr($key).'"><strong>'.esc_html($label).'</strong></label><br>';
        echo '<input class="widefat" id="rdc-prj-'.esc_attr($key).'" name="rdct_project_info['.esc_attr($key).']" type="'.($key === 'website' ? 'url' : 'text').'" value="'.esc_attr($value).'"';
        if ($key === 'website') { echo ' dir="ltr" placeholder="https://example.com"'; }
        if ($key === 'year') { echo ' inputmode="numeric" maxlength="4" placeholder="۱۴۰۵ / 2026"'; }
        echo '></p>';
    }
    echo '<p class="description">'.esc_html__('Use the title above for the project name, the main editor for full details, and the excerpt for the short card description. The year accepts Persian or Latin digits; fields are optional.', 'radmanct').'</p>';
}

add_action('add_meta_boxes_project', static function(): void {
    add_meta_box('rdc-prj-info', __('Project information', 'radmanct'), __NAMESPACE__.'\\fields', 'project', 'normal', 'high');
});

add_action('save_post_project', static function(int $id): void {
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($id) || !current_user_can('edit_post', $id)) { return; }
    $nonce = $_POST['rdct_project_info_nonce'] ?? null;
    $input = $_POST['rdct_project_info'] ?? null;
    if (!is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'rdc-prj-info') || !is_array($input)) { return; }
    $meta = get_post_meta($id, '_webtalik_project', true);
    if (!is_array($meta)) { $meta = array(); }
    foreach (array('client', 'website', 'year') as $key) {
        if (!isset($input[$key]) || !is_string($input[$key])) { continue; }
        $value = wp_unslash($input[$key]);
        $value = $key === 'website' ? website($value) : ($key === 'year' ? year($value) : sanitize_text_field($value));
        $stored_key = $key === 'website' && !array_key_exists('website', $meta) && array_key_exists('url', $meta) ? 'url' : $key;
        $meta[$stored_key] = $value;
    }
    update_post_meta($id, '_webtalik_project', $meta);
});
