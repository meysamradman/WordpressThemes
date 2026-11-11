<?php

define( 'ABSPATH', '/' );
$handlers = array(); $allowed = true; $nonce_checks = 0;
function add_action( $name, $callback ): void { $GLOBALS['handlers'][ $name ] = $callback; }
function current_user_can( $cap ): bool { return $GLOBALS['allowed']; }
function check_ajax_referer( $action, $key, $die ): bool { ++$GLOBALS['nonce_checks']; return 'valid' === ( $_POST[ $key ] ?? null ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
class PreviewResponse extends RuntimeException { public function __construct( public int $status ) { parent::__construct(); } }
function wp_send_json_error( $data, $status ): void { throw new PreviewResponse( $status ); }
function expect_preview( bool $value, string $message ): void { if ( ! $value ) { throw new RuntimeException( $message ); } }
require __DIR__ . '/../radmanct-components/inc/admin/component-render.php';
function preview_request( array $fields, string $method = 'POST' ): int {
	$_POST = $fields; $_SERVER['REQUEST_METHOD'] = $method;
	try { $GLOBALS['handlers']['wp_ajax_rdct_component_render'](); } catch ( PreviewResponse $response ) { return $response->status; }
	throw new RuntimeException( 'Preview unexpectedly passed the input gate.' );
}
$request = array( '_ajax_nonce' => 'valid', 'settings' => '{}' );
$allowed = false;
expect_preview( 403 === preview_request( $request ) && 0 === $nonce_checks, 'Unauthorized preview reached nonce or renderer work.' );
$allowed = true;
expect_preview( 403 === preview_request( $request, 'GET' ), 'GET preview was accepted.' );
foreach ( array( array( 'valid' ), '', str_repeat( 'x', 129 ), 'expired' ) as $nonce ) {
	expect_preview( 403 === preview_request( array_replace( $request, array( '_ajax_nonce' => $nonce ) ) ), 'Malformed or invalid nonce was accepted.' );
}
foreach ( array( '{broken', str_repeat( 'x', 262145 ), str_repeat( '[', 40 ) . '0' . str_repeat( ']', 40 ) ) as $settings ) {
	expect_preview( 400 === preview_request( array_replace( $request, array( 'settings' => addslashes( $settings ) ) ) ), 'Invalid, oversized or deeply nested JSON reached a renderer.' );
}
echo "Passed: preview capability, POST, scalar nonce, payload size and JSON depth guards before renderer loading.\n";
