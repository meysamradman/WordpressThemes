<?php
namespace Elementor {
	class Controls_Manager { const SELECT = 'select'; const MEDIA = 'media'; const SWITCHER = 'switcher'; const COLOR = 'color'; const NUMBER = 'number'; const TEXT = 'text'; const TAB_STYLE = 'style'; const TAB_CONTENT = 'content'; }
	class Group_Control_Typography { public static function get_type() { return 'typography'; } }
	class Widget_Base {
		public function __construct( $data = array(), $args = null ) {}
		public function render_content() {}
		public function is_type_instance() { return true; }
		public array $controls = array();
		public function start_controls_section( ...$args ) {}
		public function end_controls_section() {}
		public function add_control( $key, $value ) { $this->controls[ $key ] = $value; }
		public function add_group_control( ...$args ) {}
	}
}
namespace {
define( 'ABSPATH', '/usr/src/wordpress/' );
function __( $text, $domain ) { return $text; }
function esc_html__( $text, $domain ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value, ...$args ) { return $value; }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function get_posts( $args ) { return array(); }
function post_type_exists( $type ) { return false; }
function wp_get_nav_menus( $args ) { return array( (object) array( 'term_id' => 17, 'name' => 'Header' ) ); }
function get_nav_menu_locations() { return array(); }
function wp_get_nav_menu_items( ...$args ) { return array(); }
function get_intermediate_image_sizes() { return array( 'large' ); }
function wp_get_additional_image_sizes() { return array(); }
function get_option( ...$args ) { return 800; }
function expect( $value, $message ) { if ( ! $value ) { throw new \RuntimeException( $message ); } }
spl_autoload_register( static function ( $class ) {
	if ( ! str_starts_with( $class, 'Elementor\\' ) ) { return; }
	$parts = explode( '\\', substr( $class, 10 ) );
	$parts = array_map( static fn( $part ) => strtolower( str_replace( '_', '-', preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $part ) ) ), $parts );
	$file = dirname( __DIR__ ) . '/themes/elementor/' . implode( '/', $parts ) . '.php';
	if ( is_file( $file ) ) { require $file; }
} );
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/navigation.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/navigation/classic.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/navigation/atomic.php';
require dirname( __DIR__ ) . '/radmanct/inc/components.php';
$settings = array( 'menuId' => '17', 'drawerDesktop' => true, 'mobileDisplay' => 'drawer', 'panelMode' => 'split', 'menuSkin' => 'line-fill', 'panelImage' => array( 'id' => 42 ) );
$options = \RadmanCT\Integrations\Elementor\Navigation\options( $settings );
expect( 17 === $options['menuId'] && 42 === $options['panelImageId'] && array( 'desktop', 'mobile' ) === $options['drawerDevices'] && 'drawer' === $options['mobileDisplay'], 'Native editor values lost shared menu/device/image settings.' );
$styles = \RadmanCT\Integrations\Elementor\Navigation\styles( $settings );
expect( in_array( 'rdct-menu-panel-split', $styles, true ) && in_array( 'rdct-menu-skin-line-fill', $styles, true ) && ! in_array( 'rdct-menu-panel-fullscreen', $styles, true ), 'Unused panel style leaked into widget dependencies.' );
foreach ( array( 'menuId' => 'invalid', 'panelMode' => 'raw-html', 'panelColor' => 'red;display:none', 'panelWidth' => 99999, 'megaColumns' => 2.5 ) as $key => $value ) {
	expect( null === \RadmanCT\Integrations\Elementor\Navigation\options( array_replace( $settings, array( $key => $value ) ) ), 'Invalid adapter value accepted: ' . $key );
}
$classic = new \RadmanCT\Integrations\Elementor\Navigation\Classic();
expect( array() === $classic->get_style_depends(), 'Prototype styling tried to read instance settings.' );
( new \ReflectionMethod( $classic, 'register_controls' ) )->invoke( $classic );
expect( 'select' === $classic->controls['menuId']['type'] && 'media' === $classic->controls['panelImage']['type'] && 'color' === $classic->controls['panelColor']['type'], 'Classic controls did not use native menu/media/color controls.' );
expect( 'select' === $classic->controls['menuHover']['type'] && 'select' === $classic->controls['menuIndicator']['type'], 'Hover effects lost native controls.' );
expect( null === \RadmanCT\Navigation\Component\settings( array( 'menuHover' => 'unknown' ) ), 'Unknown hover effect accepted.' );
expect( array() === \RadmanCT\Navigation\Hover\assets( array() ), 'Default menu loads hover assets.' );
expect( array( 'menu-hover-roll', 'menu-indicator-dot' ) === \RadmanCT\Navigation\Hover\assets( array( 'menuHover' => 'roll', 'menuIndicator' => 'dot' ) ), 'Selected hover assets are not independent.' );
$roll_title = \RadmanCT\Navigation\Hover\title( '<span id="original">نمونه</span>' );
expect( 1 === substr_count( $roll_title, 'id="original"' ) && str_contains( $roll_title, 'aria-hidden="true">نمونه' ), 'Rolling label duplicated IDs or lost its accessible Persian label.' );
$schema = \RadmanCT\Integrations\Elementor\Navigation\Atomic::get_props_schema();
expect( $schema['menuId'] instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type && $schema['drawerDesktop'] instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type, 'Atomic props lost native types.' );
$atomic = new \RadmanCT\Integrations\Elementor\Navigation\Atomic();
$controls = ( new \ReflectionMethod( $atomic, 'define_atomic_controls' ) )->invoke( $atomic );
json_encode( $controls, JSON_THROW_ON_ERROR );
expect( 3 === count( $controls ), 'Atomic menu/panel groups missing.' );
$bindings = array(); foreach ( $controls as $section ) { foreach ( $section->get_items() as $control ) { $bindings[ $control->get_bind() ] = $control->get_type(); } }
expect( 'rdct-navigation-choice' === $bindings['menuId'] && 'image' === $bindings['panelImage'] && 'rdct-navigation-color' === $bindings['panelColor'] && 'switch' === $bindings['drawerDesktop'], 'Atomic native control bindings mismatched the schema.' );
require_once dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/navigation/panel.php';
require_once dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/navigation/panel-atomic.php';
$panel_classic = new \RadmanCT\Integrations\Elementor\Navigation\PanelClassic();
( new \ReflectionMethod( $panel_classic, 'register_controls' ) )->invoke( $panel_classic );
expect( isset( $panel_classic->controls['panelTemplateId'] ) && ! isset( $panel_classic->controls['menuId'] ), 'Independent panel editor still requires a menu.' );
$panel_schema = \RadmanCT\Integrations\Elementor\Navigation\PanelAtomic::get_props_schema();
expect( isset( $panel_schema['panelTemplateId'] ) && ! isset( $panel_schema['menuId'] ), 'Independent Atomic panel is coupled to menu props.' );
$panel_controls = ( new \ReflectionMethod( \RadmanCT\Integrations\Elementor\Navigation\PanelAtomic::class, 'define_atomic_controls' ) )->invoke( new \RadmanCT\Integrations\Elementor\Navigation\PanelAtomic() );
expect( 2 === count( $panel_controls ), 'Independent panel retains an empty menu section.' );
expect( 'elementor' === \RadmanCT\Integrations\Elementor\Navigation\panel_options( array() )['panelContentType'], 'Elementor panel does not default to an empty template selection.' );
echo "Passed: Classic/Atomic navigation controls, native typed schema, integer choices, shared device settings and invalid input boundaries.\n";
}
