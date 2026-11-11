<?php
require __DIR__ . '/image-contract.php';
require_once __DIR__ . '/../radmanct-components/inc/components/image-media.php';
function esc_html( $value ) { return esc_attr( $value ); }
function wp_upload_dir( $time = null, $create = true ) { return array( 'baseurl' => 'https://local.test/wp-content/uploads' ); }
function attachment_url_to_postid( $url ) { $GLOBALS['media_lookups'][] = $url; return 'https://local.test/wp-content/uploads/2026/10/photo.webp' === $url ? 1 : 0; }
use RadmanCTComponents\Components\ImageMedia;
$native = array( 'src' => 'https://local.test/wp-content/uploads/2026/10/photo-1024x683.webp', 'width' => 1024, 'height' => 683, 'alt' => 'Native image' );
$settings = ImageMedia\editor_images( array( 'image' => $native, 'items' => array( array( 'image_id' => $native ) ), 'background_image' => $native, 'overlay_image' => $native ) );
expect( 1 === $settings['image']['id'] && 1 === $settings['items'][0]['image_id']['id'] && 1 === $settings['background_image']['id'], 'ID-free Atomic images were lost in top-level or repeated fields.' );
expect( array( 1024, 683 ) === ImageMedia\size( $settings['image'] ), 'Resolved native image dimensions were discarded.' );
expect( 2 === count( $GLOBALS['media_lookups'] ), 'Repeated media did not reuse its request-local lookup.' );
expect( 7 === ImageMedia\attachment_id( array( 'id' => 7, 'src' => 'https://other.test/a.jpg' ) ), 'Existing attachment ID was overwritten by a URL.' );
expect( 0 === ImageMedia\attachment_id( array( 'src' => 'https://other.test/wp-content/uploads/photo.webp' ) ) && 2 === count( $GLOBALS['media_lookups'] ), 'External sources reached media resolution.' );
$html = \RadmanCTComponents\Components\Image\render( array( 'image_id' => $settings['image']['id'] ) );
expect( str_contains( $html, 'src="/image.jpg"' ) && ! str_contains( $html, 'images/components/image.svg' ), 'Selected native media still renders the library placeholder.' );
require __DIR__ . '/../radmanct-components/inc/components/timeline.php';
$html = \RadmanCTComponents\Components\Timeline\render( array( array( 'title' => 'Native selection', 'image_id' => $native ) ) );
expect( str_contains( $html, '<figure' ) && str_contains( $html, 'src="/image.jpg"' ), 'Timeline dropped the real Atomic canvas media value.' );
echo "Passed: ID-free Atomic canvas images, sized local URL resolution, cache, WPML rendering, repeated/background fields and Timeline image output.\n";
