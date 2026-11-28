<?php
if ('1' !== getenv('STORE_LOCAL') || 'cli' !== PHP_SAPI) { exit(1); }
require '/var/www/html/wp-load.php';
if (DB_NAME !== 'store_radman_local' || !get_option('store_radman_local_owner')) { throw new RuntimeException('Wrong environment.'); }
require_once ABSPATH.'wp-admin/includes/plugin.php';
require_once ABSPATH.'wp-admin/includes/plugin-install.php';
require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
wp_set_current_user(get_user_by('login', 'radman')->ID);
foreach (['woocommerce'=>'woocommerce/woocommerce.php', 'wordpress-seo'=>'wordpress-seo/wp-seo.php', 'all-in-one-wp-migration'=>'all-in-one-wp-migration/all-in-one-wp-migration.php'] as $slug=>$file) {
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
