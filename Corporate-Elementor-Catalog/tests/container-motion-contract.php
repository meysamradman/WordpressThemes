<?php
namespace Elementor { class Widget_Base {} class Controls_Manager { const TAB_ADVANCED = 'advanced'; const SWITCHER = 'switcher'; const SELECT = 'select'; const TEXT = 'text'; const NUMBER = 'number'; const BUTTON = 'button'; const HIDDEN = 'hidden'; const RAW_HTML = 'raw_html'; const SLIDER = 'slider'; const POPOVER_TOGGLE = 'popover_toggle'; } }
namespace {
define( 'ABSPATH', '/usr/src/wordpress/' );
function __( $text, $domain ) { return $text; }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value, ...$args ) { return $value; }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
spl_autoload_register( static function ( $class ) {
	$prefix = 'Elementor\\';
	if ( ! str_starts_with( $class, $prefix ) ) { return; }
	$parts = explode( '\\', substr( $class, strlen( $prefix ) ) );
	$parts = array_map( static fn( $part ) => strtolower( str_replace( '_', '-', preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $part ) ) ), $parts );
	$file = dirname( __DIR__ ) . '/themes/elementor/' . implode( '/', $parts ) . '.php';
	if ( is_file( $file ) ) { require $file; }
} );
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/container-motion.php';
$fields = \RadmanCT\Components\ContainerMotion\fields();
expect( 'top bottom-=25%' === \RadmanCT\Components\ContainerMotion\scroll_position( 'top bottom-=25%', 'default' ), 'Custom scroll position rejected a supported offset.' );
expect( 'default' === \RadmanCT\Components\ContainerMotion\scroll_position( '()=>alert(1)', 'default' ), 'Custom scroll position accepted executable input.' );
$easings = json_decode( file_get_contents( dirname( __DIR__ ) . '/radmanct/inc/components/motion-easings.json' ), true, 16, JSON_THROW_ON_ERROR );
foreach ( $easings as $ease ) {
	$normalized = \RadmanCT\Components\ContainerMotion\normalize( array( 'ease' => $ease, 'parallax_ease' => $ease ) );
	expect( $ease === $normalized['ease'] && $ease === $normalized['parallax_ease'], 'Shared reference easing was lost during PHP normalization.' );
}
$defaults = \RadmanCT\Components\ContainerMotion\normalize( array() );
expect( 'custom' === $defaults['preset'] && 0.0 === (float) $defaults['from_y'] && 1.0 === (float) $defaults['from_opacity'] && 0.0 === (float) $defaults['parallax_from_y'] && 0.0 === (float) $defaults['parallax_to_y'], 'Hub custom defaults are not neutral.' );
$full_range = \RadmanCT\Components\ContainerMotion\normalize( array( 'duration' => 0, 'stagger' => 8, 'to_scaleX' => 5, 'from_x' => 900 ) );
expect( 0.0 === (float) $full_range['duration'] && 8.0 === (float) $full_range['stagger'] && 5.0 === (float) $full_range['to_scaleX'] && 900.0 === (float) $full_range['from_x'], 'Expanded ranges or legacy numeric values were lost.' );
foreach ( json_decode( file_get_contents( __DIR__ . '/fixtures/motion-values.json' ), true, 16, JSON_THROW_ON_ERROR ) as $case ) {
	$actual = \RadmanCT\Components\ContainerMotion\normalize( $case['input'] );
	foreach ( $case['expected'] as $key => $value ) { expect( $actual[ $key ] === $value, 'PHP/JS motion value fixture mismatch: ' . $key ); }
}
expect( ! $defaults['animate'] && ! $defaults['parallax'] && '' === \RadmanCT\Components\ContainerMotion\attributes( array() ), 'Disabled motion produced runtime markup.' );
$safe = \RadmanCT\Components\ContainerMotion\normalize( array( 'animate' => 'yes', 'duration' => 999, 'from_x' => 'expression(bad)', 'ease' => 'run()', 'to_opacity' => -8 ) );
expect( $safe['animate'] && 10 === $safe['duration'] && 0 === $safe['from_x'] && 'power4.out' === $safe['ease'] && 0 === $safe['to_opacity'], 'Motion limits or whitelist failed.' );
$schema = \RadmanCT\Integrations\Elementor\ContainerMotion\atomic_schema( array() );
expect( isset( $schema['rm_motion_horizontal'], $schema['rm_motion_horizontal_width'] ), 'Pinned horizontal Atomic props missing.' );
expect( str_contains( \RadmanCT\Components\ContainerMotion\attributes( array( 'horizontal' => true ) ), 'data-rdct-motion' ), 'Horizontal-only PHP settings did not activate.' );
expect( count( $schema ) === count( $fields ) && $schema['rm_motion_animate'] instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type, 'Native Atomic schema did not register typed fields.' );
json_encode( $schema, JSON_THROW_ON_ERROR );
$element = new class extends \Elementor\Widget_Base {
	public string $name = 'e-flexbox'; public array $settings = array(); public array $controls = array(); public array $sections = array(); public $interactions = null;
	public function get_name() { return $this->name; }
	public function get_data( $key = null ) { return $this->interactions; }
	public function get_controls( $key ) { return $this->controls[ $key ] ?? null; }
	public function start_popover() {}
	public function end_popover() {}
	public function get_settings() { return $this->settings; }
	public function start_controls_section( $key, $options ) { $this->sections[ $key ] = $options; $this->controls[ $key ] = $options; }
	public function end_controls_section() {}
	public function add_control( $key, $value ) { $this->controls[ $key ] = $value; }
};
$atomic = \RadmanCT\Integrations\Elementor\ContainerMotion\atomic_controls( array(), $element );
expect( 2 === count( $atomic ), 'Atomic motion groups were not registered.' );
json_encode( $atomic, JSON_THROW_ON_ERROR );
$element->settings = array( 'rm_motion_animate' => array( '$$type' => 'boolean', 'value' => true ), 'rm_motion_duration' => array( '$$type' => 'number', 'value' => 2.5 ) );
expect( 2.5 === \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element )['duration'], 'Atomic typed numeric value was lost.' );
$original = $element->settings;
$element->settings['rm_motion_animate']['disabled'] = true;
$element->settings['rm_motion_duration'] = array( '$$type' => 'string', 'value' => 9 );
$element->settings['rm_motion_inner'] = array( '$$type' => 'boolean', 'value' => false, 'disabled' => true );
$inactive = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( false === $inactive['animate'] && 1.6 === $inactive['duration'] && true === $inactive['inner'], 'Atomic disabled/type mismatch values did not use shared defaults.' );
$level = ob_get_level();
\RadmanCT\Integrations\Elementor\ContainerMotion\before( $element );
expect( $level === ob_get_level(), 'Disabled Atomic setting activated runtime capture.' );
$element->settings = $original;
$element->name = 'container'; \RadmanCT\Integrations\Elementor\ContainerMotion\classic_controls( $element );
expect( isset( $element->controls['rm_motion_reveal'], $element->controls['rm_motion_reveal_play'] ), 'Classic reveal controls missing.' );
foreach ( $fields as $key => $field ) {
	expect( isset( $element->controls[ 'rm_motion_' . $key ] ), 'A shared motion field is missing from Classic: ' . $key );
	if ( isset( $field['unit_for'] ) ) { continue; }
	$slider = \RadmanCT\Integrations\Elementor\ContainerMotion\slider_field( $key, $field );
	$control = $element->controls[ 'rm_motion_' . $key . ( $slider ? '_slider' : '' ) ];
	$expected_type = 'boolean' === ( $field['type'] ?? '' ) ? \Elementor\Controls_Manager::SWITCHER : ( 'text' === ( $field['type'] ?? '' ) ? \Elementor\Controls_Manager::TEXT : ( isset( $field['options'] ) ? \Elementor\Controls_Manager::SELECT : ( $slider ? \Elementor\Controls_Manager::SLIDER : \Elementor\Controls_Manager::NUMBER ) ) );
	expect( $expected_type === $control['type'], 'A shared motion field lost its native control: ' . $key );
}
$element->settings = array( 'rm_motion_reveal' => array( '$$type' => 'boolean', 'value' => true ), 'rm_motion_reveal_start' => array( '$$type' => 'number', 'value' => 50 ), 'rm_motion_reveal_end' => array( '$$type' => 'number', 'value' => 80 ) );
$reveal = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( $reveal['reveal'] && 45.0 === (float) $reveal['reveal_end'] && str_contains( \RadmanCT\Components\ContainerMotion\attributes( $reveal ), 'data-rdct-motion' ), 'Reveal-only typed settings or ordered range failed.' );
expect( isset( $element->controls['rm_motion_play'], $element->controls['rm_motion_parallax_from_rotationX'] ), 'Classic controls or Play were not registered.' );
expect( isset( $element->controls['rm_motion_parallax_play'], $element->controls['rm_motion_preview_reset'] ) && 'rm_motion_reset' === $element->controls['rm_motion_preview_reset']['event'], 'Parallax Play or preview Reset is missing.' );
expect( \Elementor\Controls_Manager::TEXT === $element->controls['rm_motion_parallax_start_custom']['type'] && \Elementor\Controls_Manager::SLIDER === $element->controls['rm_motion_parallax_distance_slider']['type'], 'Native custom trigger or distance fields are missing.' );
expect( 'none' === $element->controls['rm_motion_duration_slider']['render_type'] && 'none' === $element->controls['rm_motion_targets']['render_type'], 'Live motion controls still request a full widget render.' );
expect( -500 === $element->controls['rm_motion_from_x_slider']['range']['px']['min'] && 500 === $element->controls['rm_motion_from_x_slider']['range']['px']['max'], 'Translation UI range differs from Hub.' );
expect( -360 === $element->controls['rm_motion_from_rotationX_slider']['range']['deg']['min'] && 360 === $element->controls['rm_motion_from_rotationX_slider']['range']['deg']['max'], 'Rotation UI range differs from Hub.' );
expect( \Elementor\Controls_Manager::SLIDER === $element->controls['rm_motion_from_x_slider']['type'] && \Elementor\Controls_Manager::HIDDEN === $element->controls['rm_motion_from_x']['type'], 'Transform range did not preserve its legacy value behind a native slider.' );
$element->settings = array( 'rm_motion_from_x' => 45, 'rm_motion_from_x_slider' => array( 'size' => 0, 'unit' => 'px' ) );
expect( 0.0 === (float) \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element )['from_x'], 'Explicit zero in a transform slider was lost.' );
$element->settings['rm_motion_from_x_slider']['size'] = '';
expect( 45.0 === (float) \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element )['from_x'], 'Empty transform slider did not retain the legacy setting.' );
expect( array( '%', 'px' ) === $element->controls['rm_motion_from_originX_slider']['size_units'] && 'yes' === $element->controls['rm_motion_from_originX_slider']['condition']['rm_motion_from_origin'], 'Native origin units or conditional visibility are missing.' );
$element->settings = array( 'rm_motion_from_origin' => 'yes', 'rm_motion_from_originX_slider' => array( 'size' => 0, 'unit' => 'px' ), 'rm_motion_from_originY_slider' => array( 'size' => -35.5, 'unit' => 'px' ) );
$origin = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( $origin['from_origin'] && 0.0 === (float) $origin['from_originX'] && 'px' === $origin['from_originX_unit'] && -35.5 === $origin['from_originY'], 'Classic origin sliders lost explicit zero, decimals or units.' );
$element->settings['rm_motion_from_originX'] = 20;
$element->settings['rm_motion_from_originX_unit'] = 'px';
$element->settings['rm_motion_from_originX_slider'] = array( 'size' => '', 'unit' => '%' );
$origin = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( 'px' === $origin['from_originX_unit'] && 20.0 === (float) $origin['from_originX'], 'An empty UI slider overwrote the saved origin unit.' );
$origin = \RadmanCT\Components\ContainerMotion\normalize( array( 'from_originX' => 800, 'from_originX_unit' => '%', 'to_originY' => -800, 'to_originY_unit' => 'px' ) );
expect( 100.0 === (float) $origin['from_originX'] && -500.0 === (float) $origin['to_originY'], 'Origin bounds differ from the reference units.' );
expect( array( 'px', '%', 'vw', 'vh' ) === $element->controls['rm_motion_from_x_slider']['size_units'], 'Translation slider lost reference units.' );
$element->settings = array( 'rm_motion_from_x' => 45, 'rm_motion_from_x_slider' => array( 'size' => -12.5, 'unit' => 'vw' ) );
$translated = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( -12.5 === $translated['from_x'] && 'vw' === $translated['from_x_unit'], 'Translation slider lost its viewport unit or decimal value.' );
$element->settings = array( 'rm_motion_animate' => 'yes' );
expect( \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element )['animate'], 'Classic toggle was not normalized.' );
$element->name = 'e-heading';
expect( 1 === count( \RadmanCT\Integrations\Elementor\ContainerMotion\atomic_controls( array(), $element ) ), 'Atomic widget must have motion without container scroll controls.' );
expect( 'self' === \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element )['targets'], 'Individual Atomic widget did not target itself.' );
expect( empty( $element->controls['rm_motion_animate']['condition'] ) && empty( $element->controls['rm_motion_parallax']['condition'] ), 'Content animation must remain independently visible from parallax.' );
expect( array( 'rm_motion_animate' => 'yes' ) === $element->controls['rm_motion_targets']['condition'], 'Content targets incorrectly depend on parallax.' );
expect( array( 'rm_motion_animate' => 'yes', 'rm_motion_targets' => 'contents' ) === $element->controls['rm_motion_inner']['condition'], 'Inner content toggle is attached to the wrong group.' );
expect( 'Animate from' === $element->controls['rm_motion_ui_from']['label'] && 'Animate to' === $element->controls['rm_motion_ui_to']['label'], 'Content From/To labels no longer match the reference.' );
if ( in_array( '--controls-only', $argv, true ) ) { echo "Passed: shared normalization, all Classic native fields, Atomic props/control groups and independent content animation/targets/Play/From/To. HTML runtime not exercised.\n"; exit; }
foreach ( array( 'compat', 'compat-utf8', 'utf8', 'class-wp-token-map' ) as $file ) { require ABSPATH . 'wp-includes/' . $file . '.php'; }
require ABSPATH . 'wp-includes/kses.php';
foreach ( array( 'html5-named-character-references', 'class-wp-html-attribute-token', 'class-wp-html-text-replacement', 'class-wp-html-span', 'class-wp-html-doctype-info', 'class-wp-html-decoder', 'class-wp-html-tag-processor' ) as $file ) { require ABSPATH . 'wp-includes/html-api/' . $file . '.php'; }
$element->name = 'e-flexbox';
$child = clone $element; $child->name = 'container';
ob_start();
\RadmanCT\Integrations\Elementor\ContainerMotion\before( $element );
echo '<section class="outer">';
\RadmanCT\Integrations\Elementor\ContainerMotion\before( $child );
echo '<div class="inner">Content</div>';
\RadmanCT\Integrations\Elementor\ContainerMotion\after( $child );
echo '</section>';
\RadmanCT\Integrations\Elementor\ContainerMotion\after( $element );
$html = ob_get_clean();
expect( 2 === substr_count( $html, 'data-rdct-motion=' ) && str_contains( $html, 'Content' ) && 1 === substr_count( $html, '<section' ), 'Nested Atomic/classic markup lost attributes, content or wrapper isolation.' );
$element->name = 'e-heading';
$element->settings = array( 'rm_motion_animate' => 'yes', 'rm_motion_duration' => 2.5, 'rm_motion_duration_slider' => array( 'size' => '' ), 'rm_motion_delay_slider' => array( 'size' => 0 ), 'rm_motion_horizontal' => 'yes', 'rm_motion_reveal' => 'yes' );
$options = \RadmanCT\Integrations\Elementor\ContainerMotion\options( $element );
expect( 'self' === $options['targets'] && ! $options['horizontal'] && ! $options['reveal'] && 2.5 === $options['duration'] && 0.0 === (float) $options['delay'], 'Widget scope or saved scalar/zero slider timing was lost.' );
$element->settings['_animation'] = 'fadeIn';
ob_start();
\RadmanCT\Integrations\Elementor\ContainerMotion\before( $element ); echo '<div>Native</div>'; \RadmanCT\Integrations\Elementor\ContainerMotion\after( $element );
$native = ob_get_clean();
expect( str_contains( $native, 'data-rdct-native-motion' ) && ! str_contains( $native, 'data-rdct-motion=' ) && 'yes' === $element->settings['rm_motion_animate'], 'Native ownership must exclude Radman runtime without deleting settings.' );
unset( $element->settings['_animation'] );
$element->interactions = json_encode( array( 'items' => array( array( 'id' => 'native' ) ) ) );
expect( \RadmanCT\Integrations\Elementor\ContainerMotion\native_motion( $element ), 'Native Atomic interactions were not detected.' );
$element->interactions = null;
$element->controls = array(); $element->sections = array();
\RadmanCT\Integrations\Elementor\ContainerMotion\classic_controls( $element );
expect( 1 === count( $element->sections ) && ! isset( $element->controls['rm_motion_horizontal'], $element->controls['rm_motion_targets'] ), 'Individual Classic widget gained container-only controls.' );
\RadmanCT\Integrations\Elementor\ContainerMotion\classic_controls( $element );
expect( 1 === count( $element->sections ), 'Classic motion controls registered twice.' );
$custom_widget = clone $element; $custom_widget->name = 'third-party-widget'; $custom_widget->controls = array(); $custom_widget->sections = array();
\RadmanCT\Integrations\Elementor\ContainerMotion\classic_section_controls( $custom_widget, 'content' );
expect( array() === $custom_widget->sections, 'Motion controls were injected into an unrelated content section.' );
\RadmanCT\Integrations\Elementor\ContainerMotion\classic_section_controls( $custom_widget, 'section_advanced' );
\RadmanCT\Integrations\Elementor\ContainerMotion\classic_section_controls( $custom_widget, '_section_style' );
expect( 1 === count( $custom_widget->sections ) && isset( $custom_widget->controls['rm_motion_animate'] ), 'A widget outside the common section route lost motion or registered it twice.' );
$element->settings = array(); $level = ob_get_level();
foreach ( json_decode( file_get_contents( __DIR__ . '/fixtures/native-motion-values.json' ), true, 32, JSON_THROW_ON_ERROR ) as $case ) {
	$element->settings = $case['settings']; $element->interactions = $case['interactions'] ?? null;
	expect( $case['expected'] === \RadmanCT\Integrations\Elementor\ContainerMotion\native_motion( $element ), 'Inactive typed native motion incorrectly claimed ownership.' );
}
$element->settings = array(); $element->interactions = null;
\RadmanCT\Integrations\Elementor\ContainerMotion\before( $element );
expect( $level === ob_get_level(), 'Disabled element opened an output buffer.' );
echo "Passed: bounded independent motion schema, disabled markup, native Atomic props/controls and classic settings parity.\n";
}
