<?php
require __DIR__ . '/builder-delete-contract.php';
class Builder_Response extends RuntimeException {
	public function __construct( public bool $success, public array $data ) { parent::__construct(); }
}
function nocache_headers() {}
function check_ajax_referer( ...$args ) { return true; }
function wp_unslash( $value ) { return stripslashes( $value ); }
function wp_send_json_success( $data ) { throw new Builder_Response( true, $data ); }
function wp_send_json_error( $data, $status = 400 ) { throw new Builder_Response( false, $data ); }
$GLOBALS['capability'] = true;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_REQUEST = array( 'nonce' => 'fixture' );
$before = RadmanCT\Builder\read( 'footer', $other );
$_POST = array( 'kind' => 'footer', 'id' => $other, 'operation' => 'save', 'layout' => json_encode( $before['layout'] ), 'revision' => (string) $before['revision'], 'title' => 'Inline builder name' );
try { RadmanCT\Admin\BuilderApi\request(); } catch ( Builder_Response $response ) {
	expect( $response->success && ! isset( $response->data['warning'] ), 'Combined name/layout save failed.' );
	expect( array_column( $response->data['layouts'], 'title', 'id' )[ $other ] === 'Inline builder name', 'Saved response lost the edited name.' );
}
$_POST['title'] = 'Stale name';
try { RadmanCT\Admin\BuilderApi\request(); } catch ( Builder_Response $response ) {
	expect( ! $response->success, 'Stale save was accepted.' );
}
expect( array_column( RadmanCT\Builder\Storage\listing( 'footer' ), 'title', 'id' )[ $other ] === 'Inline builder name', 'Stale save changed the title.' );
echo "Passed: one builder save updates name and layout; revision conflicts preserve the existing name. Stub API only.\n";
