<?php
if ('1' !== getenv('STORE_LOCAL') || 'cli' !== PHP_SAPI) { exit(1); }
require '/var/www/html/wp-load.php';
if (DB_NAME !== 'store_radman_local' || !get_option('store_radman_local_owner')) { throw new RuntimeException('Wrong environment.'); }
require_once ABSPATH.'wp-admin/includes/plugin.php';
require_once ABSPATH.'wp-admin/includes/plugin-install.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
wp_set_current_user(get_user_by('login', getenv('STORE_ADMIN_USER') ?: 'radman')->ID);
$snapp_file = 'snapppay-woocommerce-gateway/index.php';
if (!file_exists(WP_PLUGIN_DIR.'/'.$snapp_file)) {
    $result = (new Plugin_Upgrader(new Automatic_Upgrader_Skin()))->install('https://academy.snapppay.ir/wp-content/uploads/2026/03/snapppay-woocommerce-gateway-v1.4.2.zip');
    if (is_wp_error($result) || !$result) { throw new RuntimeException('Install failed: SnappPay'); }
}
echo "SnappPay installed; activation is manual pending docs/wordpress.md compatibility and TLS checks.\n";
foreach (['woocommerce'=>'woocommerce/woocommerce.php', 'wordpress-seo'=>'wordpress-seo/wp-seo.php', 'all-in-one-wp-migration'=>'all-in-one-wp-migration/all-in-one-wp-migration.php', 'zarinpal-woocommerce-payment-gateway'=>'zarinpal-woocommerce-payment-gateway/index.php'] as $slug=>$file) {
    if (is_plugin_active($file)) { echo $slug." already active\n"; continue; }
    if (!file_exists(WP_PLUGIN_DIR.'/'.$file)) {
        $info = plugins_api('plugin_information', ['slug'=>$slug, 'fields'=>['sections'=>false]]);
        if (is_wp_error($info)) { throw new RuntimeException($info->get_error_message()); }
        $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());
        $result = $upgrader->install($info->download_link);
        if (is_wp_error($result) || !$result) { throw new RuntimeException('Install failed: '.$slug); }
    }
    $result = activate_plugin($file);
    if (is_wp_error($result)) { throw new RuntimeException($result->get_error_message()); }
    echo $slug." active\n";
}
