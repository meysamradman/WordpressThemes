<?php

namespace RadmanCTComponents\Components\TextReveal;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/text-fields.php';
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/text-effects.php';
require_once __DIR__ . '/text-segments.php';

function number( $value, float $fallback, float $minimum, float $maximum ): float {
	return is_numeric( $value ) && is_finite( (float) $value ) ? max( $minimum, min( $maximum, (float) $value ) ) : $fallback;
}

function position( $value, string $fallback ): string {
	return is_string( $value ) && preg_match( '/^(?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%) (?:top|center|bottom|(?:100|[0-9]{1,2}(?:\.[0-9]{1,2})?)%)$/D', $value ) ? $value : $fallback;
}

function split_attributes( array $options, string $mode ): string {
	$lines = 'scroll' === $mode && 'lines' === ( $options['split_type'] ?? '' );
	$mask = $lines && in_array( $options['line_mask'] ?? false, array( true, 'yes' ), true );
	return $lines ? ' data-reveal-split="lines" data-reveal-mask="' . ( $mask ? 'true' : 'false' ) . '"' : '';
}

function render( string $text, array $options = array() ): string {
	if ( ( '' === trim( $text ) && ! ( ( 'rotate' === ( $options['mode'] ?? '' ) && ! empty( $options['items'] ) ) || ( 'rich' === ( $options['mode'] ?? '' ) && ! empty( $options['segments'] ) ) ) ) || strlen( $text ) > 16000 ) {
		return '';
	}
	$tag = in_array( $options['tag'] ?? '', array( 'p', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $options['tag'] : 'p';
	$direction = in_array( $options['direction'] ?? '', array( 'auto', 'rtl', 'ltr' ), true ) ? ' dir="' . esc_attr( $options['direction'] ) . '"' : '';
	if ( 'marquee' === ( $options['mode'] ?? '' ) ) {
		$inner = effects( $text, array_merge( $options, array( 'mode' => 'static', 'nowrap' => true ) ), $tag, $direction, 'static' );
		$inner = str_replace( 'data-rdct-text-reveal="static"', '', $inner );
		return \RadmanCTComponents\Components\Marquee\render( array( $inner ), $options, 'rdct-tr', '', true );
	}
	$mode = in_array( $options['mode'] ?? '', array( 'static', 'hover', 'fill', 'rotate', 'rich' ), true ) ? $options['mode'] : 'scroll';
	$words = preg_match_all( '/\S+/u', $text );
	if ( in_array( $mode, array( 'scroll', 'fill' ), true ) && ( false === $words || $words > 400 ) ) {
		$mode = 'static';
	}
	if ( uses_effects( $options ) || ! empty( $options['url'] ) || in_array( $mode, array( 'static', 'fill', 'rotate' ), true ) || ! empty( $options['highlight'] ) || ! empty( $options['outline'] ) || ! empty( $options['font_family'] ) || ! empty( $options['word_font_family'] ) ) { return effects( $text, $options, $tag, $direction, $mode ); }
	$opacity = number( $options['opacity'] ?? null, 0.25, 0.1, 1 );
	$attributes = array(
		'initial-opacity' => $opacity,
		'final-opacity' => number( $options['final_opacity'] ?? null, 1, 0.1, 1 ),
		'reveal-start' => position( $options['start'] ?? null, 'top 80%' ),
		'reveal-end' => position( $options['end'] ?? null, 'bottom 35%' ),
		'reveal-from' => in_array( $options['from'] ?? '', array( 'start', 'end', 'center', 'edges', 'random' ), true ) ? $options['from'] : 'start',
		'reveal-scrub' => number( $options['scrub'] ?? null, 0.5, 0, 5 ),
		'reveal-stagger' => number( $options['stagger'] ?? null, 0.12, 0, 1 ),
	);
	$data = split_attributes( $options, $mode );
	foreach ( $attributes as $key => $value ) {
		$data .= ' data-' . $key . '="' . esc_attr( (string) $value ) . '"';
	}
	$styles = array();
	if ( in_array( $options['align'] ?? '', array( 'start', 'end', 'center', 'justify' ), true ) ) {
		$styles[] = 'text-align:' . $options['align'];
	}
	foreach ( array( 'idle' => '#667085', 'active' => '#172b3a' ) as $key => $fallback ) {
		$value = $options[ $key ] ?? $fallback;
		$color = segment_color( $value, $fallback );
		$styles[] = '--rdct-text-' . $key . ':' . $color;
	}
	$focus = 'hover' === $mode ? ' tabindex="0"' : '';
	$html = '<' . $tag . ' class="rdct-tr" data-rdct-text-reveal="' . $mode . '"' . $data . $focus . $direction . ' style="' . esc_attr( implode( ';', $styles ) ) . '">' . esc_html( $text ) . '</' . $tag . '>';
	return \RadmanCTComponents\Components\Presentation\render( $html, $options, 'text-reveal' );
}
