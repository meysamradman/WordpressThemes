<?php

define( 'ABSPATH', '/' );
if ( 'off' !== ( $argv[1] ?? '' ) ) { define( 'ICL_SITEPRESS_VERSION', '5.0.2' ); }
$options = array(); $mods = array(); $posts = array(); $meta = array(); $details = array(); $hooks = array();
$language = 'fa'; $capability = true; $number = 0; $post_number = 0; $actions = array(); $reads = 0;
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
function __( $value, $domain ) { return $value; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return strip_tags( $value ); }
function esc_url_raw( $value, ...$args ) { return $value; }
function wp_slash( $value ) { return addslashes( $value ); }
function wp_html_excerpt( $value, $length, $more ) { return mb_substr( $value, 0, $length ); }
function current_user_can( ...$args ) { return $GLOBALS['capability']; }
function get_current_user_id() { return 1; }
function wp_generate_uuid4() { return sprintf( '00000000-0000-4000-a000-%012d', ++$GLOBALS['number'] ); }
function get_option( $key, $default = false ) { ++$GLOBALS['reads']; return $GLOBALS['options'][ $key ] ?? $default; }
function add_option( $key, $value, $old = '', $autoload = true ) { expect( false === $autoload, 'Bridge options must not autoload.' ); if ( isset( $GLOBALS['options'][ $key ] ) ) { return false; } $GLOBALS['options'][ $key ] = $value; return true; }
function delete_option( $key ) { unset( $GLOBALS['options'][ $key ] ); }
function maybe_serialize( $value ) { return serialize( $value ); }
function wp_cache_delete( ...$args ) {}
function get_theme_mod( $key, $default = false ) { return $GLOBALS['mods'][ $key ] ?? $default; }
function set_theme_mod( $key, $value ) { $GLOBALS['mods'][ $key ] = $value; }
function remove_theme_mod( $key ) { unset( $GLOBALS['mods'][ $key ] ); }
function add_action( $name, $callback, ...$args ) { $GLOBALS['hooks'][ $name ][] = $callback; }
function add_filter( ...$args ) {}
function register_post_type( $type, $args ) { $GLOBALS['registered_type'] = $args; }
function get_post_type( $id ) { return $GLOBALS['posts'][ $id ]['post_type'] ?? null; }
function get_post_status( $id ) { return $GLOBALS['posts'][ $id ]['post_status'] ?? null; }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['meta'][ $id ][ $key ] = $value; }
function wp_insert_post( $data, $error = false ) {
	$id = ++$GLOBALS['post_number']; $GLOBALS['posts'][ $id ] = $data;
	$GLOBALS['details'][ $id ] = (object) array( 'trid' => $id, 'language_code' => $GLOBALS['language'], 'source_language_code' => null );
	return $id;
}
function wp_delete_post( $id, $force ) { unset( $GLOBALS['posts'][ $id ], $GLOBALS['meta'][ $id ], $GLOBALS['details'][ $id ] ); }
class WP_Error { public string $code; public function __construct( $code, $message ) { $this->code = $code; } }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function apply_filters( $hook, $value, ...$args ) {
	if ( 'wpml_active_languages' === $hook ) { return array( array( 'code' => 'fa', 'native_name' => 'Persian' ), array( 'code' => 'en', 'native_name' => 'English' ), array( 'code' => 'de', 'native_name' => 'German' ) ); }
	if ( in_array( $hook, array( 'wpml_default_language', 'wpml_current_language' ), true ) ) { return 'wpml_current_language' === $hook ? $GLOBALS['language'] : 'fa'; }
	if ( 'wpml_is_rtl' === $hook ) { return 'fa' === $value; }
	if ( 'wpml_element_language_details' === $hook ) { expect( 'rdct_layout' === $args[0]['element_type'], 'Read hook requires the raw post type.' ); return $GLOBALS['details'][ $args[0]['element_id'] ] ?? null; }
	if ( 'wpml_get_element_translations' === $hook ) {
		expect( 'post_rdct_layout' === $args[1], 'Translation group requires the prefixed type.' ); $result = array();
		foreach ( $GLOBALS['details'] as $id => $item ) { if ( (int) $item->trid === (int) $args[0] ) { $result[ $item->language_code ] = (object) array( 'element_id' => $id ); } }
		return $result;
	}
	if ( 'wpml_object_id' === $hook ) {
		expect( 'rdct_layout' === $args[0], 'Object translation requires the raw post type.' );
		$group = $GLOBALS['details'][ $value ]->trid ?? 0;
		foreach ( $GLOBALS['details'] as $id => $item ) { if ( $item->trid === $group && $item->language_code === $args[2] ) { return $id; } }
		return false;
	}
	return $value;
}
function do_action( $hook, ...$args ) {
	$GLOBALS['actions'][] = array( $hook, $args );
	if ( 'wpml_set_element_language_details' === $hook ) {
		$value = $args[0]; expect( 'post_rdct_layout' === $value['element_type'], 'Write hook requires the prefixed type.' );
		$GLOBALS['details'][ $value['element_id'] ] = (object) array( 'trid' => (int) $value['trid'], 'language_code' => $value['language_code'], 'source_language_code' => $value['source_language_code'] );
	}
	if ( 'wpml_switch_language' === $hook ) { $GLOBALS['language'] = $args[0]; }
	foreach ( $GLOBALS['hooks'][ $hook ] ?? array() as $callback ) { $callback( ...$args ); }
}
$wpdb = new class {
	public string $options = 'options';
	public function prepare( $sql, ...$args ) { return $args; }
	public function query( $args ) { [ $next, $key, $previous ] = $args; if ( serialize( $GLOBALS['options'][ $key ] ?? null ) !== $previous ) { return 0; } $GLOBALS['options'][ $key ] = unserialize( $next ); return 1; }
};
require dirname( __DIR__ ) . '/radmanct/inc/builder.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/wpml/builder.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/wpml/admin-builder.php';
use RadmanCT\Builder\Storage;
use RadmanCT\Integrations\Wpml\Builder as Bridge;
set_error_handler( static function ( $severity, $message ) { throw new ErrorException( $message, 0, $severity ); } );
if ( ! Bridge\enabled() ) {
	Bridge\register(); $before = $reads;
	expect( 'default' === Bridge\resolve( 'default', 'header' ) && ! isset( $registered_type ) && $reads === $before && null === Bridge\translation_state( 'header', 'default' ), 'Inactive WPML changed runtime or queried bridge storage.' );
	echo "Passed: no WPML means no bridge writes, registration or frontend storage reads.\n"; exit;
}
Bridge\register(); expect( false === $registered_type['public'] && false === $registered_type['show_in_rest'], 'Translation references exposed a public endpoint.' );
$layout = \RadmanCT\Builder\validate( array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'row1', 'items' => array( array( 'id' => 'item1', 'type' => 'text', 'text' => 'Original content' ) ) ) ) ) );
$source = Storage\create( 'header', 'Persian header', $layout );
$before = \RadmanCT\Builder\read( 'header', $source );
$english = Bridge\translate_layout( 'header', $source, 'fa', 'en', 1 );
expect( is_string( $english ) && $english !== $source, 'Independent English layout was not created.' );
expect( $before === \RadmanCT\Builder\read( 'header', $source ) && false === \RadmanCT\Builder\read( 'header', $english )['layout']['enabled'], 'Creating a translation overwrote or published a layout.' );
expect( $english === Bridge\translate_layout( 'header', $source, 'fa', 'en', 1 ), 'Repeated creation was not idempotent.' );
$state = Bridge\translation_state( 'header', $english );
expect( $state['bound'] && 'en' === $state['source'] && 'ltr' === $state['direction'], 'Selected translation language/direction is wrong.' );
$language = 'en'; expect( $source === Bridge\resolve( $source, 'header' ), 'Disabled translation replaced a live layout.' );
$edited = $layout; $edited['enabled'] = true; $edited['transparent'] = true;
expect( 'saved' === Storage\save( 'header', $english, $edited, 1 )['status'], 'Independent edit failed.' );
expect( $english === Bridge\resolve( $source, 'header' ) && $before === \RadmanCT\Builder\read( 'header', $source ), 'Translated selection or source isolation failed.' );
expect( 'Local text' === \RadmanCT\Builder\translate( 'Local text', 'header', 'item', 'text', $english ), 'Independent translated content was passed through String Translation again.' );
$language = 'fa'; expect( 'en' === Bridge\render_preview( 'header', $english, static fn() => $GLOBALS['language'] ) && 'fa' === $language, 'Preview did not switch and restore its language.' );
expect( $english === Bridge\resolve_language( $source, 'header', 'en' ) && 'fa' === $language, 'Language selection changed global language or ignored the native translation.' );
$german = Storage\create( 'header', 'Existing German header', $edited ); $original = \RadmanCT\Builder\read( 'header', $german );
expect( $german === Bridge\translate_layout( 'header', $source, 'fa', 'de', 1, $german ) && $original === \RadmanCT\Builder\read( 'header', $german ), 'Connecting an existing layout overwrote it.' );
$capability = false; expect( is_wp_error( Bridge\translate_layout( 'header', $source, 'fa', 'en', 1 ) ), 'Unauthorized translation succeeded.' ); $capability = true;
expect( is_wp_error( Bridge\translate_layout( 'header', $source, 'fa', 'en', 99 ) ) && is_wp_error( Bridge\translate_layout( 'header', $source, 'fa', 'xx', 1 ) ), 'Stale revision or inactive language accepted.' );
$footer = Storage\create( 'footer', 'Footer', $layout );
expect( is_wp_error( Bridge\translate_layout( 'footer', $footer, 'fa', 'de', 1, $german ) ), 'Cross-builder connection accepted.' );
$snapshot = \RadmanCT\Builder\read( 'header', $english );
Storage\remove( 'header', $german );
expect( $snapshot === \RadmanCT\Builder\read( 'header', $english ), 'Deleting a layout cascaded into another translation.' );
$replacement = Bridge\translate_layout( 'header', $source, 'fa', 'de', 1 );
expect( is_string( $replacement ) && $replacement !== $german, 'Deleted translation reference could not be reused safely.' );
echo "Passed: native-hook bridge contract, connected independent layouts, draft safety, active selection, existing connections, preview restore, source isolation and bounded authorization. Real WPML/browser acceptance remains separate.\n";
