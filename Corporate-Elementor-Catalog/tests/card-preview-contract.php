<?php

namespace RadmanCTTests\CardPreview;

defined( 'ABSPATH' ) || exit;
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once get_template_directory() . '/inc/admin/card-media.php';

function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new \RuntimeException( $message ); } }

$posts = array(); $attachment = 0; $user = get_current_user_id(); $input = $_POST; $get = $_GET;
try {
	$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0]; wp_set_current_user( $admin->ID );
	$temporary = wp_tempnam( 'rdct-card-preview.webm' );
	expect( copy( __DIR__ . '/fixtures/card-preview.webm', $temporary ), 'Local clip fixture is missing.' );
	$attachment = media_handle_sideload( array( 'name' => 'rdct-card-preview-' . wp_generate_uuid4() . '.webm', 'tmp_name' => $temporary ), 0 );
	expect( is_int( $attachment ) && $attachment > 0, 'Local video attachment could not be created.' );
	$native_image = '<img src="/poster.webp" width="640" height="400" alt="Poster">';
	foreach ( array( 0, -1, 12, array( $attachment ), 'wrong' ) as $invalid ) {
		$frame = \RadmanCTComponents\Components\CardPreview\frame( $native_image, array( 'video_id' => $invalid ) );
		expect( $frame['html'] === $native_image && '' === $frame['control'], 'Image-only fallback changed or invalid media was accepted.' );
	}
	expect( ! wp_style_is( 'rdct-card-preview', 'enqueued' ), 'Image-only output loads video assets.' );

	foreach ( \RadmanCT\CardMedia\types() as $type ) {
		$_POST = array();
		$id = wp_insert_post( array( 'post_type' => $type, 'post_status' => 'draft', 'post_title' => 'rdct-card-preview-contract-' . wp_generate_uuid4() ), true );
		expect( is_int( $id ) && $id > 0, 'Temporary post creation failed.' ); $posts[] = $id;
		set_post_thumbnail( $id, 12 );
		$frame = \RadmanCT\CardMedia\frame( $id, $native_image );
		expect( $native_image === $frame['html'] && ! $frame['control'], 'Image is not the default for ' . $type );
		$_POST = array( 'rdct_card_media_nonce' => wp_create_nonce( 'rdct_card_media_' . $id ), 'rdct_card_video_id' => (string) $attachment, 'rdct_card_video_mode' => 'hover' );
		\RadmanCT\Admin\CardMedia\save( $id );
		expect( $attachment === (int) get_post_meta( $id, \RadmanCT\CardMedia\VIDEO_KEY, true ), 'Video selection did not save for ' . $type );
		$frame = \RadmanCT\CardMedia\frame( $id, $native_image );
		expect( str_contains( $frame['html'], 'data-mode="hover"' ) && str_contains( $frame['html'], 'preload="none"' ) && str_contains( $frame['html'], 'muted loop playsinline' ), 'Shared preview behavior is missing.' );
		expect( ! preg_match( '/<video[^>]*\ssrc=/', $frame['html'] ), 'SSR eagerly starts video download.' );
		expect( str_contains( $frame['html'], $native_image ) && str_contains( $frame['control'], 'aria-controls=' ), 'Native poster or accessible separate control was lost.' );
		$second = \RadmanCT\CardMedia\frame( $id, $native_image );
		preg_match( '/<video id="([^"]+)"/', $frame['html'], $first_id ); preg_match( '/<video id="([^"]+)"/', $second['html'], $second_id );
		expect( $first_id[1] !== $second_id[1], 'Repeated cards share a video/control ID.' );
		ob_start(); \RadmanCT\Admin\CardMedia\render( get_post( $id ) ); $editor = ob_get_clean();
		expect( str_contains( $editor, 'value="auto"' ) && str_contains( $editor, 'value="hover" selected' ), 'Native editor does not expose both modes.' );

		$GLOBALS['post'] = get_post( $id ); setup_postdata( $GLOBALS['post'] );
		$files = 'post' === $type ? array( '/template-parts/blog/card.php', '/template-parts/blog/shared/post-card.php', '/template-parts/blog/archive/lead-card.php' ) : array( 'radman_product' === $type ? '/template-parts/catalog/shared/product-card.php' : '/template-parts/portfolio/card.php' );
		foreach ( $files as $file ) {
			$args = array( 'post_id' => $id, 'variant' => 'tile', 'primary' => true );
			ob_start(); include get_stylesheet_directory() . $file; $html = ob_get_clean();
			expect( str_contains( $html, 'data-rdct-card-preview' ) && str_contains( $html, 'data-rdct-preview-control' ), 'Card path does not use shared preview: ' . $file );
			preg_match_all( '/<a\b.*?<\/a>/s', $html, $links );
			foreach ( $links[0] as $link ) { expect( ! str_contains( $link, '<button' ), 'Pause control was nested in a card link.' ); }
		}
		$shortcode = '[rdct_card_preview post_id="' . $id . '"]';
		expect( str_contains( do_shortcode( $shortcode ), 'data-rdct-card-preview' ), 'Native Shortcode bridge is missing.' );
		$_POST['rdct_card_media_nonce'] = 'invalid'; $_POST['rdct_card_video_id'] = '0';
		\RadmanCT\Admin\CardMedia\save( $id );
		expect( $attachment === (int) get_post_meta( $id, \RadmanCT\CardMedia\VIDEO_KEY, true ), 'Invalid nonce removed the preview.' );
		$_POST['rdct_card_media_nonce'] = wp_create_nonce( 'rdct_card_media_' . $id ); $_POST['rdct_card_video_id'] = '12';
		\RadmanCT\Admin\CardMedia\save( $id );
		expect( $attachment === (int) get_post_meta( $id, \RadmanCT\CardMedia\VIDEO_KEY, true ), 'Invalid attachment overwrote the selected video.' );
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$prototype = \Elementor\Plugin::$instance->widgets_manager->get_widget_types( 'shortcode' );
		$class = get_class( $prototype );
		$widget = new $class( array( 'id' => 'abc1234', 'elType' => 'widget', 'widgetType' => 'shortcode', 'settings' => array( 'shortcode' => $shortcode ) ), array() );
		$render = new \ReflectionMethod( $widget, 'render' ); $render->setAccessible( true );
		ob_start(); $render->invoke( $widget ); $html = ob_get_clean();
		expect( str_contains( $html, 'data-rdct-card-preview' ), 'Installed Elementor native Shortcode widget does not render the shared preview.' );
	}
	$_GET['_rdct_fragment'] = 'catalog';
	wp_styles()->done = array_values( array_diff( wp_styles()->done, array( 'rdct-card-preview' ) ) );
	ob_start(); \RadmanCT\CardMedia\frame( $id, $native_image ); \RadmanCT\CardMedia\frame( $id, $native_image ); $html = ob_get_clean();
	expect( 1 === substr_count( $html, "id='rdct-card-preview-css'" ) + substr_count( $html, 'id="rdct-card-preview-css"' ), 'AJAX preview CSS is missing or duplicated.' );
	$_POST = array(); wp_update_post( array( 'ID' => $id, 'post_password' => 'contract-protected' ) );
	$frame = \RadmanCT\CardMedia\frame( $id, $native_image );
	expect( $frame['html'] === $native_image && ! $frame['control'] && '' === do_shortcode( $shortcode ), 'Protected content exposes preview media.' );
	wp_update_post( array( 'ID' => $id, 'post_password' => '' ) );
	wp_set_current_user( 0 );
	$_POST = array( 'rdct_card_media_nonce' => wp_create_nonce( 'rdct_card_media_' . $id ), 'rdct_card_video_id' => '0', 'rdct_card_video_mode' => 'auto' );
	\RadmanCT\Admin\CardMedia\save( $id );
	expect( $attachment === (int) get_post_meta( $id, \RadmanCT\CardMedia\VIDEO_KEY, true ), 'Unauthorized write removed the preview.' );
	echo "Passed: default images, all active content types, native poster, both modes, lazy sources, unique controls, card links, save authorization, AJAX assets and installed Elementor Shortcode rendering.\n";
} finally {
	$_POST = $input; $_GET = $get; wp_set_current_user( $user );
	foreach ( $posts as $id ) { wp_delete_post( $id, true ); }
	if ( is_int( $attachment ) && $attachment > 0 ) { wp_delete_attachment( $attachment, true ); }
	wp_reset_postdata();
}
