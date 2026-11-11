<?php
namespace RadmanCTComponents { function url( $path ) { return '/plugins/radmanct-components/' . $path; } }
namespace {
define( 'ABSPATH', '/usr/src/wordpress/' );
function __( $s, $d ) { return $s; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $s, $d ) { return esc_attr( $s ); }
function esc_url_raw( $s, $p = array() ) { return filter_var( $s, FILTER_SANITIZE_URL ); }
function esc_url( $s ) { return esc_attr( $s ); }
function sanitize_text_field( $s ) { return strip_tags( $s ); }
function wp_kses_post( $s ) { return strip_tags( $s, '<p><b><em>' ); }
function wp_strip_all_tags( $s ) { return strip_tags( $s ); }
function apply_filters( $tag, $id, ...$args ) { return 1 === $id ? 2 : $id; }
function wp_attachment_is_image( $id ) { return 2 === $id; }
function wp_get_attachment_image( $id, $size, $icon, $attrs ) { $GLOBALS['image_calls'][] = array( 'id' => $id, 'size' => $size, 'attributes' => $attrs ); return '<img src="/image.jpg" width="1200" height="800" class="' . esc_attr( $attrs['class'] ?? '' ) . '" alt="Photo">'; }
function wp_get_attachment_image_url( $id, $size ) { return '/full.jpg'; }
function get_post_meta( $id, $key, $single ) { return 'Photo'; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct-components/inc/components/image.php';
$safe = \RadmanCTComponents\Components\Image\settings( array( 'width' => '1px;background:url(bad)', 'custom_radius' => 'url(bad)', 'shadow_blur' => 9999, 'lines_count' => 9999, 'float_custom_ease' => 'var(--unsafe)', 'url' => 'javascript:bad()', 'overlay_color' => 'red;position:fixed', 'float' => array() ) );
expect( '100%' === $safe['width'] && '20px' === $safe['custom_radius'] && 200 === $safe['shadow_blur'] && 20 === $safe['lines_count'] && 'ease' === $safe['float_custom_ease'] && '' === $safe['url'] && false === $safe['float'], 'Unsafe values bypassed image normalization.' );
$basic = \RadmanCTComponents\Components\Image\render( array( 'image_id' => 1 ) );
expect( str_contains( $basic, '/image.jpg' ) && ! str_contains( $basic, 'data-rdct-image' ) && ! str_contains( $basic, 'rdct-im__link' ), 'Plain image needs motion or bypasses WPML attachment lookup.' );
expect( '' === \RadmanCTComponents\Components\Image\render( array( 'image_id' => 999 ) ), 'Invalid attachment accepted.' );
$html = \RadmanCTComponents\Components\Image\render( array( 'image_id' => 1, 'reveal' => true, 'float' => true, 'tilt' => true, 'lightbox' => true, 'lightbox_group' => 'gallery" onclick="bad', 'label_enabled' => true, 'label' => '<p><b>Label</b></p>', 'lines' => true, 'lines_count' => 3, 'shadow' => 'depth4', 'overlay' => true, 'overlay_background' => 'linear', 'overlay_stop' => 80, 'overlay_second_stop' => 20 ) );
expect( str_contains( $html, 'href="/full.jpg"' ) && str_contains( $html, 'data-rdct-image-lightbox=' ) && ! str_contains( $html, ' onclick="bad' ) && 3 === substr_count( $html, '<i></i>' ), 'Link/line/group contract failed.' );
expect( str_contains( $html, 'linear-gradient(180deg,#00000080 80%,#00000000 80%)' ) && str_contains( $html, '<b>Label</b>' ), 'Background bounds or allowed label markup lost.' );
expect( array( 'image' ) === \RadmanCTComponents\Components\Image\assets( array() ) && array( 'image', 'image-reveal', 'image-lightbox' ) === \RadmanCTComponents\Components\Image\assets( array( 'reveal' => true, 'lightbox' => true ) ), 'Unused image assets requested.' );
expect( array( 'image', 'image-parallax' ) === \RadmanCTComponents\Components\Image\assets( array( 'parallax' => true ) ), 'Parallax loads unrelated image effects.' );
$parallax = \RadmanCTComponents\Components\Image\render( array( 'image_id' => 1, 'parallax' => true, 'parallax_amount' => 99, 'parallax_direction' => 'down', 'parallax_min_width' => 0 ) );
expect( str_contains( $parallax, 'data-parallax="true"' ) && str_contains( $parallax, 'data-parallax-amount="30"' ) && str_contains( $parallax, 'data-parallax-direction="down"' ) && str_contains( $parallax, 'data-parallax-min-width="0"' ), 'Parallax options were lost or unbounded.' );
expect( 1 === substr_count( $parallax, '<img ' ) && str_contains( $parallax, 'rdct-im__parallax-frame' ) && ! str_contains( $basic, 'rdct-im__parallax-frame' ), 'Image cloning or unused parallax markup introduced.' );
echo "Passed: image bounds, escaping, attachments/WPML, effects, group links and conditional assets.\n";
}
