<?php
define( 'ABSPATH', __DIR__ );
function __( $value, $domain ) { return $value; }
require dirname( __DIR__ ) . '/radmanct-components/inc/components/presentation.php';
use function RadmanCTComponents\Components\Presentation\render;
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
$html = '<div class="rdct-ib"><svg></svg></div>';
expect( render( $html, array(), 'icon-box' ) === $html, 'Unchanged component should not emit responsive CSS.' );
expect( render( $html, array( 'icon_size_responsive' => array( '$$type' => 'number', 'value' => 20, 'disabled' => true ) ), 'icon-box' ) === $html, 'Disabled presentation data must inherit rather than override.' );
$mobile = render( $html, array( 'icon_size_responsive' => array( 'mobile' => 19 ), 'icon_padding_responsive' => array( 'mobile' => 0 ) ), 'icon-box' );
expect( str_contains( $mobile, '@media(max-width:767px)' ) && str_contains( $mobile, 'inline-size:19px;block-size:19px;' ) && str_contains( $mobile, 'padding:0px;' ), 'Mobile sizes or zero spacing were lost.' );
expect( ! str_contains( $mobile, '@media(max-width:1024px)' ), 'Missing tablet override should inherit.' );
$classic = render( $html, array( 'icon_size_responsive_mobile' => array( 'size' => 19, 'unit' => 'px' ), 'icon_padding_responsive_mobile' => array( 'size' => 0 ) ), 'icon-box' );
expect( $classic === $mobile, 'Classic and PHP/Atomic did not produce the same bounded CSS.' );
$typed = render( $html, array( 'icon_size_responsive' => array( '$$type' => 'rdct-responsive', 'value' => array( 'mobile' => array( '$$type' => 'number', 'value' => 19 ) ) ) ), 'icon-box' );
expect( str_contains( $typed, 'inline-size:19px' ), 'Typed Atomic payload was not resolved.' );
$invalid = render( $html, array( 'align_responsive' => array( 'mobile' => 'start;}body{display:none' ), 'icon_size_responsive' => array( 'mobile' => INF ) ), 'icon-box' );
expect( $invalid === $html, 'Untrusted CSS entered the output.' );
$bounded = render( $html, array( 'icon_size_responsive' => array( 'tablet' => 999 ), 'layout_responsive' => array( 'mobile' => 'top' ) ), 'icon-box' );
expect( str_contains( $bounded, 'inline-size:128px' ) && str_contains( $bounded, 'grid-row:3' ), 'Bounds or stacked layout were lost.' );
echo "Passed: shared responsive inheritance, Classic/Atomic payloads, zero values, bounds and CSS injection boundary.\n";
$alignment = render( $html, array( 'icon_side' => 'end', 'vertical_align' => 'end', 'align_responsive' => array( 'mobile' => 'justify' ), 'icon_side_responsive' => array( 'mobile' => 'start' ), 'vertical_align_responsive' => array( 'mobile' => 'auto' ), 'layout_responsive' => array( 'tablet' => 'side', 'mobile' => 'top' ) ), 'icon-box' );
expect( str_contains( $alignment, '--rdct-ib-icon-col:2;' ) && str_contains( $alignment, '--rdct-ib-icon-col:1;' ) && str_contains( $alignment, 'var(--rdct-ib-text-col,2)' ), 'Independent icon side or responsive layout lost its logical grid column.' );
expect( str_contains( $alignment, '--rdct-ib-valign:end;' ) && str_contains( $alignment, '--rdct-ib-valign:initial;' ) && str_contains( $alignment, 'text-align:justify;--rdct-ib-align:start;' ) && ! str_contains( $alignment, 'justify-items:justify' ), 'Vertical reset or justified text emitted an invalid grid alignment.' );
$icon = render( '<svg class="rdct-ico"></svg>', array( 'icon_align' => 'center', 'icon_align_responsive_mobile' => 'end' ), 'icon' );
expect( str_contains( $icon, 'display:block;margin-inline-start:auto;margin-inline-end:auto;' ) && str_contains( $icon, 'margin-inline-end:0;' ), 'Standalone icon lost logical responsive alignment.' );
expect( render( $html, array( 'icon_side' => 'right;}body{', 'vertical_align' => array() ), 'icon-box' ) === $html, 'Invalid alignment entered CSS.' );
$curtain = render( '<div class="rdct-curtain"></div>', array( 'gap_responsive' => array( 'mobile' => 8 ), 'padding_responsive_mobile' => array( 'size' => 0 ) ), 'curtain' );
expect( str_contains( $curtain, 'gap:8px;' ) && str_contains( $curtain, 'padding:0px;' ) && str_contains( $curtain, '@media(max-width:767px)' ), 'Curtain spacing did not retain mobile overrides or zero padding.' );
$flip = render( '<div class="rdct-fb" style="--rdct-fb-height:300px;color:red;"></div>', array( 'height_responsive' => array( 'mobile' => 500 ) ), 'flipbox' );
expect( str_contains( $flip, '--rdct-fb-height:300px;' ) && str_contains( $flip, '--rdct-fb-height:500px;' ) && ! preg_match( '/style="[^"]*--rdct-fb-height/', $flip ) && str_contains( $flip, 'color:red;' ), 'Inline defaults blocked a shared component mobile override or unrelated styling was lost.' );
$flip_other = render( '<div class="rdct-fb" style="--rdct-fb-height:450px;"></div>', array( 'height_responsive' => array( 'mobile' => 500 ) ), 'flipbox' );
preg_match( '/class="([^"]+)"/', $flip, $first_class ); preg_match( '/class="([^"]+)"/', $flip_other, $second_class );
expect( $first_class[1] !== $second_class[1], 'Different desktop fallbacks shared the same generated class.' );
$native_text = render( '<h2 class="rdct-text"></h2>', array( 'font_size_responsive' => array( 'size' => 2.5, 'unit' => 'rem' ), 'word_spacing_responsive_mobile' => array( 'size' => -.2, 'unit' => 'em' ) ), 'text-reveal' );
expect( str_contains( $native_text, 'font-size:2.5rem;' ) && str_contains( $native_text, 'word-spacing:-0.2em;' ), 'Native desktop/mobile text size values were dropped.' );
$atomic_text = render( '<h2 class="rdct-text"></h2>', array( 'font_size_responsive' => array( '$$type' => 'rdct-responsive', 'value' => array( 'desktop' => array( '$$type' => 'size', 'value' => array( 'size' => array( '$$type' => 'number', 'value' => 2.5 ), 'unit' => array( '$$type' => 'string', 'value' => 'rem' ) ) ) ) ) ), 'text-reveal' );
expect( str_contains( $atomic_text, 'font-size:2.5rem;' ), 'Typed native Atomic sizes were dropped.' );
expect( render( '<h2></h2>', array( 'font_size_responsive_mobile' => array( 'size' => 24, 'unit' => 'px;display:none' ) ), 'text-reveal' ) === '<h2></h2>', 'Invalid size unit entered CSS.' );
