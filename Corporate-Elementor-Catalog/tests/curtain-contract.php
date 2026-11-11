<?php
define( 'ABSPATH', '/' );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function __( $text, $domain ) { return $text; }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function wp_kses_post( $text ) { return strip_tags( $text, '<p><b><strong>' ); }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return esc_attr( $text ); }
function wp_unique_id( $prefix ) { static $id = 0; return $prefix . ++$id; }
function apply_filters( $hook, $value, ...$args ) { return $value; }
function wp_attachment_is_image( $id ) { return 7 === $id; }
function wp_get_attachment_image( $id, $size, $icon, $attributes ) { $GLOBALS['curtain_image_calls'][] = array( $id, $size ); return '<img data-id="' . $id . '" data-size="' . ( is_array( $size ) ? implode( 'x', $size ) : $size ) . '" class="' . $attributes['class'] . '">'; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require __DIR__ . '/../radmanct-components/inc/components/curtain.php';
$items = array( array( 'title' => '<b>Service</b>', 'content' => '<p>Details</p>', 'image_id' => 7, 'background_id' => 99, 'background' => 'red;url(bad)' ), array( 'title' => 'Next' ) );
$html = \RadmanCTComponents\Components\Curtain\render( $items, array( 'heading_tag' => 'script', 'active_item' => 99, 'gap' => -20 ) );
expect( str_contains( $html, '<h3' ) && str_contains( $html, 'data-active-item="2"' ) && str_contains( $html, '--rdct-curtain-gap:0px' ), 'Options bypassed bounds or safe heading tags.' );
expect( ! str_contains( $html, 'url(bad)' ) && str_contains( $html, 'data-id="7"' ) && ! str_contains( $html, 'data-id="99"' ), 'Invalid color or attachment reached output.' );
expect( 2 === substr_count( $html, 'aria-expanded="true"' ) && ! str_contains( $html, 'aria-hidden="true"' ), 'No-JS content is hidden.' );
preg_match_all( '/ id="([^"]+)"/', $html, $matches );
expect( count( $matches[1] ) === count( array_unique( $matches[1] ) ), 'Curtain control IDs are duplicated.' );
$second = \RadmanCTComponents\Components\Curtain\render( $items );
expect( ! str_contains( $second, $matches[1][0] . '"' ), 'Different component instances reused control IDs.' );
expect( 12 === count( \RadmanCTComponents\Components\Curtain\items( array_fill( 0, 20, $items[0] ) ) ) && '' === \RadmanCTComponents\Components\Curtain\render( array() ), 'Unbounded or empty component output.' );
$GLOBALS['curtain_image_calls'] = array();
\RadmanCTComponents\Components\Curtain\render( array( array( 'title' => 'Image', 'image_id' => array( 'id' => 7, 'width' => 320, 'height' => 180 ), 'background_id' => array( 'id' => 7, 'width' => 640, 'height' => 360 ) ) ) );
expect( array( array( 7, array( 640, 360 ) ), array( 7, array( 320, 180 ) ) ) === $GLOBALS['curtain_image_calls'], 'Curtain lost independent native image sizes.' );
$images = \RadmanCTComponents\Components\Curtain\render( $items, array( 'layout' => 'images', 'expanded_ratio' => 99, 'trigger' => 'hover' ) );
expect( str_contains( $images, 'data-layout="images"' ) && str_contains( $images, 'data-ratio="8"' ), 'Image layout or ratio bounds were lost.' );
expect( 2 === substr_count( $images, 'aria-pressed="false"' ) && ! str_contains( $images, 'aria-hidden' ) && ! str_contains( $images, 'aria-expanded' ), 'Image captions must remain accessible when selection changes.' );
require_once __DIR__ . '/../radmanct-components/inc/components.php';
expect( array( 'curtain-images' ) === \RadmanCTComponents\Components\requirements( 'curtain', array( 'layout' => 'images' ) )['styles'] && array( 'curtain' ) === \RadmanCTComponents\Components\requirements( 'curtain' )['styles'], 'Image and legacy panel CSS were not isolated.' );
echo "Passed: bounded curtain options, images, safe headings, unique IDs, accessible captions and isolated layout CSS.\n";
