<?php
if ( PHP_SAPI !== 'cli' || '1' !== getenv( 'WEBTALIK_LOCAL_SITE' ) ) { exit( 1 ); }
define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST'] = '127.0.0.1:8095';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
if ( is_blog_installed() ) {
    wp_installing( false );
    if ( ! get_option( 'webtalik_local_provisioned' ) ) { throw new RuntimeException( 'Existing database is not owned by this Webtalik installation. No data changed.' ); }
    echo "Existing Webtalik site preserved; no content, passwords or settings reset.\n";
    exit;
}
$password = getenv( 'WEBTALIK_ADMIN_PASSWORD' );
if ( ! is_string( $password ) || strlen( $password ) < 24 ) { throw new RuntimeException( 'Missing local administrator password.' ); }
$result = wp_install( 'Webtalik', getenv( 'WEBTALIK_ADMIN_USER' ), 'radman@localhost.invalid', false, '', $password, 'fa_IR' );
if ( empty( $result['user_id'] ) ) { throw new RuntimeException( 'WordPress installation failed.' ); }
wp_installing( false );
wp_cache_flush();
wp_set_current_user( $result['user_id'] );
update_user_meta( $result['user_id'], 'locale', 'fa_IR' );
update_option( 'WPLANG', 'fa_IR' );
update_option( 'timezone_string', 'Asia/Tehran' );
switch_theme( 'radmanct' );
require_once get_template_directory().'/inc/content/projects.php';
\RadmanCT\Content\Projects\register();
foreach ( get_posts( array( 'post_type' => array( 'post', 'page' ), 'post_status' => 'any', 'numberposts' => -1 ) ) as $sample ) { wp_delete_post( $sample->ID, true ); }
$ids = array();
foreach ( array( 'home' => 'خانه', 'blog' => 'وبلاگ', 'contact' => 'تماس با ما' ) as $slug => $title ) {
    $id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => '' ), true );
    if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
    $ids[ $slug ] = $id;
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $ids['home'] );
update_option( 'page_for_posts', $ids['blog'] );
$menu = wp_create_nav_menu( 'Primary' );
if ( is_wp_error( $menu ) ) { throw new RuntimeException( $menu->get_error_message() ); }
foreach ( array( 'home', 'blog', 'contact' ) as $slug ) {
    wp_update_nav_menu_item( $menu, 0, array( 'menu-item-object-id' => $ids[$slug], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
    if ( 'home' === $slug ) { wp_update_nav_menu_item( $menu, 0, array( 'menu-item-title' => 'نمونه‌کارها', 'menu-item-url' => home_url( '/projects/' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) ); }
}
set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu ) );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( true );
update_option( 'webtalik_local_provisioned', 1, false );
echo "Fresh independent Webtalik database installed. No Elementor, Child, builders or products.\n";
