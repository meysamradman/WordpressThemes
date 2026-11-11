<?php

require __DIR__ . '/curtain-contract.php';
function esc_url_raw( $value, $protocols ) { return preg_match( '~^javascript:~i', $value ) ? '' : $value; }
function esc_url( $value ) { return esc_attr( $value ); }
require __DIR__ . '/../radmanct-components/inc/components/icon-box.php';
require __DIR__ . '/../radmanct-components/inc/icons.php';
$s = array( 'title' => '<b>Service</b>', 'title_tag' => 'script', 'text' => '<p>Details</p>', 'url' => 'javascript:alert(1)', 'icon_color' => '#fff;display:none', 'layout' => 'side', 'icon_size' => 900, 'animation' => 'draw', 'animation_target' => 'parts' );
$html = \RadmanCTComponents\Components\IconBox\render( $s );
expect( str_contains( $html, 'rdct-ib--side' ) && str_contains( $html, '<h3' ) && str_contains( $html, 'Service' ) && str_contains( $html, '<p>Details</p>' ), 'Icon Box content/layout failed.' );
expect( ! str_contains( $html, 'javascript:' ) && ! str_contains( $html, 'display:none' ) && ! str_contains( $html, '<script' ) && str_contains( $html, '--rdct-ib-icon-size:128px' ), 'Invalid style or link reached output.' );
expect( str_contains( $html, 'pathLength="100"' ) && str_contains( $html, 'rdct-ib--motion-draw' ) && str_contains( $html, 'rdct-ib--target-parts' ), 'SVG animation geometry/selection missing.' );
$image = \RadmanCTComponents\Components\IconBox\render( array( 'icon_type' => 'image', 'image_id' => 7 ) );
expect( str_contains( $image, 'data-id="7"' ) && ! str_contains( $image, '<svg' ), 'Image mode failed.' );
expect( '' === \RadmanCTComponents\Components\IconBox\render( array( 'title' => '' ) ), 'Empty component should not render.' );
$GLOBALS['curtain_image_calls'] = array();
\RadmanCTComponents\Components\IconBox\render( array( 'title' => 'Image', 'icon_type' => 'image', 'image_id' => 7, 'image' => array( 'width' => 320, 'height' => 180 ) ) );
expect( array( array( 7, array( 320, 180 ) ) ) === $GLOBALS['curtain_image_calls'], 'Icon Box lost native image resolution.' );
echo "Passed: Icon Box content, local icon/image modes, safe options and SVG animation geometry.\n";
