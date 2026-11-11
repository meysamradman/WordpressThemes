<?php
if ( PHP_SAPI !== 'cli' || '1' !== getenv( 'RADMAN_LOCAL_SITE' ) ) { exit( 1 ); }

foreach ( array( 'elementor', 'elementor-pro' ) as $plugin_name ) {
    $destination = '/var/www/html/wp-content/plugins/' . $plugin_name;
    if ( is_file( $destination . '/' . $plugin_name . '.php' ) ) { continue; }
    $staging = '/var/www/html/wp-content/rdct-install-' . $plugin_name;
    $source = '/opt/radman/reference-plugins/' . $plugin_name;
    $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
    foreach ( $iterator as $file ) {
        $target = $staging . '/' . $iterator->getSubPathName();
        if ( $file->isDir() ) {
            if ( ! is_dir( $target ) && ! mkdir( $target, 0775, true ) ) { throw new RuntimeException( 'Cannot create local plugin directory.' ); }
        } elseif ( ! is_file( $target ) ) {
            if ( ! is_dir( dirname( $target ) ) ) { mkdir( dirname( $target ), 0775, true ); }
            if ( ! copy( $file->getPathname(), $target ) ) { throw new RuntimeException( 'Cannot install local plugin file.' ); }
        }
    }
    if ( is_dir( $destination ) && ! rmdir( $destination ) ) { throw new RuntimeException( 'Existing plugin files were preserved; installation needs review.' ); }
    if ( ! rename( $staging, $destination ) ) { throw new RuntimeException( 'Cannot finish local plugin installation.' ); }
    echo 'Installed supplied ' . $plugin_name . " into persistent WordPress storage.\n";
}

define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST'] = '127.0.0.1:8094';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

if ( is_blog_installed() && get_option( 'radman_local_provisioned' ) ) {
    echo "Existing local site preserved; no content, settings, plugins or passwords reset.\n";
    exit;
}

$password = getenv( 'RADMAN_ADMIN_PASSWORD' );
if ( ! is_string( $password ) || strlen( $password ) < 24 ) { throw new RuntimeException( 'Missing local administrator password.' ); }
if ( ! is_blog_installed() ) {
    $result = wp_install( 'Radman CT Local', getenv( 'RADMAN_ADMIN_USER' ), 'radman@localhost.invalid', false, '', $password, 'fa_IR' );
    if ( empty( $result['user_id'] ) ) { throw new RuntimeException( 'WordPress installation failed.' ); }
}
$admin = get_user_by( 'login', getenv( 'RADMAN_ADMIN_USER' ) );
if ( ! $admin ) { throw new RuntimeException( 'The local administrator is missing; existing accounts were not reset.' ); }
wp_set_current_user( $admin->ID );
update_user_meta( $admin->ID, 'locale', 'fa_IR' );
update_option( 'WPLANG', 'fa_IR' );
update_option( 'timezone_string', 'Asia/Tehran' );
update_option( 'radman_core_modules', array( 'catalog' => true, 'portfolio' => true ) );

require_once ABSPATH . 'wp-admin/includes/plugin.php';
foreach ( array( 'radmanct-components/radmanct-components.php', 'radmanct-content/radmanct-content.php', 'radmanct-mcp/radmanct-mcp.php', 'elementor/elementor.php' ) as $plugin ) {
    $activated = activate_plugin( $plugin, '', false, true );
    if ( is_wp_error( $activated ) ) { throw new RuntimeException( $plugin . ': ' . $activated->get_error_message() ); }
}
switch_theme( 'radmanct-child' );

$existing_home = get_page_by_path( 'home' );
$home = $existing_home ? $existing_home->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Home', 'post_content' => '<!-- wp:heading --><h2 class="wp-block-heading">Radman CT</h2><!-- /wp:heading -->' ), true );
if ( is_wp_error( $home ) ) { throw new RuntimeException( $home->get_error_message() ); }
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
if ( ! get_page_by_path( 'elementor-sandbox' ) ) { wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Elementor sandbox', 'post_name' => 'elementor-sandbox' ) ); }
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( true );
update_option( 'radman_local_provisioned', 1, false );
echo 'Installed WordPress ' . get_bloginfo( 'version' ) . ' with Radman CT Child, Components, Content, MCP and Elementor. Elementor Pro is installed but inactive.' . "\n";
