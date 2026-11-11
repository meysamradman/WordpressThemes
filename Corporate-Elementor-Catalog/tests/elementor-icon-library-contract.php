<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver { class Props_Resolver_Context {} abstract class Transformer_Base { abstract public function transform( $value, Props_Resolver_Context $context ); } }
namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers {
	class Icon_Transformer extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base {
		const SVG_INLINE_STYLES = 'width: 100%; height: 100%; overflow: unset;';
		public function transform( $value, \Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context $context ) { return array( 'native' => $value ); }
	}
}
namespace Elementor\Modules\AtomicWidgets\PropTypes { class Icon_Prop_Type { public static function get_key() { return 'icon'; } } }
namespace {

define( 'ABSPATH', '/usr/src/wordpress/' );
$hooks = array(); $styles = array(); $rendered = array();
function __( $value, $domain ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false ); }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function add_filter( $hook, $callback ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
function add_action( $hook, $callback ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
function admin_url( $path ) { return 'https://example.test/wp-admin/' . $path; }
function wp_create_nonce( $action ) { return 'test-nonce'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function apply_filters( $hook, $value ) {
	if ( 'radmanct/icons/catalog' === $hook ) { $value['client-factory'] = $value['phone']; $value['client-factory']['label'] = 'Factory'; }
	return $value;
}
function wp_register_style( $handle, $url, $deps, $version ) { $GLOBALS['styles'][ $handle ] = array( 'url' => $url ); }
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][ $handle ]['enqueued'] = true; }
function wp_style_is( $handle, $state ) { return ! empty( $GLOBALS['styles'][ $handle ]['enqueued'] ); }
function wp_add_inline_style( $handle, $css ) { $GLOBALS['styles'][ $handle ]['css'][] = $css; }
function rdct_icon( $name, $options = array() ) { $GLOBALS['rendered'][] = $name; echo function_exists( '\\RadmanCT\\Icons\\render' ) ? \RadmanCT\Icons\render( $name, $options ) : \RadmanCTComponents\Icons\render( $name, $options ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$package = 'theme' === ( $argv[1] ?? '' ) ? 'radmanct' : 'radmanct-components';
require dirname( __DIR__ ) . '/' . $package . '/inc/icons.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/icons/elementor.php';
require dirname( __DIR__ ) . '/radmanct/inc/icons/elementor.php';
foreach ( $hooks as $callbacks ) { expect( 1 === count( $callbacks ), 'Theme and Components registered duplicate hooks.' ); }
expect( 4 === count( $hooks ), 'Native tabs, Atomic transformer or editor-only CSS hooks missing.' );
expect( ! isset( $hooks['wp_enqueue_scripts'] ), 'Entire icon catalog was added to public assets.' );

use RadmanCTShared\Icons\Elementor as Library;
$tabs = Library\tabs( array( 'fa-solid' => array( 'native' => true ) ) );
expect( isset( $tabs['fa-solid'] ) && 87 === count( $tabs['rdct-local']['icons'] ), 'Native libraries or project icons were lost.' );
expect( false === $tabs['rdct-local']['url'] && str_starts_with( $tabs['rdct-local']['fetchJson'], 'https://example.test/wp-admin/admin-ajax.php?' ), 'Library requires a font or a remote JSON request.' );
Library\editor_styles(); Library\editor_styles();
$css = $styles['rdct-elementor-icon-library']['css'];
expect( 1 === count( $css ) && str_contains( $css[0], '.rdct-icon-client-factory' ) && str_contains( $css[0], 'data:image/svg+xml,' ), 'Editor masks were duplicated or missed project icons.' );
expect( ! preg_match( '~url\(["\']?(?:https?:|//|/)~', $css[0] ), 'Editor icon preview requires a network image/font.' );
$picker = Library\picker_data();
expect( 'Factory' === $picker['client-factory']['label'] && str_starts_with( $picker['phone']['src'], 'data:image/svg+xml,' ), 'Visual picker data does not share the project catalog.' );

foreach ( array( 'compat', 'compat-utf8', 'utf8', 'class-wp-token-map' ) as $file ) { require ABSPATH . 'wp-includes/' . $file . '.php'; }
require ABSPATH . 'wp-includes/kses.php';
foreach ( array( 'html5-named-character-references', 'class-wp-html-attribute-token', 'class-wp-html-text-replacement', 'class-wp-html-span', 'class-wp-html-doctype-info', 'class-wp-html-decoder', 'class-wp-html-tag-processor' ) as $file ) { require ABSPATH . 'wp-includes/html-api/' . $file . '.php'; }
$html = Library\render_icon( array( 'library' => 'rdct-local', 'value' => 'rdct-library rdct-icon-phone' ), array( 'class' => array( 'native-hover' ), 'aria-label' => 'Phone & contact', 'aria-hidden' => false, 'onclick' => 'bad()' ) );
expect( array( 'phone' ) === $rendered && 1 === substr_count( $html, '<svg' ) && ! str_contains( $html, 'data:image' ), 'Public output contains the whole library or multiple icons.' );
expect( str_contains( $html, 'native-hover' ) && str_contains( $html, 'Phone &amp; contact' ) && ! str_contains( $html, 'onclick' ), 'Native attributes were lost or unsafe event attributes accepted.' );
expect( str_contains( $html, 'fill:none;' ), 'Native fill CSS can turn an outline icon into a solid icon.' );
expect( '' === Library\render_icon( array( 'value' => 'rdct-library rdct-icon-../../bad' ) ), 'Arbitrary icon path accepted.' );
expect( '' === Library\render_icon( array( 'value' => 'fas fa-phone' ) ), 'Another library was intercepted.' );
echo "Passed: native tab, Theme/standalone coexistence, project icons, local editor previews and selected public SVG with real WP HTML API.\n";
$registry = new class {
	public $transformer;
	public function __construct() { $this->transformer = new \Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Icon_Transformer(); }
	public function get( $key ) { return $this->transformer; }
	public function register( $key, $transformer ) { expect( 'icon' === $key, 'Another native type was intercepted.' ); $this->transformer = $transformer; }
};
$hooks['elementor/atomic-widgets/settings/transformers/register'][0]( $registry );
$context = new \Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context();
$native = array( 'library' => 'fa-solid', 'value' => 'fas fa-phone' );
expect( array( 'native' => $native ) === $registry->transformer->transform( $native, $context ), 'Native icon transformer was not preserved.' );
$atomic = $registry->transformer->transform( array( 'library' => 'rdct-local', 'value' => 'rdct-library rdct-icon-client-factory' ), $context );
expect( null === $atomic['url'] && 1 === substr_count( $atomic['html'], '<svg' ) && str_contains( $atomic['html'], 'width:100%' ) && str_contains( $atomic['html'], 'fill:none;' ), 'Atomic custom icon requires font lookup, a request or loses its native sizing/outline.' );
echo "Passed: {$package} owns only the local Atomic icon branch and delegates other libraries.\n";
}
