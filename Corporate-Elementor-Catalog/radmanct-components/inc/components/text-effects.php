<?php

namespace RadmanCTComponents\Components\TextReveal;

defined( 'ABSPATH' ) || exit;

function settings( array $input ): array {
	if ( is_array( $input['url'] ?? null ) ) { $link = $input['url']; $input['url'] = $link['url'] ?? ''; $input['external'] = ! empty( $link['is_external'] ); $input['nofollow'] = ! empty( $link['nofollow'] ); }
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( isset( $field['min'] ) ) { $value = number( $value, $field['default'], $field['min'], $field['max'] ); }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = segment_color( $value, $field['default'] ); }
		else { $value = is_string( $value ) ? ( strlen( $value ) <= 16000 ? $value : $field['default'] ) : $field['default']; }
		$result[ $key ] = $value;
	}
	return $result;
}

function highlighted( string $text ): string {
	$parts = preg_split( '/(\[\[.*?\]\])/us', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	return implode( '', array_map( static fn( $part ) => str_starts_with( $part, '[[' ) && str_ends_with( $part, ']]' ) ? '<mark class="rdct-text__highlight">' . esc_html( substr( $part, 2, -2 ) ) . '</mark>' : esc_html( $part ), $parts ?: array( $text ) ) );
}

function effects( string $text, array $input, string $tag, string $direction, string $mode ): string {
	$s = settings( $input );
	$style = '';
	$style .= segment_typography( $s );
	if ( '' !== $s['font_weight'] ) { $style .= 'font-weight:' . $s['font_weight'] . ';'; }
	foreach ( array( 'idle', 'active', 'highlight_color', 'word_color', 'word_active_end', 'outline_color', 'gradient_end' ) as $key ) { $style .= '--rdct-text-' . str_replace( '_', '-', $key ) . ':' . $s[ $key ] . ';'; }
	$style .= 'writing-mode:' . $s['writing_mode'] . ';--rdct-text-gradient-angle:' . $s['gradient_angle'] . 'deg;';
	$style .= '--rdct-text-stroke:' . $s['outline_width'] . 'px;--rdct-text-word-duration:' . $s['word_duration'] . 's;--rdct-text-visible:' . (int) $s['visible_words'] . ';';
	$style .= '--rdct-text-highlight-duration:' . $s['highlight_duration'] . 's;--rdct-text-highlight-delay:' . $s['highlight_delay'] . 's;';
	foreach ( array( 'font_family' => 'font', 'word_font_family' => 'word-font' ) as $key => $variable ) { if ( isset( fonts()[ $s[ $key ] ] ) ) { $style .= '--rdct-text-' . $variable . ':"' . fonts()[ $s[ $key ] ] . '",sans-serif;'; } }
	if ( in_array( $input['align'] ?? '', array( 'start', 'end', 'center', 'justify' ), true ) ) { $style .= 'text-align:' . $input['align'] . ';'; }
	$body = 'rich' === $mode ? segments( $input['segments'] ?? array() ) : '';
	if ( '' === $body ) { $body = highlighted( $text ); }
	$play = '';
	$data = '';
	if ( 'rotate' === $mode ) {
		$words = array();
		foreach ( array_slice( is_array( $input['items'] ?? null ) ? $input['items'] : array(), 0, 30 ) as $row ) {
			$word = is_array( $row ) && is_string( $row['title'] ?? null ) ? ( strlen( $row['title'] ) <= 960 ? trim( $row['title'] ) : '' ) : '';
			if ( '' === $word ) { continue; }
			$color = segment_color( $row['color'] ?? null );
			$color = '' !== $color ? ' style="--rdct-word-color:' . esc_attr( $color ) . '"' : '';
			$words[] = '<span class="rdct-text__word"' . $color . '>' . esc_html( $word ) . '</span>';
		}
		if ( $words ) {
			$body .= '<span class="rdct-text__rotator" data-word-style="' . $s['rotate_style'] . '"><span class="rdct-text__words">' . implode( ' ', $words ) . '</span></span>';
			$body .= highlighted( $s['suffix'] );
			$play = '<button type="button" class="rdct-text__play" data-word-play hidden aria-pressed="true" aria-label="' . esc_attr__( 'Play or pause rotating words', 'radmanct-components' ) . '">&#10074;&#10074;</button>';
			$data = ' data-word-delay="' . $s['word_delay'] . '" data-word-duration="' . $s['word_duration'] . '"';
		} else { $mode = 'static'; }
	}
	if ( in_array( $mode, array( 'scroll', 'fill' ), true ) ) {
		$data .= split_attributes( $s, $mode );
		$data .= ' data-initial-opacity="' . $s['opacity'] . '" data-final-opacity="' . $s['final_opacity'] . '" data-reveal-start="' . esc_attr( position( $s['start'], 'top 80%' ) ) . '" data-reveal-end="' . esc_attr( position( $s['end'], 'bottom 35%' ) ) . '" data-reveal-scrub="' . $s['scrub'] . '" data-reveal-stagger="' . $s['stagger'] . '" data-reveal-from="' . $s['from'] . '"';
		$body = esc_html( str_replace( array( '[[', ']]' ), '', $text ) );
	}
	$focus = 'hover' === $mode || 'hover' === $s['outline'] || $s['highlight_hover'] ? ' tabindex="0"' : '';
	$link = esc_url( $s['url'] );
	if ( '' !== $link ) { $body = '<a href="' . $link . '"' . ( $s['external'] ? ' target="_blank"' : '' ) . ' rel="' . ( $s['external'] ? 'noopener noreferrer ' : '' ) . ( $s['nofollow'] ? 'nofollow' : '' ) . '">' . $body . '</a>'; }
	$body .= $play;
	$data .= ' data-gradient="' . ( $s['gradient'] ? 'true' : 'false' ) . '"';
	if ( $s['header_colors'] ) {
		$data .= ' data-rdct-text-header';
		foreach ( array( 'sticky', 'light', 'dark' ) as $state ) { if ( '' !== $s[ 'header_' . $state . '_color' ] ) { $style .= '--rdct-text-header-' . $state . ':' . $s[ 'header_' . $state . '_color' ] . ';'; } }
	}
	$html = '<' . $tag . ' class="rdct-tr rdct-text" data-rdct-text-reveal="' . $mode . '" data-highlight="' . $s['highlight'] . '" data-highlight-animation="' . $s['highlight_animation'] . '" data-highlight-hover="' . ( $s['highlight_hover'] ? 'true' : 'false' ) . '" data-outline="' . $s['outline'] . '" data-nowrap="' . ( $s['nowrap'] ? 'true' : 'false' ) . '"' . $direction . $focus . $data . ' style="' . esc_attr( $style ) . '">' . $body . '</' . $tag . '>';
	return \RadmanCTComponents\Components\Presentation\render( $html, $input, 'text-reveal' );
}
