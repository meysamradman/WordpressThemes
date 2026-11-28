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
