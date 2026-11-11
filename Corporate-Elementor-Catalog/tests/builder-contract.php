<?php

define( 'ABSPATH', '/' );
$saved = array();
$active = false;
$allow = true;
$capability = false;
$nonce_checked = false;
$styles = array();
$queries = 0;
function is_singular( $type ) { return false; }
function add_option( $key, $value, $deprecated = '', $autoload = true ) { expect( false === $autoload, 'Layout must not autoload.' ); if ( isset( $GLOBALS['saved'][ $key ] ) ) { return false; } $GLOBALS['saved'][ $key ] = $value; return true; }
function maybe_serialize( $value ) { return serialize( $value ); }
function wp_cache_delete( ...$args ) {}
$wpdb = new class {
	public $options = 'test_options';
	public function prepare( $sql, ...$args ) { return $args; }
	public function query( $args ) {
		[ $next, $key, $old ] = $args;
		if ( serialize( $GLOBALS['saved'][ $key ] ?? null ) !== $old ) { return 0; }
		$GLOBALS['saved'][ $key ] = unserialize( $next );
		return 1;
	}
};
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function __( $value, $domain ) { return $value; }
function esc_html__( $value, $domain ) { return esc_html( $value ); }
function esc_attr__( $value, $domain ) { return esc_attr( $value ); }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function sanitize_textarea_field( $value ) { return strip_tags( $value ); }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function esc_url_raw( $value, $protocols ) { return preg_match( '/^javascript:/i', $value ) ? '' : $value; }
function esc_url( $value, $protocols = array() ) { return htmlspecialchars( esc_url_raw( $value, $protocols ), ENT_QUOTES ); }
function get_option( $key, $default ) { ++$GLOBALS['queries']; return $GLOBALS['saved'][ $key ] ?? $default; }
function get_theme_mod( $key, $default ) { return $GLOBALS['active']; }
function apply_filters( $hook, $value, ...$args ) {
	if ( 'radmanct/builder/allowed' === $hook ) { return $GLOBALS['allow']; }
	if ( 'wpml_translate_single_string' === $hook ) { return 'text' === substr( $args[1], -4 ) ? '<translated>' : $value; }
	return $value;
}
function current_user_can( $cap ) { return $GLOBALS['capability']; }
function wp_unslash( $value ) { return stripslashes( $value ); }
function update_option( $key, $value, $autoload ) { $GLOBALS['saved'][ $key ] = $value; expect( false === $autoload, 'Layout must not autoload.' ); return true; }
function set_theme_mod( $key, $value ) { $GLOBALS['active'] = $value; }
function do_action( $hook, ...$args ) { $GLOBALS['registered'][] = array( $hook, $args ); }
function admin_url( $path ) { return '/wp-admin/' . $path; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function wp_safe_redirect( $url ) { throw new RuntimeException( $url ); }
function wp_die( $message, $title, $args ) { throw new RuntimeException( (string) $args['response'] ); }
function check_admin_referer( $action ) { $GLOBALS['nonce_checked'] = true; if ( empty( $GLOBALS['allow_nonce'] ) ) { throw new RuntimeException( 'nonce-check' ); } }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require dirname( __DIR__ ) . '/radmanct/inc/assets.php';
require dirname( __DIR__ ) . '/radmanct/inc/builder.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/builder-page.php';

$layout = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'row1', 'items' => array( array( 'id' => 'item1', 'type' => 'text', 'text' => '<b>Hello</b>' ) ) ) ) );
$valid = \RadmanCT\Builder\validate( $layout );
expect( 'full' === $valid['width'], 'Default builder width is not full.' );
$contained = $layout + array( 'width' => 'contained' );
expect( 'contained' === \RadmanCT\Builder\validate( $contained )['width'], 'Contained layout width was not retained.' );
expect( null === \RadmanCT\Builder\validate( $layout + array( 'width' => '100vw;position:fixed' ) ), 'Invalid layout width was accepted.' );
expect( $valid['rows'][0]['appearance']['gap'] === 24, 'Old layouts lost default spacing.' );
$styled = $layout;
$styled['rows'][0]['appearance'] = array( 'gap' => 32, 'padding' => 20, 'background' => '#ABCDEF', 'visibility' => 'mobile' );
expect( '#abcdef' === \RadmanCT\Builder\validate( $styled )['rows'][0]['appearance']['background'], 'Row appearance did not normalize.' );
$styled['rows'][0]['appearance']['gap'] = '20px;display:none';
expect( null === \RadmanCT\Builder\validate( $styled ), 'Unsafe row appearance accepted.' );
$with_block = $layout;
$with_block['rows'][0]['items'][0] = array( 'id' => 'shared1', 'type' => 'block', 'blockId' => 7 );
expect( null !== \RadmanCT\Builder\validate( $with_block ), 'Shared content reference rejected.' );
$with_block['rows'][0]['items'][0]['blockId'] = '7';
expect( null === \RadmanCT\Builder\validate( $with_block ), 'Non-numeric shared content identifier accepted.' );
expect( 'Hello' === $valid['rows'][0]['items'][0]['text'], 'Text was not normalized.' );
$invalid = $layout;
$invalid['rows'][0]['items'][] = $invalid['rows'][0]['items'][0];
expect( null === \RadmanCT\Builder\validate( $invalid ), 'Duplicate identifiers accepted.' );
$invalid = $layout;
$invalid['rows'] = array_fill( 0, 9, $layout['rows'][0] );
expect( null === \RadmanCT\Builder\validate( $invalid ), 'Row limit bypassed.' );
$invalid = $layout;
$invalid['rows'][0]['items'][0] = array( 'id' => 'link1', 'type' => 'button', 'text' => 'Go', 'url' => 'javascript:alert(1)' );
expect( null === \RadmanCT\Builder\validate( $invalid ), 'Executable URL accepted.' );
$invalid['rows'][0]['items'][0]['url'] = '//untrusted.invalid';
expect( null === \RadmanCT\Builder\validate( $invalid ), 'Protocol-relative URL accepted.' );
$invalid['rows'][0]['items'][0]['url'] = '/contact/';
expect( null !== \RadmanCT\Builder\validate( $invalid ), 'Local URL rejected.' );
$invalid = $layout;
$invalid['rows'][0]['items'][0]['type'] = 'raw-html';
expect( null === \RadmanCT\Builder\validate( $invalid ), 'Unsupported renderer accepted.' );
expect( null === \RadmanCT\Builder\validate( array( 'version' => 1, 'enabled' => true, 'rows' => array() ) ), 'Empty active layout accepted.' );

ob_start();
expect( ! \RadmanCT\Builder\render( 'header' ), 'Inactive layout rendered.' );
expect( '' === ob_get_clean() && 0 === $queries, 'Inactive builder queried layout data.' );
expect( ! function_exists( '\RadmanCT\ContentBlocks\render' ), 'Inactive builder loaded the content block renderer.' );
$active = true;
$saved['radman_builder_header'] = $valid + array( 'revision' => 2 );
ob_start();
expect( \RadmanCT\Builder\render( 'header' ), 'Valid layout did not render.' );
$html = ob_get_clean();
expect( str_contains( $html, '&lt;translated&gt;' ) && ! str_contains( $html, '<translated>' ), 'Translated output was not escaped.' );
expect( ! str_contains( $html, 'data-builder-preview-' ), 'Editor selection markers leaked into public output.' );
ob_start();
\RadmanCT\Builder\render_layout( 'header', $valid, 'default', true );
$preview_markup = ob_get_clean();
expect( str_contains( $preview_markup, 'data-builder-preview-row="' . $valid['rows'][0]['id'] . '"' ) && str_contains( $preview_markup, 'data-builder-preview-item="' . $valid['rows'][0]['items'][0]['id'] . '"' ), 'Preview selection markers missing.' );
$allow = false;
expect( ! \RadmanCT\Builder\active( 'header' ), 'Per-page opt-out ignored.' );
expect( ! \RadmanCT\Builder\active( '../../invalid' ), 'Invalid placement accepted.' );
ob_start();
\RadmanCT\Admin\BuilderPage\render();
expect( '' === ob_get_clean(), 'Unauthorized editor rendered.' );
try { \RadmanCT\Admin\BuilderPage\save(); throw new LogicException( 'Unauthorized save accepted.' ); } catch ( RuntimeException $error ) { expect( '403' === $error->getMessage(), 'Capability check failed.' ); }
$capability = true;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST['kind'] = 'header';
try { \RadmanCT\Admin\BuilderPage\save(); throw new LogicException( 'GET save accepted.' ); } catch ( RuntimeException $error ) { expect( '400' === $error->getMessage(), 'POST check failed.' ); }
$_SERVER['REQUEST_METHOD'] = 'POST';
try { \RadmanCT\Admin\BuilderPage\save(); } catch ( RuntimeException $error ) { expect( 'nonce-check' === $error->getMessage() && $nonce_checked, 'Nonce check was bypassed.' ); }
$allow_nonce = true;
$_POST['layout'] = addslashes( json_encode( $layout ) );
$_POST['revision'] = '1';
try { \RadmanCT\Admin\BuilderPage\save(); } catch ( RuntimeException $error ) { expect( str_contains( $error->getMessage(), 'radman_status=conflict' ), 'Stale edit was not rejected.' ); }
expect( 2 === $saved['radman_builder_header']['revision'], 'Stale edit changed saved data.' );
$_POST['revision'] = '2';
$_POST['layout'] = '{broken';
try { \RadmanCT\Admin\BuilderPage\save(); } catch ( RuntimeException $error ) { expect( str_contains( $error->getMessage(), 'radman_status=invalid' ), 'Malformed JSON was not rejected.' ); }
expect( 2 === $saved['radman_builder_header']['revision'], 'Malformed submission changed data.' );
$_POST['layout'] = str_repeat( 'x', 262145 );
try { \RadmanCT\Admin\BuilderPage\save(); } catch ( RuntimeException $error ) { expect( str_contains( $error->getMessage(), 'radman_status=invalid' ), 'Oversized submission was not rejected.' ); }
expect( 2 === $saved['radman_builder_header']['revision'], 'Oversized submission changed data.' );
$_POST['layout'] = addslashes( json_encode( $layout ) );
try { \RadmanCT\Admin\BuilderPage\save(); } catch ( RuntimeException $error ) { expect( str_contains( $error->getMessage(), 'radman_status=saved' ), 'Valid edit did not save.' ); }
expect( 3 === $saved['radman_builder_header']['revision'] && $active && ! isset( $saved['radman_builder_footer'] ), 'Placement isolation or revision failed.' );
expect( 'header/item1/text' === $registered[0][1][1], 'WPML string identifier is not stable.' );
$attributes = \RadmanCT\Admin\BuilderPage\script_attributes( array( 'id' => 'rdct-admin-builder-js', 'nonce' => 'kept' ) );
expect( $attributes['type'] === 'module' && $attributes['nonce'] === 'kept', 'Module script metadata lost.' );
echo "Passed: builder schema limits, safe links, inactive data isolation, translated escaping, permission and nonce boundaries.\n";
