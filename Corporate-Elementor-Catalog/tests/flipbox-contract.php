<?php

require __DIR__ . '/curtain-contract.php';
function esc_html__( $text, $domain ) { return esc_html( $text ); }
function get_post_type( $id ) { return 'elementor_library'; }
function get_post_status( $id ) { return 'draft'; }
require __DIR__ . '/../radmanct-components/inc/components/flipbox.php';
$options = array( 'front_content' => '<p>Front</p>', 'back_content' => '<p>Back</p>', 'front_image_id' => 7, 'back_image_id' => 99, 'direction' => 'tb', 'front_color' => '#fff;display:none', 'front_background_type' => 'gradient', 'front_angle' => 999, 'height' => -10 );
$html = \RadmanCTComponents\Components\FlipBox\render( $options );
expect( str_contains( $html, '<p>Front</p>' ) && str_contains( $html, '<p>Back</p>' ) && ! str_contains( $html, 'aria-hidden="true" inert' ), 'No-JS card lost one side.' );
expect( str_contains( $html, 'rdct-fb--tb' ) && str_contains( $html, '--rdct-fb-min-height:100px' ) && str_contains( $html, 'linear-gradient(360deg,' ) && ! str_contains( $html, 'display:none' ), 'FlipBox values bypassed validation.' );
expect( str_contains( $html, 'data-id="7"' ) && ! str_contains( $html, 'data-id="99"' ), 'Nonimage attachment reached the card.' );
expect( '' === \RadmanCTComponents\Components\FlipBox\template( 12 ), 'Nonpublished template rendered.' );
expect( ! str_contains( \RadmanCTComponents\Components\FlipBox\render( $options ), 'id="rdct-fb-1"' ), 'Card IDs were reused.' );
$GLOBALS['curtain_image_calls'] = array();
\RadmanCTComponents\Components\FlipBox\render( array( 'front_image_id' => 7, 'front_image' => array( 'width' => 320, 'height' => 180 ), 'back_image_id' => 7, 'back_image' => array( 'width' => 640, 'height' => 360 ) ) );
expect( array( array( 7, array( 320, 180 ) ), array( 7, array( 640, 360 ) ) ) === $GLOBALS['curtain_image_calls'], 'Flip Box lost independent face resolutions.' );
echo "Passed: both card faces, bounded style values, safe attachments, published-template guard and unique IDs.\n";
