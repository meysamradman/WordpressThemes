<?php
namespace RadmanCT\Icons {
	function labels(): array { return array( 'phone' => 'Phone', 'client-factory' => 'Factory', 'bad;name' => 'Invalid' ); }
}
namespace {
define( 'ABSPATH', '/' );
$allowed = true; $valid_nonce = true; $hooks = array();
$_SERVER['REQUEST_METHOD'] = 'GET'; $_REQUEST['_ajax_nonce'] = 'test-nonce';
function add_action( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ] = $callback; }
function add_filter( ...$args ) { add_action( ...$args ); }
function current_user_can( $capability ) { return 'edit_posts' === $capability && $GLOBALS['allowed']; }
function wp_create_nonce( $action ) { return 'nonce-' . $action; }
function admin_url( $path ) { return 'https://local.test/wp-admin/' . $path; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
class Response extends RuntimeException { public function __construct( public $payload, public int $status ) { parent::__construct(); } }
function wp_send_json( $payload ) { throw new Response( $payload, 200 ); }
function wp_send_json_error( $payload, $status ) { throw new Response( $payload, $status ); }
function check_ajax_referer( $action ) { if ( ! $GLOBALS['valid_nonce'] || 'rdct_icon_library' !== $action ) { throw new Response( null, 403 ); } }
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/shared/icons/elementor.php';
use RadmanCTShared\Icons\Elementor as Library;
$tabs = Library\tabs( array( 'fa-solid' => array( 'native' => true ) ) );
$tab = $tabs['rdct-local'];
expect( isset( $tabs['fa-solid'] ) && array( 'phone', 'client-factory' ) === $tab['icons'] && false === $tab['url'], 'Native or project icons changed, or a font dependency was added.' );
expect( str_starts_with( $tab['fetchJson'], 'https://local.test/wp-admin/admin-ajax.php?' ) && str_contains( $tab['fetchJson'], 'action=rdct_icon_library' ), 'Fallback JSON is not the local authenticated native format.' );
expect( ! isset( $hooks['wp_ajax_nopriv_rdct_icon_library'] ), 'Icon metadata endpoint was exposed to guests.' );
foreach ( array( array( true, true, 200 ), array( false, true, 403 ), array( true, false, 403 ) ) as [$allowed, $valid_nonce, $status] ) {
	try { Library\library_json(); throw new RuntimeException( 'Endpoint did not respond.' ); }
	catch ( Response $response ) {
		expect( $status === $response->status, 'Icon JSON permission or nonce guard failed.' );
		if ( 200 === $status ) { expect( array( 'icons' => array( 'phone', 'client-factory' ) ) === $response->payload, 'Native JSON omitted project icons or retained invalid identifiers.' ); }
	}
}
$allowed = true; $valid_nonce = true;
foreach ( array( array( 'POST', 'nonce', 405 ), array( 'GET', array(), 403 ), array( 'GET', str_repeat( 'a', 101 ), 403 ) ) as [$method, $nonce, $status] ) {
	$_SERVER['REQUEST_METHOD'] = $method; $_REQUEST['_ajax_nonce'] = $nonce;
	try { Library\library_json(); throw new RuntimeException( 'Invalid method/nonce accepted.' ); }
	catch ( Response $response ) { expect( $status === $response->status, 'Method or malformed nonce was not rejected.' ); }
}
echo "Passed: same-origin native icon JSON, shared project catalog, permissions, nonce and no public endpoint.\n";
}
