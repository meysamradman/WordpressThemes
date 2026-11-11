<?php
define('WP_INSTALLING', true);
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if (!is_blog_installed()) {
    wp_install('Hub Local', 'hubadmin', 'hubadmin@localhost.test', false, '', getenv('HUB_ADMIN_PASSWORD'));
    update_option('home', 'http://localhost:8096');
    update_option('siteurl', 'http://localhost:8096');
    update_option('blog_public', 0);
}
switch_theme('hub');
foreach (array('elementor/elementor.php', 'hub-core/landing-hub-core.php', 'hub-elementor-addons/hub-elementor-addons.php') as $plugin) {
    $result = activate_plugin($plugin);
    if (is_wp_error($result)) {
        fwrite(STDERR, $result->get_error_message() . "\n");
        exit(1);
    }
}
echo "Hub Local installed; theme and three plugins activated.\n";
