<?php
/**
 * Plugin Name: Store Radman Local Boundaries
 */
defined('ABSPATH') || exit;
if ('1' !== getenv('STORE_LOCAL') || 'local' !== wp_get_environment_type()) {
    return;
}
add_filter('pre_wp_mail', '__return_false');
add_filter('pre_option_blog_public', static fn() => '0');
add_filter('automatic_updater_disabled', '__return_true');
add_filter('woocommerce_allow_tracking', '__return_false');
add_action('send_headers', static function (): void {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
});
add_filter('pre_http_request', static function ($result, $args, $url) {
    $host = wp_parse_url($url, PHP_URL_HOST);
    if (in_array($host, ['127.0.0.1', 'localhost', 'wordpress'], true)) {
        return $result;
    }
    if ('https' === wp_parse_url($url, PHP_URL_SCHEME) && in_array($host, ['api.wordpress.org', 'downloads.wordpress.org', 'translations.wordpress.org'], true)) {
        return $result;
    }
    return new WP_Error('store_local_external', 'External services are disabled in this local store.');
}, 10, 3);
