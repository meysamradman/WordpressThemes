<?php

define( 'ABSPATH', __DIR__ );
function __( $text, $domain = '' ) { return $text; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_hex_color( $value ) { return preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value ) ? $value : null; }
function apply_filters( $hook, $value, ...$args ) { return $value; }
function wp_get_attachment_image_url( $id, $size ) { return 7 === $id ? 'https://example.test/uploads/particle.png' : 'https://external.test/particle.png'; }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function home_url() { return 'https://example.test'; }
function wp_unique_id( $prefix ) { static $counter = 0; return $prefix . ++$counter; }
function sanitize_html_class( $value ) { return preg_replace( '/[^a-zA-Z0-9_-]/', '', $value ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
require dirname( __DIR__ ) . '/radmanct-components/inc/components/particles.php';

use RadmanCTComponents\Components\Particles;

function check( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$s = Particles\settings( array( 'number' => 999999, 'opacity' => array( 'size' => 0 ), 'anim_opacity_min' => array( 'size' => .8 ), 'size' => 0, 'anim_size_min' => 99, 'enable_anim_sync' => 'yes', 'shape_type' => array( 'polygon', 'image', array(), 'script' ), 'multi_color_values' => array( array( 'scolor' => '#abc' ), array( 'scolor' => 'url(evil)' ) ) ) );
check( 200.0 === (float) $s['number'], 'Particle budget' );
check( 0.0 === (float) $s['opacity'] && 0.0 === (float) $s['anim_opacity_min'] && 0.0 === (float) $s['anim_size_min'], 'Slider zero and bounded minima' );
check( array( 'polygon', 'image' ) === $s['shape_type'], 'Shape whitelist without array warnings' );
check( array( '#abc' ) === $s['multi_color_values'], 'Color sanitization' );
$options = Particles\options( $s );
check( true === $options['particles']['opacity']['anim']['sync'], 'Independent opacity sync' );
check( array( 'polygon' ) === $options['particles']['shape']['type'], 'Missing image fallback' );
check( is_array( $options['particles']['number']['density'] ), 'Density object' );
foreach ( array( 7 => true, 8 => false ) as $id => $local ) {
	$options = Particles\options( Particles\settings( array( 'image' => array( 'id' => $id ), 'shape_image' => true ) ) );
	check( $local === ( '' !== $options['particles']['shape']['image']['src'] ), 'Only local attachment URLs' );
}
$html = Particles\render( array( 'particle_id' => '"><script>', 'height' => '1px;position:fixed', 'height_tablet' => '60vh' ) );
check( ! str_contains( $html, '<script>' ) && ! str_contains( $html, 'position:fixed' ), 'Escaped ID and safe height' );
check( str_contains( $html, '--rdct-pt-height:450px' ) && str_contains( $html, '--rdct-pt-height-tablet:60vh' ), 'Height fallback and override' );
check( Particles\render( array( 'particle_id' => 'same' ) ) !== Particles\render( array( 'particle_id' => 'same' ) ), 'Unique cloned IDs' );
echo "Particles PHP contract passed.\n";
