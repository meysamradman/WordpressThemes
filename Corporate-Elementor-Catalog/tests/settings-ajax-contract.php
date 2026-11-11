<?php

require __DIR__ . '/settings-contract.php';

class SettingsResponse extends RuntimeException {
	public $data;
	public $status;
	public function __construct( $data, $status ) { parent::__construct(); $this->data = $data; $this->status = $status; }
}
function check_ajax_referer( $action, $key, $die ) { return 'valid' === ( $_POST[ $key ] ?? '' ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function get_settings_errors( $setting ) { return $GLOBALS['errors']; }
function update_option( $key, $value ) { if ( empty( $GLOBALS['fail_settings_write'] ) ) { $GLOBALS['saved'] = $value; } }
function wp_send_json_error( $data, $status ) { throw new SettingsResponse( $data, $status ); }
function wp_send_json_success( $data ) { throw new SettingsResponse( $data, 200 ); }
function save_request( array $input, $nonce = 'valid', string $method = 'POST' ): SettingsResponse {
	$_SERVER['REQUEST_METHOD'] = $method;
	$GLOBALS['errors'] = array(); $_POST = array( 'nonce' => $nonce, 'values' => addslashes( json_encode( $input ) ) );
	try { \RadmanCT\Admin\SettingsPage\save(); } catch ( SettingsResponse $result ) { return $result; }
	throw new RuntimeException( 'Missing save response.' );
}

$settings_caps = array( 'edit_theme_options' => true );
$saved = \RadmanCT\Settings\defaults();
expect( 403 === save_request( array( 'body_persian_line_height' => '1.9' ), 'expired' )->status, 'Expired nonce allowed a write.' );
expect( 403 === save_request( array( 'body_persian_line_height' => '1.9' ), array( 'valid' ) )->status, 'Malformed nonce reached validation.' );
expect( 403 === save_request( array( 'body_persian_line_height' => '1.9' ), 'valid', 'GET' )->status, 'A non-POST request reached settings storage.' );
$settings_caps['edit_theme_options'] = false;
expect( 403 === save_request( array( 'body_persian_line_height' => '1.9' ) )->status, 'Missing capability allowed a write.' );
$settings_caps['edit_theme_options'] = true;
expect( 400 === save_request( array( 'body_persian_line_height' => '1;display:none' ) )->status && '' === $saved['body_persian_line_height'], 'Invalid values were persisted.' );
$result = save_request( array( 'body_persian_line_height' => '1.90', 'custom_js' => 'forbidden()' ) );
expect( 200 === $result->status && '1.9' === $saved['body_persian_line_height'] && '' === $saved['custom_js'], 'Valid save failed or code capability was bypassed.' );
expect( $result->data['values'] === $saved, 'Save response is not canonical persisted data.' );
$fail_settings_write = true;
expect( 500 === save_request( array( 'body_persian_line_height' => '2' ) )->status, 'Failed persistence reported success.' );
echo "Passed: AJAX settings capability, nonce, validation, code boundary, canonical response and failed-write detection.\n";
