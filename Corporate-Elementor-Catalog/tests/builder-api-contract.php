<?php

require __DIR__ . '/builder-contract.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/builder-api.php';

$nonce_calls = 0;
$no_cache_calls = 0;
class BuilderJsonResponse extends RuntimeException {
	public array $result;
	public function __construct( bool $success, array $data, int $status ) {
		$this->result = array( 'success' => $success, 'data' => $data, 'status' => $status );
		parent::__construct( 'Builder response' );
	}
}
function nocache_headers() { ++$GLOBALS['no_cache_calls']; }
function check_ajax_referer( $action, $key, $stop ) {
	++$GLOBALS['nonce_calls'];
	expect( is_string( $_REQUEST[ $key ] ), 'Malformed nonce reached WordPress verification.' );
	return 'valid' === $_REQUEST[ $key ];
}
function wp_send_json_error( $data, $status = 400 ) { throw new BuilderJsonResponse( false, $data, $status ); }
function wp_send_json_success( $data ) { throw new BuilderJsonResponse( true, $data, 200 ); }
function builder_request( array $fields = array(), string $method = 'POST' ): array {
	$_SERVER['REQUEST_METHOD'] = $method;
	$_POST = array_merge( array( 'kind' => 'header', 'id' => 'default', 'operation' => 'read', 'nonce' => 'valid' ), $fields );
	$_REQUEST = $_POST;
	try { \RadmanCT\Admin\BuilderApi\request(); } catch ( BuilderJsonResponse $response ) { return $response->result; }
	throw new RuntimeException( 'Builder request did not respond.' );
}

set_error_handler( static function ( $severity, $message ) { throw new ErrorException( $message, 0, $severity ); } );
try {
	$capability = false;
	$before = $queries;
	expect( 403 === builder_request()['status'] && 0 === $nonce_calls && $before === $queries, 'Unauthorized request reached verification or storage.' );
	$capability = true;
	expect( 403 === builder_request( array(), 'GET' )['status'], 'GET request reached the builder.' );
	foreach ( array( null, array( 'valid' ), '', str_repeat( 'n', 129 ) ) as $nonce ) {
		$before_nonce = $nonce_calls;
		expect( 403 === builder_request( array( 'nonce' => $nonce ) )['status'] && $before_nonce === $nonce_calls, 'Malformed nonce was not rejected before core verification.' );
	}
	expect( 403 === builder_request( array( 'nonce' => 'invalid' ) )['status'], 'Invalid nonce was accepted.' );
	foreach ( array( array(), 'unknown', str_repeat( 'x', 1000 ) ) as $operation ) {
		$before = $queries;
		expect( 400 === builder_request( array( 'operation' => $operation ) )['status'] && $before === $queries, 'Unsupported operation reached layout storage.' );
	}
	expect( 404 === builder_request( array( 'kind' => array( 'header' ) ) )['status'], 'Malformed builder kind was accepted.' );
	expect( 404 === builder_request( array( 'id' => '../../options' ) )['status'], 'Path-like layout identifier was accepted.' );
	$before = $saved;
	foreach ( array( '{broken', str_repeat( 'x', 262145 ) ) as $layout_payload ) {
		expect( 400 === builder_request( array( 'operation' => 'save', 'layout' => $layout_payload, 'revision' => '3' ) )['status'] && $saved === $before, 'Rejected payload changed saved data.' );
	}
	expect( 409 === builder_request( array( 'operation' => 'save', 'layout' => addslashes( json_encode( $layout ) ), 'revision' => '999' ) )['status'] && $saved === $before, 'Stale revision changed a layout.' );
	$response = builder_request();
	expect( $response['success'] && 3 === $response['data']['data']['revision'] && $no_cache_calls > 0, 'Authorized read failed or cache boundary was not used.' );
	expect( builder_request( array( 'operation' => 'read', 'language' => 'all' ) )['success'], 'Site selection read failed.' );
	foreach ( array( 'unknown', array( 'en' ) ) as $invalid_language ) {
		expect( 400 === builder_request( array( 'operation' => 'read', 'language' => $invalid_language ) )['status'] && $saved === $before, 'Invalid language changed the selected layout.' );
	}
} finally {
	restore_error_handler();
}
echo "Passed: builder AJAX authorization, malformed nonce and operation guards, method and identifier boundaries, bounded payloads and stale-write preservation.\n";
