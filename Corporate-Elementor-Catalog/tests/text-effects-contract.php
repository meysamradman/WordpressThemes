<?php

require __DIR__ . '/components-contract.php';
function esc_attr__( $value, $domain ) { return esc_attr( $value ); }
function esc_url( $value ) { return preg_match( '~^(https?://|/)~', $value ) ? esc_attr( $value ) : ''; }

use RadmanCTComponents\Components\TextReveal;

$alpha_settings = TextReveal\settings( array( 'idle' => 'rgba(12,34,56,0)', 'active' => '#1234', 'highlight_color' => '#abcdef80', 'gradient_end' => 'transparent', 'outline_color' => 'red;display:none' ) );
$header_text = TextReveal\render( 'Header', array( 'mode' => 'static', 'header_colors' => true, 'header_sticky_color' => '#1234', 'header_dark_color' => 'url(bad)', 'font_weight' => '700', 'font_size' => array( 'size' => 2, 'unit' => 'rem' ) ) );
expect( str_contains( $header_text, 'data-rdct-text-header' ) && str_contains( $header_text, '--rdct-text-header-sticky:#1234;' ) && ! str_contains( $header_text, 'url(bad)' ) && str_contains( $header_text, 'font-weight:700;' ) && str_contains( $header_text, 'font-size:2rem;' ), 'Header/typography options lost safe SSR or units.' );
expect( in_array( 'text-header-state', TextReveal\assets( array( 'header_colors' => true ) ), true ) && ! in_array( 'text-header-state', TextReveal\assets( array() ), true ), 'Header text CSS leaked into normal text.' );
expect( 'rgba(12,34,56,0)' === $alpha_settings['idle'] && '#1234' === $alpha_settings['active'] && '#abcdef80' === $alpha_settings['highlight_color'] && '#00000000' === $alpha_settings['gradient_end'] && '#172b3a' === $alpha_settings['outline_color'], 'Shared Text colors lost alpha or accepted CSS injection.' );
foreach ( array( 'scroll', 'static', 'hover', 'fill' ) as $mode ) {
	$alpha_html = TextReveal\render( 'Readable', array( 'mode' => $mode, 'idle' => 'rgba(12,34,56,0)', 'active' => '#1234' ) );
	expect( str_contains( $alpha_html, '--rdct-text-idle:rgba(12,34,56,0)' ) && str_contains( $alpha_html, '--rdct-text-active:#1234' ), 'Plain or styled Text rendering discarded native alpha.' );
}
$alpha_words = TextReveal\render( '', array( 'mode' => 'rotate', 'items' => array( array( 'title' => 'Alpha', 'color' => 'rgba(12,34,56,.25)' ), array( 'title' => 'Invalid', 'color' => 'red;display:none' ) ) ) );
expect( str_contains( $alpha_words, '--rdct-word-color:rgba(12,34,56,.25)' ) && ! str_contains( $alpha_words, 'display:none' ), 'Rotating word colors discarded alpha or accepted CSS injection.' );

$html = TextReveal\render( '', array( 'mode' => 'rotate', 'items' => array( array( 'title' => '<script>bad</script>', 'color' => 'red;display:none' ), array( 'title' => 'English' ) ), 'word_delay' => -100, 'word_duration' => 999 ) );
expect( str_contains( $html, 'data-rdct-text-reveal="rotate"' ) && str_contains( $html, '&lt;script&gt;' ) && ! str_contains( $html, '<script>' ) && ! str_contains( $html, 'display:none' ), 'Words-only content or escaping failed.' );
expect( str_contains( $html, 'data-word-delay="1"' ) && str_contains( $html, '--rdct-text-word-duration:3s' ) && str_contains( $html, 'data-word-play hidden' ), 'Bounded timing or accessible motion control missing.' );
$bounded = TextReveal\render( 'Words ', array( 'mode' => 'rotate', 'items' => array_fill( 0, 40, array( 'title' => 'One' ) ) ) );
expect( 30 === substr_count( $bounded, 'class="rdct-text__word"' ), 'Word list is unbounded.' );
$static = TextReveal\render( 'Our [[work]]', array( 'mode' => 'static', 'highlight' => 'underline', 'font_family' => 'evil;display:none', 'direction' => 'rtl', 'url' => array( 'url' => 'https://example.test/', 'is_external' => true, 'nofollow' => true ) ) );
expect( str_contains( $static, '<mark class="rdct-text__highlight">work</mark>' ) && str_contains( $static, 'dir="rtl"' ) && str_contains( $static, 'noopener noreferrer nofollow' ) && ! str_contains( $static, 'evil' ), 'Highlight, native URL or local font boundary failed.' );
expect( ! TextReveal\uses_effects( array( 'mode' => 'scroll', 'highlight' => 'none', 'outline' => 'none' ) ) && TextReveal\uses_effects( array( 'mode' => 'rotate' ) ), 'Optional effects stylesheet is unconditional.' );
$geometry = TextReveal\render( 'Our [[work]]', array( 'mode' => 'static', 'highlight' => 'circle', 'highlight_width_responsive' => array( 'desktop' => 120, 'mobile' => 90 ), 'highlight_height_responsive' => array( 'desktop' => 0 ), 'highlight_inline_offset_responsive' => array( 'tablet' => -12 ), 'highlight_radius_responsive' => array( 'mobile' => '0;display:none' ), 'highlight_duration' => 100, 'highlight_delay' => -4 ) );
expect( str_contains( $geometry, '--rdct-text-highlight-width:120%;' ) && ! str_contains( $geometry, '120%%' ) && str_contains( $geometry, '--rdct-text-highlight-height:0px;' ), 'Highlight units or explicit zero failed.' );
expect( str_contains( $geometry, '--rdct-text-highlight-inline-offset:-12px;' ) && str_contains( $geometry, '--rdct-text-highlight-width:90%;' ) && ! str_contains( $geometry, 'display:none' ), 'Responsive logical offset or CSS input boundary failed.' );
expect( str_contains( $geometry, '--rdct-text-highlight-duration:10s;' ) && str_contains( $geometry, '--rdct-text-highlight-delay:0s;' ), 'Highlight timing bounds failed.' );
$units = TextReveal\render( 'Our [[work]]', array( 'mode' => 'static', 'highlight' => 'brush', 'highlight_height_responsive' => array( 'size' => .4, 'unit' => 'px' ), 'highlight_height_unit_responsive' => 'em', 'highlight_height_unit_responsive_mobile' => 'rem', 'highlight_inline_offset_responsive' => array( 'desktop' => -1.25, 'mobile' => 0 ), 'highlight_inline_offset_unit_responsive' => array( 'desktop' => 'em' ), 'highlight_width_responsive' => array( 'desktop' => 110 ), 'highlight_width_unit_responsive' => array( 'mobile' => 'vw' ) ) );
expect( str_contains( $units, '--rdct-text-highlight-height:0.4em;' ) && str_contains( $units, '--rdct-text-highlight-height:0.4rem;' ) && str_contains( $units, '--rdct-text-highlight-inline-offset:-1.25em;' ) && str_contains( $units, '--rdct-text-highlight-inline-offset:0em;' ) && str_contains( $units, '--rdct-text-highlight-width:110vw;' ), 'Highlight Classic/Atomic units, negative offsets or unit-only inheritance failed.' );
$invalid_units = TextReveal\render( 'Our [[work]]', array( 'mode' => 'static', 'highlight' => 'brush', 'highlight_height_responsive' => array( 'desktop' => 8 ), 'highlight_height_unit_responsive' => array( 'desktop' => 'px;display:none' ) ) );
expect( str_contains( $invalid_units, '--rdct-text-highlight-height:8px;' ) && ! str_contains( $invalid_units, 'display:none' ), 'Invalid highlight unit escaped the allowlist.' );
echo "Passed: words-only SSR, escaped/bounded words, native URL, local font boundary and conditional effects CSS.\n";
