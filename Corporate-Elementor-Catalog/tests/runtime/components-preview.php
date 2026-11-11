<?php
if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
register_shutdown_function( static function (): void { $error = error_get_last(); if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR ), true ) ) { fwrite( STDERR, "PREVIEW_FATAL: " . json_encode( $error ) . "\n" ); } } );
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/?elementor-preview=1';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
wp_set_current_user( 1 );
$plugin = \Elementor\Plugin::$instance;
$plugin->widgets_manager->get_widget_types();
require_once '/var/www/html/wp-content/plugins/radmanct-components/inc/components/library.php';
$cases = array( 'classic-table' => \RadmanCTComponents\Components\Library\model( 'table-general', 'fa', 'classic' ), 'classic-icon' => \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'classic' ) );
$cases['classic-icon-box'] = \RadmanCTComponents\Components\Library\model( 'icon-box-service', 'fa', 'classic' );
$cases['classic-icon-box']['elements'][0]['settings']['icon_size_responsive_mobile'] = array( 'size' => 19, 'unit' => 'px' );
$atomic = \RadmanCTComponents\Components\Library\atomic_available();
fwrite( STDERR, 'Atomic preview availability: ' . json_encode( array( 'feature' => \Elementor\Modules\AtomicWidgets\Module::is_active(), 'icon_class' => class_exists( '\\RadmanCTComponents\\Elementor\\AtomicComponents\\Icon', false ), 'available' => $atomic ) ) . "\n" );
if ( $atomic ) {
	$cases['atomic-table'] = \RadmanCTComponents\Components\Library\model( 'table-general', 'fa', 'atomic' );
	$cases['atomic-icon-box'] = \RadmanCTComponents\Components\Library\model( 'icon-box-service', 'fa', 'atomic' );
	$cases['atomic-icon-box']['elements'][0]['settings']['icon_size_responsive'] = \RadmanCTComponents\Elementor\AtomicComponents\ResponsiveValues::generate( array( 'mobile' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 19 ) ) );
	$cases['atomic-image'] = \RadmanCTComponents\Components\Library\model( 'image-reveal', 'fa', 'atomic' );
}
$ids = static function ( array $element ) use ( &$ids ): array { $element['id'] = substr( md5( wp_generate_uuid4() ), 0, 7 ); $element['elements'] = array_map( $ids, $element['elements'] ?? array() ); return $element; };
foreach ( $cases as $name => $model ) {
	fwrite( STDERR, 'Rendering preview case: ' . $name . "\n" );
	if ( ! $model ) { throw new RuntimeException( 'Missing preview model: ' . $name ); }
	$document = $plugin->documents->create( 'wp-page', array( 'post_title' => $name, 'post_status' => 'publish' ) );
	$document->save( array( 'elements' => array( $ids( $model ) ) ) );
	if ( 'classic-table' === $name ) {
		update_option( 'radman_builder_header', array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'preview-row', 'items' => array( array( 'id' => 'preview-contact', 'type' => 'contact', 'text' => 'Call', 'icon' => 'phone', 'url' => 'tel:123' ) ) ) ) ), false );
		set_theme_mod( 'radman_builder_header', true );
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = new WP_Query( array( 'page_id' => $document->get_main_id() ) );
		$GLOBALS['post'] = get_post( $document->get_main_id() ); setup_postdata( $GLOBALS['post'] );
		$_GET['elementor-preview'] = (string) $document->get_main_id();
		$plugin->preview->init();
		ob_start(); get_header(); $head = ob_get_clean();
		if ( ! str_contains( $head, '<head>' ) ) { throw new RuntimeException( 'Theme preview head did not render.' ); }
		fwrite( STDERR, "Theme preview header with contact icon rendered.\n" );
	}
	ob_start(); $html = $plugin->frontend->get_builder_content( $document->get_main_id(), true ); $output = ob_get_clean();
	if ( ! $html ) { throw new RuntimeException( 'Empty native render: ' . $name . ' ' . $output ); }
	if ( str_contains( $name, 'table' ) && ! str_contains( $html, 'rdct-tb' ) ) { throw new RuntimeException( 'Native table renderer did not produce table markup: ' . $name ); }
	if ( str_contains( $name, 'icon-box' ) && ( ! str_contains( $html, 'data-rdct-responsive' ) || ! str_contains( $html, 'inline-size:19px' ) ) ) { throw new RuntimeException( 'Responsive override missing: ' . $name ); }
	fwrite( STDERR, 'Native preview render passed: ' . $name . "\n" );
	echo 'Rendered: ' . $name . "\n";
}
