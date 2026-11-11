<?php
namespace Elementor { class Controls_Manager { const TAB_ADVANCED = 'advanced'; const CODE = 'code'; } }
namespace {
define( 'ABSPATH', '/' );
$permission = true;
function __( $value, $domain ) { return $value; }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function current_user_can( $cap ) { return $GLOBALS['permission']; }
function expect( $test, $message ) { if ( ! $test ) { throw new \RuntimeException( $message ); } }
spl_autoload_register( static function ( $class ) {
	if ( ! str_starts_with( $class, 'Elementor\\' ) ) { return; }
	$parts = explode( '\\', substr( $class, 10 ) );
	$parts = array_map( static fn( $part ) => strtolower( str_replace( '_', '-', preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $part ) ) ), $parts );
	$file = dirname( __DIR__ ) . '/themes/elementor/' . implode( '/', $parts ) . '.php';
	if ( is_file( $file ) ) { require $file; }
} );
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/custom-css.php';
use RadmanCT\Integrations\Elementor\CustomCSS as CSS;
$results = array();
foreach ( json_decode( file_get_contents( __DIR__ . '/fixtures/custom-css.json' ), true, 32, JSON_THROW_ON_ERROR ) as $entry ) {
	$result = CSS\compile( $entry['input'], $entry['id'] ?? 'sample' ); $results[] = $result ?: null;
	expect( (bool) $result === isset( $entry['body'] ), 'CSS fixture validity changed: ' . json_encode( $entry ) );
	if ( $result ) { expect( '.' . $result['class'] . '{' . $entry['body'] . "\n}" === $result['css'], 'CSS fixture lost scoping or literal content.' ); }
}
if ( in_array( '--json', $argv, true ) ) { echo json_encode( $results ); exit; }
expect( array() === CSS\compile( str_repeat( 'x', 16385 ), 'sample' ), 'CSS byte limit missing.' );
$element = new class {
	public array $controls = array(); public array $attributes = array();
	public string $css = 'selector{color:red}'; public string $stored = '';
	function get_data( $key ) { return array( 'rdct_custom_css' => $this->stored ); }
	function get_controls( $key ) { return $this->controls[ $key ] ?? null; }
	function start_controls_section( $key, $options ) { $this->controls[ $key ] = $options; }
	function end_controls_section() {}
	function add_control( $key, $value ) { $this->controls[ $key ] = $value; }
	function get_settings( $key ) { return $this->css; }
	function get_id() { return 'sample'; }
	function add_render_attribute( $wrapper, $key, $value ) { $this->attributes[ $key ] = $value; }
};
CSS\classic_controls( $element, 'section_custom_css_pro' );
expect( 'code' === $element->controls['rdct_custom_css']['type'] && 'css' === $element->controls['rdct_custom_css']['language'], 'Classic CSS lost its native Code control.' );
$pro = clone $element; $pro->controls = array( 'custom_css' => array( 'type' => 'code' ) ); CSS\classic_controls( $pro, 'section_custom_css_pro' );
expect( ! isset( $pro->controls['rdct_custom_css'] ), 'Native Pro editor was duplicated.' );
$pro->stored = 'selector{opacity:.5}'; CSS\classic_controls( $pro, 'section_custom_css_pro' );
expect( 'Saved Radman CSS' === $pro->controls['rdct_custom_css_section']['label'] && isset( $pro->controls['custom_css'], $pro->controls['rdct_custom_css'] ), 'Installing Pro made existing Radman CSS uneditable or overwrote native data.' );
$permission = false; $denied = clone $element; $denied->controls = array(); CSS\classic_controls( $denied, 'section_custom_css_pro' );
expect( ! $denied->controls && ! CSS\atomic_controls( array() ), 'Untrusted editor exposed the custom code control.' );
CSS\render( $element ); expect( CSS\compile( $element->css, 'sample' )['class'] === $element->attributes['class'], 'Public rendering incorrectly depends on viewer capability.' );
$sheet = new class { public string $css = ''; function add_raw_css( $value ) { $this->css .= $value; } };
$post = new class( $sheet ) { function __construct( public $sheet ) {} function get_stylesheet() { return $this->sheet; } };
CSS\parse( $post, $element ); expect( CSS\compile( $element->css, 'sample' )['css'] === $sheet->css, 'CSS did not reach the native post stylesheet.' );
$permission = true; $controls = CSS\atomic_controls( array() ); $serialized = json_decode( json_encode( $controls ), true );
expect( 'textarea' === $serialized[0]['value']['items'][0]['value']['type'], 'Atomic CSS did not use the supplied native Textarea control.' );
$schema = CSS\atomic_schema( array() ); expect( isset( $schema['rdct_custom_css'] ), 'Atomic CSS schema missing.' );
echo "Passed: scoped CSS fixtures, native Classic/Atomic controls, Pro deduplication, capability gate, wrapper class and native post stylesheet.\n";
}
