<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
define( 'WP_ADMIN', true );
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/wp-admin/post.php?action=elementor';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
fwrite( STDERR, "Reached WordPress load.\n" );
require_once ABSPATH . 'wp-admin/includes/admin.php';
wp_raise_memory_limit( 'admin' );
fwrite( STDERR, "Reached admin includes.\n" );
wp_set_current_user( 1 );
fwrite( STDERR, "Reached current user.\n" );
$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Editor startup QA' ) );
fwrite( STDERR, "Reached page creation.\n" );
$_GET['action'] = $_REQUEST['action'] = 'elementor';
$_GET['post'] = $_REQUEST['post'] = $id;
$GLOBALS['pagenow'] = 'post.php';
set_current_screen( 'post' );
foreach ( array( 'elementor/editor/init', 'elementor/editor/before_enqueue_scripts', 'elementor/editor/scripts/enqueue/before', 'elementor/editor/scripts/enqueue', 'elementor/editor/scripts/enqueue/after', 'elementor/editor/after_enqueue_scripts', 'elementor/editor/after_enqueue_styles', 'elementor/editor/footer' ) as $hook ) {
	add_action( $hook, static function () use ( $hook ): void { fwrite( STDERR, "Reached: $hook\n" ); }, PHP_INT_MAX );
}
fwrite( STDERR, "Starting native editor HTML.\n" );
set_time_limit( 8 );
ob_start();
\Elementor\Plugin::$instance->editor->init( false );
$html = ob_get_clean();
foreach ( array( 'elementor-editor-loader-js', 'elementor-editor-js', 'elementor-editor-css', 'ElementorConfig', 'tmpl-uploader-editor' ) as $marker ) {
	if ( ! str_contains( $html, $marker ) ) { throw new RuntimeException( 'Missing native editor output: ' . $marker ); }
}
if ( ! \RadmanCTComponents\Components\Library\atomic_available() ) { throw new RuntimeException( 'Native Atomic sample coverage was not enabled.' ); }
if ( str_contains( $html, 'data-library-models' ) || str_contains( $html, 'data-library-card=' ) ) { throw new RuntimeException( 'Editor startup still builds the component library.' ); }
$widgets = \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
foreach ( array( 'radman-icon', 'radman-text-reveal' ) as $name ) {
	if ( ! isset( $widgets[ $name ] ) || ! $widgets[ $name ]->show_in_panel() ) { throw new RuntimeException( 'Classic widget was removed or hidden while Atomic is active.' ); }
}
foreach ( array_keys( \RadmanCTComponents\Components\Library\catalog() ) as $sample ) {
	foreach ( array( 'fa', 'en' ) as $language ) {
		foreach ( array( 'classic', 'atomic' ) as $mode ) {
			if ( ! \RadmanCTComponents\Components\Library\model( $sample, $language, $mode ) ) { throw new RuntimeException( 'Invalid editor sample: ' . $sample . '/' . $language . '/' . $mode ); }
		}
	}
}
echo "Passed: complete native Elementor editor HTML includes scripts, styles, config and media templates.\n";
echo "Passed: all classic and Atomic library samples are generated in both languages after native widget schema initialization.\n";
define( 'DOING_AJAX', true );
add_filter( 'wp_die_ajax_handler', static fn() => static function ( $message ): void { throw new RuntimeException( '-1' === (string) $message ? 'qa_nonce_end' : 'qa_json_end' ); } );
function library_request( int $user, string $nonce ): array {
	wp_set_current_user( $user );
	$_POST['post_id'] = $GLOBALS['id'];
	$_REQUEST['_ajax_nonce'] = $nonce;
	ob_start();
	try { do_action( 'wp_ajax_radman_component_library' ); }
	catch ( RuntimeException $error ) {
		if ( ! in_array( $error->getMessage(), array( 'qa_json_end', 'qa_nonce_end' ), true ) ) { ob_end_clean(); throw $error; }
		$output = ob_get_clean();
		return array( 'end' => $error->getMessage(), 'data' => json_decode( $output, true ) );
	}
	ob_end_clean();
	throw new RuntimeException( 'Library route did not terminate.' );
}
$nonce = wp_create_nonce( 'radman_component_library' );
$response = library_request( 1, $nonce );
if ( empty( $response['data']['success'] ) || ! str_contains( $response['data']['data']['html'] ?? '', 'data-library-models' ) ) { throw new RuntimeException( 'Lazy library route did not return its models.' ); }
if ( 'qa_nonce_end' !== library_request( 1, 'invalid' )['end'] || false !== library_request( 0, $nonce )['data']['success'] ) { throw new RuntimeException( 'Library permission or nonce boundary failed.' ); }
echo "Passed: panel deduplication, empty initial library shell and native lazy library AJAX permission/nonce boundaries.\n";
