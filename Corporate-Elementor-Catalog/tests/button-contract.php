<?php

require __DIR__ . '/icon-box-contract.php';
function esc_attr__( $text, $domain ) { return esc_attr( __( $text, $domain ) ); }
require __DIR__ . '/../radmanct-components/inc/components/button.php';
require_once __DIR__ . '/../radmanct-components/inc/components.php';

use RadmanCTComponents\Components\Button;
use RadmanCTComponents\Components;

$plain = Button\render( array( 'label' => 'Continue', 'url' => '/next/' ) );
expect( str_starts_with( $plain, '<a ' ) && ! str_contains( $plain, '<div' ) && ! str_contains( $plain, '<span' ) && ! str_contains( $plain, 'data-rdct-button' ), 'Plain button adds unnecessary DOM or JS.' );
expect( array( 'button', 'button-solid' ) === Button\assets( array() ) && ! Components\needs_script( 'button' ), 'Default button loads unrelated styles or runtime.' );
$motion = array( 'label' => 'More', 'secondary_label' => 'Details', 'text_effect' => 'change-y', 'style' => 'underline', 'icon' => 'arrow-right', 'icon_effect' => 'swap', 'icon_ripple' => true, 'magnetic' => true, 'new_tab' => true, 'nofollow' => true, 'url' => '/next/' );
$html = Button\render( $motion );
expect( str_contains( $html, 'aria-hidden="true">Details' ) && str_contains( $html, 'noopener noreferrer nofollow' ) && str_contains( $html, 'rdct-button__icon-copy' ), 'Text/icon effects or accessible link attributes failed.' );
expect( in_array( 'button-switch', Button\assets( $motion ), true ) && ! in_array( 'button-liquid', Button\assets( $motion ), true ) && Components\needs_script( 'button', $motion ), 'Button feature isolation failed.' );
expect( ! in_array( 'button-text', Button\assets( $motion ), true ), 'Text layout should be supplied once by the button base.' );
$icon_only = array( 'icon' => 'arrow-right', 'icon_effect' => 'swap' );
expect( str_contains( Button\render( $icon_only ), 'rdct-button__text' ) && ! Components\needs_script( 'button', $icon_only ), 'Icon swap lost its text wrapper or added unnecessary runtime.' );
$disabled = Button\render( array( 'label' => 'Disabled', 'disabled' => true, 'url' => '/next/', 'magnetic' => true ) );
expect( str_contains( $disabled, 'aria-disabled="true"' ) && ! str_contains( $disabled, ' href=' ) && ! str_contains( $disabled, 'data-rdct-button' ), 'Disabled link remains actionable or starts motion.' );
expect( str_contains( Button\render( array( 'tag' => 'button' ) ), 'type="button"' ), 'Action button could submit a surrounding form.' );
$unsafe = Button\render( array( 'tag' => 'script', 'label' => '<script>Bad</script>', 'url' => 'javascript:alert(1)', 'background' => 'red;display:none', 'gap' => 10000 ) );
expect( ! str_contains( $unsafe, '<script' ) && ! str_contains( $unsafe, 'javascript:' ) && ! str_contains( $unsafe, 'display:none' ) && str_contains( $unsafe, '--rdct-button-gap:100px' ), 'Invalid markup/style escaped validation.' );
$responsive = Button\render( array( 'label' => 'Responsive', 'gap_responsive' => array( 'desktop' => 8, 'laptop' => 10, 'tablet' => 12, 'mobile' => 14 ) ) );
expect( str_contains( $responsive, '@media(max-width:1439px)' ) && str_contains( $responsive, '--rdct-button-gap:14px' ), 'Four-device button spacing was lost.' );
$sizes = Button\render( array( 'size' => 'custom', 'icon' => 'phone', 'font_size' => 18, 'icon_size' => 20, 'font_size_responsive' => array( 'desktop' => 30, 'mobile' => 14 ), 'icon_size_responsive' => array( 'desktop' => 10, 'mobile' => 24 ), 'gap_responsive' => array( 'desktop' => 20 ) ) );
foreach ( array( 'font-size', 'icon-size', 'gap' ) as $property ) { expect( ! preg_match( '/style="[^"]*--rdct-button-' . $property . ':/', $sizes ), 'Inline button defaults still override responsive ' . $property ); }
expect( str_contains( $sizes, '--rdct-button-font-size:30px;' ) && str_contains( $sizes, '--rdct-button-icon-size:10px;' ) && str_contains( $sizes, '--rdct-button-icon-size:24px;' ), 'Selected text/icon size did not reach bounded responsive styles.' );
$automatic_icon = Button\render( array( 'icon' => 'phone', 'icon_box_size_responsive' => array( 'desktop' => 0, 'mobile' => 40 ) ) );
expect( str_contains( $automatic_icon, '--rdct-button-icon-box-size:auto;' ) && str_contains( $automatic_icon, '--rdct-button-icon-box-size:40px;' ) && ! str_contains( $automatic_icon, '--rdct-button-icon-box-size:0px;' ), 'Zero icon box must remain automatic rather than collapse.' );
$theme_css = file_get_contents( __DIR__ . '/../radmanct/src/css/theme.css' );
expect( str_contains( $theme_css, ':where(.rdct-site) a {' ) && ! str_contains( $theme_css, '.rdct-site a {' ), 'Generic theme link colors must not outrank component link/button colors.' );
foreach ( Button\fields()['text_effect']['options'] as $effect => $label ) {
	$options = array( 'text_effect' => $effect );
	$requirements = Components\requirements( 'button', $options );
	foreach ( $requirements['styles'] as $style ) { expect( isset( Components\definitions()[ $style ] ), 'Missing button style definition.' ); }
	expect( str_starts_with( $effect, 'liquid-' ) === $requirements['script'], 'CSS-only effect eagerly starts JavaScript.' );
}
expect( '' === Button\render( array( 'label' => '' ) ), 'Empty button renders an inaccessible control.' );
$typography = Button\render( array( 'font_weight' => '700', 'font_style' => 'italic', 'text_transform' => 'uppercase', 'text_decoration' => 'underline' ) );
foreach ( array( 'font-weight:700;', 'font-style:italic;', 'text-transform:uppercase;', 'text-decoration:underline;' ) as $declaration ) { expect( str_contains( $typography, $declaration ), 'Shared button typography lost ' . $declaration ); }
expect( str_contains( Button\render( array( 'button_typography_font_weight' => '600' ) ), 'font-weight:600;' ), 'Existing Classic typography was lost.' );
expect( '700' === Button\settings( array( 'font_weight' => '700', 'button_typography_font_weight' => '600' ) )['font_weight'], 'Explicit shared typography lost precedence.' );
expect( ! str_contains( Button\render( array( 'font_weight' => '700;display:none', 'font_style' => 'invalid' ) ), 'display:none' ), 'Typography bypassed the shared allowlist.' );
$native_spacing = Button\render( array( 'button_typography_line_height' => array( 'size' => 1.6, 'unit' => 'em' ), 'button_typography_letter_spacing_mobile' => array( 'size' => -.5, 'unit' => 'px' ), 'button_typography_word_spacing_laptop' => array( 'size' => .2, 'unit' => 'em' ) ) );
foreach ( array( 'line-height:1.6em;', 'letter-spacing:-0.5px;', 'word-spacing:0.2em;' ) as $declaration ) { expect( str_contains( $native_spacing, $declaration ), 'Native typography did not reach shared button rendering: ' . $declaration ); }
$shared_spacing = Button\render( array( 'line_height_responsive' => array( 'desktop' => 1.6 ), 'line_height_unit' => 'em', 'letter_spacing_responsive' => array( 'mobile' => -.5 ), 'word_spacing_responsive' => array( 'laptop' => .2 ), 'word_spacing_unit' => 'em' ) );
expect( str_contains( $shared_spacing, 'line-height:1.6em;' ) && str_contains( $shared_spacing, 'letter-spacing:-0.5px;' ) && str_contains( $shared_spacing, 'word-spacing:0.2em;' ), 'PHP/Atomic typography controls lost units, negative spacing or laptop values.' );
expect( ! str_contains( Button\render( array( 'line_height' => -1000, 'word_spacing' => -1000 ) ), '-1000' ), 'Unset typography reached CSS.' );
$unit_button = Button\render( array( 'size' => 'custom', 'padding_x_responsive' => array( 'size' => 1.5, 'unit' => 'rem' ), 'font_size_responsive_mobile' => array( 'size' => 1.1, 'unit' => 'em' ), 'icon_box_size_responsive' => array( 'size' => 0, 'unit' => 'rem' ) ) );
expect( str_contains( $unit_button, '--rdct-button-padding-x:1.5rem;' ) && str_contains( $unit_button, '--rdct-button-font-size:1.1em;' ) && str_contains( $unit_button, '--rdct-button-icon-box-size:auto;' ), 'Native dimension units or zero automatic icon box were lost.' );
$preset_button = Button\render( array( 'size' => 'lg', 'padding_x' => 2, 'padding_x_unit' => 'em', 'font_size_responsive_mobile' => array( 'size' => 8, 'unit' => 'rem' ) ) );
expect( str_contains( $preset_button, '--rdct-button-padding-x:32px;' ) && ! str_contains( $preset_button, '--rdct-button-font-size:8rem;' ), 'Hidden custom values overrode a selected size preset.' );
foreach ( array( 'switch-x' => .85, 'switch-y' => .85, 'change-y' => .85, 'liquid-x' => .6, 'liquid-y-alt' => .6, 'marquee-x' => 2, 'marquee-y' => 1 ) as $effect => $duration ) {
	expect( (float) $duration === (float) Button\settings( array( 'text_effect' => $effect ) )['duration'], 'Effect-specific default timing was lost: ' . $effect );
	expect( (float) $duration === (float) Button\settings( array( 'text_effect' => $effect, 'duration' => '' ) )['duration'], 'Empty native duration did not inherit effect timing.' );
	expect( .4 === Button\settings( array( 'text_effect' => $effect, 'duration' => .4 ) )['duration'], 'Explicit legacy duration was replaced.' );
}
echo "Passed: button semantics, typography parity, safe links/styles, timing profiles, four-device controls and selected asset isolation.\n";
