<?php

namespace RadmanCT\Admin\SettingsPage;

defined( 'ABSPATH' ) || exit;

function save(): void {
	$nonce = $_POST['nonce'] ?? null;
	if ( ! current_user_can( 'edit_theme_options' ) || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! is_string( $nonce ) || '' === $nonce || strlen( $nonce ) > 128 || ! check_ajax_referer( 'radman_settings_save', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Settings could not be saved. Reload the page and try again.', 'radmanct' ) ), 403 );
	}
	$raw = $_POST['values'] ?? null;
	$input = is_string( $raw ) && strlen( $raw ) <= 4194304 ? json_decode( wp_unslash( $raw ), true, 16 ) : null;
	if ( ! is_array( $input ) || ! array_intersect_key( $input, \RadmanCT\Settings\defaults() ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid settings data.', 'radmanct' ) ), 400 );
	}
	if ( ! \RadmanCT\CustomCode\can_edit() ) {
		$input = array_diff_key( $input, \RadmanCT\CustomCode\defaults() );
	}
	$values = \RadmanCT\Settings\sanitize( $input );
	if ( get_settings_errors( 'radman_settings' ) ) {
		wp_send_json_error( array( 'message' => __( 'Some values are invalid. Check the settings and try again.', 'radmanct' ) ), 400 );
	}
	update_option( 'radman_settings', $values );
	if ( \RadmanCT\Settings\values() !== $values ) {
		wp_send_json_error( array( 'message' => __( 'Settings could not be saved. Try again.', 'radmanct' ) ), 500 );
	}
	wp_send_json_success( array( 'values' => $values ) );
}

add_action( 'wp_ajax_rdct_save_settings', __NAMESPACE__ . '\\save' );
