<?php
if ('1' !== getenv('STORE_LOCAL') || 'cli' !== PHP_SAPI) {
    exit(1);
}
define('WP_INSTALLING', true);
$_SERVER['HTTP_HOST'] = '127.0.0.1:8100';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
if (is_blog_installed()) {
    if (!get_option('store_radman_local_owner')) {
        throw new RuntimeException('Existing database is not owned by Store Radman.');
    }
    echo "Existing installation preserved.\n";
    exit;
}
$password = getenv('STORE_ADMIN_PASSWORD');
if (!is_string($password) || strlen($password) < 24) {
    throw new RuntimeException('Missing local credentials.');
}
$result = wp_install('فروشگاه رادمان', 'radman', 'radman@localhost.invalid', false, '', $password, 'fa_IR');
if (empty($result['user_id'])) {
    throw new RuntimeException('Installation failed.');
}
wp_installing(false);
update_option('store_radman_local_owner', 1, false);
update_option('WPLANG', 'fa_IR');
update_option('timezone_string', 'Asia/Tehran');
update_user_meta($result['user_id'], 'locale', 'fa_IR');
echo "Independent Store Radman installed.\n";
