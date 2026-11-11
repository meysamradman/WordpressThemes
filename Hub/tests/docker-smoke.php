<?php
$_SERVER['HTTP_HOST'] = 'localhost:8096';
$_SERVER['REQUEST_METHOD'] = 'GET';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_set_current_user(1);
$checks = 0;
function hub_check($condition, $message) {
    if (!$condition) { throw new Exception($message); }
    $GLOBALS['checks']++;
}
hub_check(get_template() === 'hub', 'Hub must be active');
hub_check(defined('ELEMENTOR_VERSION') && class_exists('Liquid_Elementor_Addons'), 'Elementor and Hub widgets must load');
$widgets = Elementor\Plugin::$instance->widgets_manager->get_widget_types();
hub_check(count($widgets) > 40, 'Widget registry must initialize');
$source = Elementor\Plugin::$instance->templates_manager->get_source('hub_collections');
hub_check($source instanceof HUB_Source, 'Original Hub collection source must be registered');
$local = Elementor\Plugin::$instance->templates_manager->get_source('local');
$content = array(array('id' => 'abc1234', 'elType' => 'container', 'settings' => array(), 'elements' => array(
    array('id' => 'def1234', 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => array('title' => 'Hub Local Smoke'), 'elements' => array())
)));
$template = $local->save_item(array('title' => 'Hub Local Smoke Template', 'type' => 'page', 'content' => $content));
hub_check(!is_wp_error($template) && is_int($template) && $template > 0, 'Native saved template must save');
$template_id = $template;
$saved = $local->get_data(array('template_id' => $template_id));
hub_check(!is_wp_error($saved) && !empty($saved['content']), 'Native saved template must load');
$previous_library = get_option(Hub_Templates_Lib::LIBRARY_OPTION_KEY, null);
$previous_status = get_option('hub_purchase_code_status', null);
$cache_key = 'hub_templates_data_' . LD_ELEMENTOR_VERSION;
$previous_cache = get_transient($cache_key);
update_option('hub_purchase_code_status', 'invalid');
delete_transient($cache_key);
$calls = array();
$mock = function($pre, $args, $url) use (&$calls, $content) {
    if (strpos($url, Hub_Templates_Lib::$api_url) !== 0) { return $pre; }
    $calls[] = array('url' => $url, 'args' => $args);
    $body = substr($url, -3) === '/42' ? array('content' => $content, 'page_settings' => array()) : array('library' => array(
        'types_data' => array('page' => array(), 'block' => array()),
        'templates' => array(array('id' => 42, 'type' => 'page', 'subtype' => '', 'title' => 'Fixture', 'thumbnail' => '', 'img_width' => 400, 'img_height' => 300, 'tmpl_created' => 1, 'url' => 'http://localhost:8096', 'alert' => ''))
    ));
    return array('headers' => array(), 'body' => wp_json_encode($body), 'response' => array('code' => 200, 'message' => 'OK'), 'cookies' => array());
};
add_filter('pre_http_request', $mock, 1, 3);
try {
    $catalog = Hub_Templates_Lib::hub_get_library_data(array('sync' => true));
    hub_check(count($catalog['templates']) === 1 && $catalog['templates'][0]['template_id'] === 42, 'Original collection catalog must parse');
    $data = $source->get_data(array('template_id' => 42, 'editor_post_id' => $template_id));
    hub_check(!is_wp_error($data) && !empty($data['content']), 'Hub collection insert must work with invalid/missing license');
    hub_check(count($calls) === 2 && $calls[1]['args']['body']['api_version'] === '6.0.3', 'Original collection API URL/version contract must be preserved');
} finally {
    remove_filter('pre_http_request', $mock, 1);
    if ($previous_library === null) { delete_option(Hub_Templates_Lib::LIBRARY_OPTION_KEY); } else { update_option(Hub_Templates_Lib::LIBRARY_OPTION_KEY, $previous_library); }
    if ($previous_status === null) { delete_option('hub_purchase_code_status'); } else { update_option('hub_purchase_code_status', $previous_status); }
    if ($previous_cache === false) { delete_transient($cache_key); } else { set_transient($cache_key, $previous_cache, HOUR_IN_SECONDS); }
    wp_delete_post($template_id, true);
}
update_option('hub_purchase_code_status', 'invalid');
update_option('hub_purchase_code_domain_revoke', 'Stale migrated license fixture');
echo 'PASS: ' . $checks . ' WordPress/Elementor checks; ' . count($widgets) . ' registered widgets; WordPress ' . get_bloginfo('version') . '; Elementor ' . ELEMENTOR_VERSION . "\n";
