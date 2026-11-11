<?php

namespace RadmanCTContent\Admin\Settings;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/assets.php';

function enqueue( string $hook ): void {
	if ( 'toplevel_page_radmanct-content' === $hook && current_user_can( 'manage_options' ) ) {
		\RadmanCTContent\Admin\Assets\style( 'rdct-core-settings', 'src/css/admin/settings.css', array( 'dashicons' ) );
	}
}

function sanitize( $input ): array {
	$previous = \RadmanCTContent\Modules\options();
	if ( ! is_array( $input ) ) {
		add_settings_error( 'radman_core_modules', 'invalid', __( 'Invalid settings were ignored.', 'radmanct-content' ) );
		return $previous;
	}
	foreach ( $previous as $key => $value ) {
		if ( isset( $input[ $key ] ) && in_array( $input[ $key ], array( true, false, 1, 0, '1', '0' ), true ) ) {
			$previous[ $key ] = in_array( $input[ $key ], array( true, 1, '1' ), true );
		}
	}
	return $previous;
}

function inquiry( $input ): int {
	if ( null === $input || ! \RadmanCTContent\Modules\enabled( 'catalog' ) ) { return (int) get_option( 'radman_core_inquiry_page', 0 ); }
	if ( ( is_string( $input ) && ctype_digit( $input ) ) || is_int( $input ) ) {
		$id = max( 0, (int) $input );
		if ( ! $id || ( 'page' === get_post_type( $id ) && 'publish' === get_post_status( $id ) && '' === get_post_field( 'post_password', $id ) ) ) {
			return $id;
		}
	}
	add_settings_error( 'radman_core_modules', 'inquiry', __( 'Choose a published public contact page.', 'radmanct-content' ) );
	return (int) get_option( 'radman_core_inquiry_page', 0 );
}

function register(): void {
	register_setting( 'radman_core', 'radman_core_modules', array( 'type' => 'object', 'default' => array( 'catalog' => false, 'portfolio' => false ), 'sanitize_callback' => __NAMESPACE__ . '\\sanitize', 'show_in_rest' => false ) );
	register_setting( 'radman_core', 'radman_core_inquiry_page', array( 'type' => 'integer', 'default' => 0, 'sanitize_callback' => __NAMESPACE__ . '\\inquiry', 'show_in_rest' => false ) );
	if ( get_option( 'radman_core_rewrite_pending', false ) ) {
		flush_rewrite_rules( false );
		delete_option( 'radman_core_rewrite_pending' );
	}
}

function render(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$values = \RadmanCTContent\Modules\options();
	require_once __DIR__ . '/module-cards.php';
	require_once __DIR__ . '/inquiry.php';
	echo '<div class="wrap rdct-cor-settings"><h1>' . esc_html__( 'Radman Content', 'radmanct-content' ) . '</h1><p>' . esc_html__( 'Enable only the modules used by this project. After saving, each enabled module provides its own management menu and archive.', 'radmanct-content' ) . '</p>';
	settings_errors();
	\RadmanCTContent\Admin\Inquiry\search_form();
	echo '<form action="' . esc_url( admin_url( 'options.php' ) ) . '" method="post">';
	settings_fields( 'radman_core' );
	echo '<h2>' . esc_html__( 'Content modules', 'radmanct-content' ) . '</h2><p>' . esc_html__( 'Disabled modules keep their data and do not register content types or frontend processing.', 'radmanct-content' ) . '</p>';
	\RadmanCTContent\Admin\ModuleCards\render( $values );
	\RadmanCTContent\Admin\Inquiry\field();
	submit_button();
	echo '</form></div>';
}

add_action( 'admin_init', __NAMESPACE__ . '\\register' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
add_action( 'admin_menu', static function (): void { add_menu_page( __( 'Radman Content', 'radmanct-content' ), __( 'Radman Content', 'radmanct-content' ), 'manage_options', 'radmanct-content', __NAMESPACE__ . '\\render', 'dashicons-screenoptions', 62 ); } );
add_action( 'update_option_radman_core_modules', static function (): void { update_option( 'radman_core_rewrite_pending', true, false ); } );
add_action( 'add_option_radman_core_modules', static function (): void { update_option( 'radman_core_rewrite_pending', true, false ); } );
