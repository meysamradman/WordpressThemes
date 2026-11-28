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
add_filter('http_request_args', static function (array $args, string $url): array {
    $site = wp_parse_url(getenv('STORE_SITE_URL'));
    $target = wp_parse_url($url);
    if (($target['scheme'] ?? '') === 'https'
        && ($target['host'] ?? '') === ($site['host'] ?? '')
        && ($target['port'] ?? 443) === ($site['port'] ?? 443)) {
        $args['sslcertificates'] = '/usr/local/share/ca-certificates/store-radman-local.crt';
        $args['sslverify'] = true;
    }
    return $args;
}, 10, 2);
add_action('send_headers', static function (): void {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
});
