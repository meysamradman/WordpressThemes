<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;

add_action( 'elementor/preview/enqueue_scripts', static function (): void {
	scripts(); wp_enqueue_script( 'rdct-elementor-navigation' );
	wp_add_inline_script( 'rdct-elementor-navigation', 'window.rdctNavigationPreview=' . wp_json_encode( array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'rdct_navigation_preview' ), 'postId' => absint( $_GET['elementor-preview'] ?? get_queried_object_id() ), 'error' => __( 'Preview could not be loaded.', 'radmanct' ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
} );

add_action( 'wp_ajax_rdct_navigation_preview', static function (): void {
	check_ajax_referer( 'rdct_navigation_preview', 'nonce' );
	if ( ! current_user_can( 'edit_post', absint( $_POST['postId'] ?? 0 ) ) ) { wp_send_json_error( null, 403 ); }
	$raw = $_POST['settings'] ?? '';
	if ( ! is_string( $raw ) || strlen( $raw ) > 8192 ) { wp_send_json_error( null, 400 ); }
	$settings = json_decode( wp_unslash( $raw ), true, 16 );
	$panel = '1' === ( $_POST['panel'] ?? '' );
	if ( $panel ) { require_once __DIR__ . '/panel.php'; }
	if ( ! is_array( $settings ) || null === ( $panel ? panel_options( $settings ) : options( $settings ) ) ) { wp_send_json_error( null, 400 ); }
	$instance = $_POST['instance'] ?? '';
	if ( ! is_string( $instance ) || ! preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', $instance ) ) { wp_send_json_error( null, 400 ); }
	\RadmanCT\Components\register_assets();
	ob_start(); if ( $panel ) { render_panel( $settings, $instance ); } else { render( $settings, $instance ); }
	$handles = array_filter( wp_styles()->queue, static fn( $handle ) => str_starts_with( $handle, 'rdct-menu-' ) );
	wp_print_styles( $handles );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
} );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	if ( ! wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { return; }
	$entry = \RadmanCT\Assets\entry( 'src/js/admin/navigation-controls.js' );
	if ( $entry ) { wp_enqueue_script( 'rdct-navigation-controls', \RadmanCT\Assets\url( $entry['file'] ), array( 'elementor-v2-editor-editing-panel', 'wp-element' ), null, true ); }
} );
