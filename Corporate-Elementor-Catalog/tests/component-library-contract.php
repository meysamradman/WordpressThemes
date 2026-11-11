<?php

require_once __DIR__ . '/fixtures/components-url.php';
define( 'ABSPATH', '/' );
$actions = array();
$permission = true;
$valid_nonce = true;
function __( $value, $domain ) { return $value; }
function esc_html__( $value, $domain ) { return esc_html( $value ); }
function esc_attr__( $value, $domain ) { return esc_attr( $value ); }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }
function current_user_can( $cap ) { return $GLOBALS['permission']; }
function is_rtl() { return true; }
function wp_die( $message = '', $title = '', $args = array() ) { throw new RuntimeException( 'Denied', $args['response'] ?? 403 ); }
function check_admin_referer( $action ) { if ( ! $GLOBALS['valid_nonce'] ) { wp_die(); } }
function check_ajax_referer( $action ) { check_admin_referer( $action ); }
function absint( $value ) { return abs( (int) $value ); }
function did_action( $action ) { return true; }
class LibraryResponse extends RuntimeException { public function __construct( public array $data, int $status = 200 ) { parent::__construct( '', $status ); } }
function wp_send_json_error( $data, $status ) { throw new LibraryResponse( array( 'success' => false ), $status ); }
function wp_send_json_success( $data ) { throw new LibraryResponse( array( 'success' => true, 'data' => $data ) ); }
function wp_unslash( $value ) { return $value; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function add_action( $name, $callback, ...$args ) { $GLOBALS['actions'][ $name ] = $callback; }
function add_filter( ...$args ) {}
function get_template_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function admin_url( $path ) { return 'https://radman.test/wp-admin/' . $path; }
function wp_create_nonce( $action ) { return 'read-preview-token'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct-components/inc/admin/components-page.php';

$catalog = \RadmanCTComponents\Components\Library\catalog();
$visible_count = count( array_filter( $catalog, static fn( $item ) => empty( $item['hidden'] ) ) );
expect( count( $catalog ) >= 10 && isset( $catalog['table-general'] ), 'Missing component samples.' );
foreach ( $catalog as $id => $item ) {
	expect( is_file( dirname( __DIR__ ) . '/radmanct-components/images/components/' . $item['image'] ), 'Missing local thumbnail.' );
	$fa = \RadmanCTComponents\Components\Library\model( $id, 'fa' );
	$en = \RadmanCTComponents\Components\Library\model( $id, 'en' );
	expect( 'container' === $fa['elType'] && $item['widget'] === $fa['elements'][0]['widgetType'], 'Incompatible Elementor sample.' );
	if ( 'text-reveal' === $item['component'] ) {
		expect( 'rtl' === $fa['elements'][0]['settings']['direction'] && 'ltr' === $en['elements'][0]['settings']['direction'], 'Language direction lost.' );
		expect( $fa['elements'][0]['settings']['text'] !== $en['elements'][0]['settings']['text'], 'Preview languages depend on admin locale.' );
	}
}
expect( array() === \RadmanCTComponents\Components\Library\model( '../../unknown', 'fa' ), 'Unknown sample accepted.' );
expect( array() === \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'atomic' ) && array() === \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'unknown' ), 'Unavailable Atomic or unknown variant silently became classic.' );
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\library(); $gallery = ob_get_clean();
expect( count( \RadmanCTComponents\Components\Library\groups() ) === substr_count( $gallery, 'data-library-card=' ) && ! str_contains( $gallery, 'data-library-insert=' ), 'Gallery exposes editor actions.' );
expect( ! str_contains( $gallery, 'data-library-card="icon-arrow"' ), 'Legacy hidden icon created a duplicate library card.' );
expect( 21 === substr_count( $gallery, 'data-library-family=' ) && str_contains( $gallery, 'data-library-designs hidden' ) && ! str_contains( $gallery, 'data-library-preview=' ), 'Families must open the deferred design list before any live preview.' );
expect( 13 === count( \RadmanCTComponents\Components\Library\groups()['button']['variants'] ) && 94 === array_sum( array_map( static fn( $group ) => count( $group['variants'] ), \RadmanCTComponents\Components\Library\groups() ) ), 'Grouping lost registered designs.' );
expect( ! str_contains( $gallery, 'Preview not captured yet' ) && ! str_contains( $gallery, '<img ' ) && 21 === substr_count( $gallery, 'data-library-icon' ), 'Image-free families require real local icons without missing-screenshot prompts.' );
expect( str_contains( $gallery, '<iframe title=' ) && ! str_contains( $gallery, '<iframe src=' ) && ! str_contains( $gallery, 'src="https://radman.test/wp-admin/admin-post.php' ), 'Gallery eagerly loaded live previews.' );
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\editor(); $editor = ob_get_clean();
expect( str_contains( $editor, 'data-library-open' ) && ! str_contains( $editor, '<dialog' ) && ! str_contains( $editor, 'data-library-models' ), 'Initial editor footer must defer to the native modal instead of creating a custom shell.' );
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\library( true ); $loaded = ob_get_clean();
expect( 1 === substr_count( $loaded, ' data-library-insert' ) && str_contains( $loaded, 'data-library-model-url' ) && ! str_contains( $loaded, 'data-library-models' ), 'Library must keep cards while deferring sample models.' );
expect( str_contains( $loaded, 'class="e-btn e-primary"' ) && ! str_contains( $loaded, 'class="button' ), 'Editor library reused WordPress Admin button styling.' );
expect( 21 === substr_count( $loaded, 'data-library-family=' ) && str_contains( $loaded, 'data-library-design-grid' ), 'Elementor must use the same family/design flow.' );
expect( str_contains( $gallery, 'class="button"' ) && ! str_contains( $gallery, 'class="e-btn' ), 'Standalone admin library lost its independent controls.' );
expect( ! str_contains( $editor, '<select data-library-mode>' ), 'Unavailable Atomic mode exposed.' );
$_POST = array( 'post_id' => 23, 'view' => 'model', 'sample' => 'icon-phone', 'language' => 'en', 'mode' => 'classic' );
try { \RadmanCTComponents\Admin\ComponentsPage\editor_library(); throw new LogicException( 'Missing model response.' ); }
catch ( LibraryResponse $result ) { expect( $result->data['success'] && $result->data['data']['model'] === \RadmanCTComponents\Components\Library\model( 'icon-phone', 'en', 'classic' ), 'Endpoint did not return only the selected model.' ); }
foreach ( array( array( 'language', 'xx' ), array( 'sample', '../../bad' ), array( 'mode', 'missing' ), array( 'view', 'unknown' ) ) as [ $key, $value ] ) {
	$old = $_POST[ $key ]; $_POST[ $key ] = $value;
	try { \RadmanCTComponents\Admin\ComponentsPage\editor_library(); throw new LogicException( 'Invalid model accepted.' ); }
	catch ( LibraryResponse $result ) { expect( 400 === $result->getCode(), 'Invalid model selection must fail.' ); }
	$_POST[ $key ] = $old;
}
expect( isset( $actions['admin_post_radman_component_preview'] ) && ! isset( $actions['admin_post_nopriv_radman_component_preview'] ), 'Preview exposed anonymously.' );
$_GET['sample'] = '../../unknown';
try { \RadmanCTComponents\Admin\ComponentsPage\preview(); throw new LogicException( 'Unknown preview accepted.' ); } catch ( RuntimeException $error ) { expect( 404 === $error->getCode(), 'Unknown preview status incorrect.' ); }
$valid_nonce = false;
try { \RadmanCTComponents\Admin\ComponentsPage\preview(); throw new LogicException( 'Invalid nonce accepted.' ); } catch ( RuntimeException $error ) { expect( 403 === $error->getCode(), 'Nonce check missing.' ); }
$permission = false;
try { \RadmanCTComponents\Admin\ComponentsPage\editor_library(); throw new LogicException( 'Denied model accepted.' ); }
catch ( LibraryResponse $result ) { expect( 403 === $result->getCode(), 'Model endpoint missed permission checks.' ); }
try { \RadmanCTComponents\Admin\ComponentsPage\preview(); throw new LogicException( 'Denied preview accepted.' ); } catch ( RuntimeException $error ) { expect( 403 === $error->getCode(), 'Permission check missing.' ); }
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\render(); \RadmanCTComponents\Admin\ComponentsPage\editor(); $denied = ob_get_clean();
expect( '' === $denied && ! \RadmanCTComponents\Admin\ComponentsPage\allowed(), 'Unauthorized UI exposed.' );
echo "Passed: shared local samples, both languages, deferred preview, editor-only insertion and capability boundary.\n";
