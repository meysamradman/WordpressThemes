<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
define( 'FS_METHOD', 'direct' );
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/wp-admin/update.php';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once '/workspace/radmanct/inc/admin/plugin-installer.php';
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
wp_set_current_user( 1 );
$GLOBALS['pagenow'] = 'update.php';
$_GET['action'] = 'install-plugin';
$_REQUEST['_wpnonce'] = wp_create_nonce( 'install-plugin_radmanct-content' );
$args = (object) array( 'slug' => 'radmanct-content' );
$api = plugins_api( 'plugin_information', $args );
expect( ! is_wp_error( $api ) && is_file( $api->download_link ) && str_starts_with( $api->download_link, get_template_directory() . '/bundled-plugins/' ), 'Native plugin API did not receive the fixed local Core package.' );
$skin = new WP_Ajax_Upgrader_Skin();
$upgrader = new Plugin_Upgrader( $skin );
$result = $upgrader->install( $api->download_link, array( 'clear_update_cache' => false ) );
expect( true === $result && is_file( WP_PLUGIN_DIR . '/radmanct-content/radmanct-content.php' ), 'WordPress could not install the bundled Core ZIP.' );
expect( is_file( $api->download_link ), 'Native installer removed the original theme bundle.' );
expect( null === activate_plugin( 'radmanct-content/radmanct-content.php', '', false, true ) && is_plugin_active( 'radmanct-content/radmanct-content.php' ), 'Installed Core could not activate through WordPress.' );
expect( array() === \RadmanCTContent\Modules\types(), 'Installing Core silently enabled content modules.' );
$_REQUEST['_wpnonce'] = array( 'invalid' );
expect( is_wp_error( \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', $args ) ), 'Malformed installer nonce exposed the package.' );
wp_set_current_user( 0 );
expect( is_wp_error( \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', $args ) ), 'Unauthorized installer request exposed the package.' );
expect( false === \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', (object) array( 'slug' => 'another-plugin' ) ), 'Installer intercepted an unrelated plugin.' );
echo "Passed: native WordPress local Core ZIP installation and activation, preserved bundle, disabled optional modules and installer nonce/capability boundaries.\n";
