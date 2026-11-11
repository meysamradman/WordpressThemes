<?php

require __DIR__ . '/carousel-contract.php';
function esc_attr__( $value, $domain ) { return esc_attr( $value ); }
require_once dirname( __DIR__ ) . '/radmanct-components/inc/components/hover-border.php';

use RadmanCTComponents\Components\HoverBorder;
use RadmanCTComponents\Components\Carousel;
use RadmanCTComponents\Components\TextReveal;

$before = $GLOBALS['enqueued'];
expect( '' === HoverBorder\attributes( array() ) && $before === $GLOBALS['enqueued'], 'Disabled border added assets.' );
ob_start(); $attr = HoverBorder\attributes( array( 'enabled' => true, 'width' => 999, 'color_end' => 'red;display:none' ) ); $output = ob_get_clean();
expect( '' === $output && str_starts_with( $attr, 'data-rdct-border=' ) && ! str_contains( $attr, '<link' ) && ! str_contains( $attr, 'display:none' ), 'Attribute helper emitted markup inside an opening tag or accepted CSS injection.' );
expect( isset( $GLOBALS['enqueued']['rdct-hover-border'] ), 'Enabled border has no scoped stylesheet.' );
$text = TextReveal\render( 'English [[work]]', array( 'mode' => 'marquee', 'direction' => 'rtl', 'marquee_speed' => 999 ) );
expect( str_contains( $text, 'data-rdct-text-reveal="marquee"' ) && str_contains( $text, 'dir="rtl"' ) && str_contains( $text, '&quot;marquee_speed&quot;:500' ) && 1 === substr_count( $text, 'class="rdct-marquee__group"' ), 'Text marquee lost direction, speed bound or duplicated SSR content.' );
$calls = 0;
$html = Carousel\render( array( array( 'render' => static function () use ( &$calls ) { ++$calls; return '<p>Original</p>'; } ) ), array( 'mode' => 'marquee', 'slides' => 3.2 ) );
expect( 1 === $calls && str_contains( $html, 'data-mode="marquee"' ) && ! str_contains( $html, 'data-previous' ), 'Continuous carousel reused slide controls or repeated PHP rendering.' );
expect( array( 'marquee', 'carousel-cards' ) === Carousel\assets( array( 'mode' => 'marquee' ) ) && in_array( 'marquee', TextReveal\assets( array( 'mode' => 'marquee' ) ), true ), 'Marquee loads Swiper CSS or has no selected CSS.' );
echo "Passed: conditional border attributes without markup, safe values, shared text/carousel marquee and single SSR rendering.\n";
