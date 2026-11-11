<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
define( 'WP_ADMIN', true );
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/wp-admin/post.php?action=elementor';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';
wp_set_current_user( 1 );
wp_enqueue_media();
remove_all_actions( 'wp_footer' );
wp_enqueue_media();
ob_start();
do_action( 'wp_footer' );
if ( str_contains( ob_get_clean(), 'id="tmpl-uploader-editor"' ) ) {
	throw new RuntimeException( 'Missing-template failure was not reproduced.' );
}
add_action( 'wp_footer', 'wp_print_media_templates' );
do_action( 'elementor/editor/after_enqueue_scripts' );
add_action( 'wp_footer', static function (): void { do_action( 'elementor/editor/footer' ); } );
ob_start();
do_action( 'wp_footer' );
do_action( 'elementor/editor/footer' );
$html = ob_get_clean();
if ( 1 !== substr_count( $html, 'id="tmpl-uploader-editor"' ) || 1 !== substr_count( $html, 'id="tmpl-media-frame"' ) ) {
	throw new RuntimeException( 'Native media template counts: uploader=' . substr_count( $html, 'id="tmpl-uploader-editor"' ) . ', frame=' . substr_count( $html, 'id="tmpl-media-frame"' ) . ', print actions=' . did_action( 'print_media_templates' ) );
}
echo "Passed: real WordPress media enqueue/reset reproduces missing templates; dedicated Elementor footer prints native templates once and suppresses duplicate generic footer printing.\n";
