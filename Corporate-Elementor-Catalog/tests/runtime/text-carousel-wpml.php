<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = '127.0.0.1:8887';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
wp_set_current_user( 1 );
\Elementor\Plugin::$instance->widgets_manager->get_widget_types();
function expect( bool $value, string $message ): void { if ( ! $value ) { throw new RuntimeException( $message ); } }
expect( defined( 'ICL_SITEPRESS_VERSION' ) && did_action( 'elementor/loaded' ), 'Real WPML and Elementor must be active.' );
global $sitepress, $wpdb;
if ( 'prepare' === ( $argv[1] ?? '' ) ) {
	$wpdb->query( "UPDATE {$wpdb->prefix}icl_languages SET active = IF(code IN ('en','fa'),1,0)" );
	$sitepress->set_setting( 'setup_complete', true );
	$sitepress->set_setting( 'existing_content_language_verified', 1 );
	$sitepress->set_setting( 'language_negotiation_type', 3 );
	$sitepress->set_setting( 'custom_posts_sync_option', array( 'page' => 1, 'attachment' => 1, 'elementor_library' => 1 ) );
	$sitepress->set_default_language( 'en' );
	$sitepress->save_settings();
	$sitepress->get_active_languages( true );
	$uploads = wp_upload_dir();
	$attachments = array();
	foreach ( array( 'en', 'fa' ) as $language ) {
		$file = $uploads['path'] . '/review-' . $language . '.png';
		copy( '/workspace/radmanct/screenshot.png', $file );
		$id = wp_insert_attachment( array( 'post_title' => 'Review ' . $language, 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ), $file );
		wp_update_attachment_metadata( $id, array( 'width' => 1200, 'height' => 900, 'file' => _wp_relative_upload_path( $file ) ) );
		$attachments[ $language ] = $id;
		do_action( 'wpml_set_element_language_details', array( 'element_id' => $id, 'element_type' => 'post_attachment', 'trid' => 'en' === $language ? false : apply_filters( 'wpml_element_trid', null, $attachments['en'], 'post_attachment' ), 'language_code' => $language, 'source_language_code' => 'en' === $language ? null : 'en' ) );
	}
	$ids = array();
	require_once WP_PLUGIN_DIR . '/radmanct-components/inc/components/library.php';
	$random_ids = static function ( array $model ) use ( &$random_ids ): array { $model['id'] = substr( md5( wp_generate_uuid4() ), 0, 7 ); $model['elements'] = array_map( $random_ids, $model['elements'] ?? array() ); return $model; };
	foreach ( array( 'en', 'fa' ) as $language ) {
		do_action( 'wpml_switch_language', $language );
		$models = array();
		$text = \RadmanCTComponents\Components\Library\model( 'text-reveal-scroll', $language, 'classic' );
		$text['elements'][0]['settings'] = array( 'mode' => 'rotate', 'text' => 'en' === $language ? 'Our ' : 'Review ', 'tag' => 'h2', 'direction' => $language === 'fa' ? 'rtl' : 'ltr', 'items' => array( array( 'title' => 'Design' ), array( 'title' => 'Development' ) ), 'highlight' => 'brush', 'highlight_height_responsive' => array( 'size' => .3, 'unit' => 'px' ), 'highlight_height_unit_responsive' => 'em', 'highlight_height_unit_responsive_mobile' => 'rem' );
		$models[] = $random_ids( $text );
		$atomic = \RadmanCTComponents\Components\Library\model( 'text-reveal-scroll', $language, 'atomic' );
		expect( ! empty( $atomic ), 'Atomic Text sample unavailable.' );
		$atomic['elements'][0]['settings']['mode'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'static' );
		$atomic['elements'][0]['settings']['text'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'Local [[highlight]] ' . $language );
		$atomic['elements'][0]['settings']['highlight'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'brush' );
		$atomic['elements'][0]['settings']['highlight_height_responsive'] = \RadmanCTComponents\Elementor\AtomicComponents\ResponsiveValues::generate( array( 'desktop' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( .3 ), 'mobile' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( .5 ) ) );
		$atomic['elements'][0]['settings']['highlight_height_unit_responsive'] = \RadmanCTComponents\Elementor\AtomicComponents\ResponsiveValues::generate( array( 'desktop' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'em' ) ) );
		$models[] = $random_ids( $atomic );
		foreach ( array( 'source', 'target' ) as $kind ) {
			$model = \RadmanCTComponents\Components\Library\model( 'carousel-cards', $language, 'classic' );
			$model['elements'][0]['settings'] = array( 'controller_id' => $kind, 'controlled_ids' => 'source' === $kind ? 'target' : '', 'sync_mode' => 'progress', 'slides' => 2, 'slides_tablet' => 2, 'slides_mobile' => 1, 'rewind' => false, 'items' => array_map( static fn( $index ) => array( 'title' => $kind . ' ' . $index, 'content' => '<p>Local content ' . $language . '</p>' ), range( 1, 5 ) ) );
			$models[] = $random_ids( $model );
		}
		$document = \Elementor\Plugin::$instance->documents->create( 'wp-page', array( 'post_title' => 'Text Carousel Review ' . $language, 'post_status' => 'publish' ) );
		$document->save( array( 'elements' => $models ) );
		$id = $document->get_main_id(); $ids[ $language ] = $id;
		update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
		do_action( 'wpml_set_element_language_details', array( 'element_id' => $id, 'element_type' => 'post_page', 'trid' => 'en' === $language ? false : apply_filters( 'wpml_element_trid', null, $ids['en'], 'post_page' ), 'language_code' => $language, 'source_language_code' => 'en' === $language ? null : 'en' ) );
	}
	update_option( 'rdct_qa_text_carousel', array( 'posts' => $ids, 'attachments' => $attachments ) );
	echo 'Created local fixtures: ' . wp_json_encode( $ids ) . ".\n";
	exit;
}
$data = get_option( 'rdct_qa_text_carousel' );
foreach ( array( 'en', 'fa' ) as $language ) {
	do_action( 'wpml_switch_language', $language );
	expect( (int) apply_filters( 'wpml_object_id', $data['posts']['en'], 'page', false, $language ) === $data['posts'][ $language ], 'WPML page mapping failed.' );
	expect( (int) apply_filters( 'wpml_object_id', $data['attachments']['en'], 'attachment', false, $language ) === $data['attachments'][ $language ], 'WPML attachment mapping failed.' );
	$id = $data['posts'][ $language ];
	$document = \Elementor\Plugin::$instance->documents->get( $id );
	$elements = $document->get_elements_data();
	expect( count( $elements ) === 4, 'Elementor save/reopen lost elements.' );
	expect( .3 === $elements[0]['elements'][0]['settings']['highlight_height_responsive']['size'], 'Classic decimal lost on persistence.' );
	expect( 'em' === $elements[0]['elements'][0]['settings']['highlight_height_unit_responsive'], 'Classic unit lost on persistence.' );
	expect( 'progress' === $elements[2]['elements'][0]['settings']['sync_mode'], 'Carousel sync method lost on persistence.' );
	$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id );
	expect( str_contains( $html, '--rdct-text-highlight-height:0.3em;' ) && str_contains( $html, '--rdct-text-highlight-height:0.5em;' ), 'Classic/Atomic responsive Text did not render in WordPress.' );
	expect( substr_count( $html, 'data-rdct-carousel data-options=' ) === 2 && str_contains( $html, '&quot;sync_mode&quot;:&quot;progress&quot;' ), 'Carousel options missing from actual Elementor output.' );
	$mapped = \RadmanCTComponents\Components\Carousel\render( array( array( 'title' => 'Media', 'image_id' => $data['attachments']['en'] ) ), array() );
	expect( str_contains( $mapped, 'review-' . $language . '.png' ), 'Carousel did not use translated local media.' );
}
echo 'Passed real WordPress/Elementor persistence/rendering and WPML ' . ICL_SITEPRESS_VERSION . " linked pages/local media in en/fa.\n";
