<?php

namespace RadmanCT\Integrations\Elementor\CustomCSS;

defined( 'ABSPATH' ) || exit;

function compile( $value, string $id ): array {
	if ( is_array( $value ) ) { $value = 'string' === ( $value['$$type'] ?? '' ) && empty( $value['disabled'] ) ? ( $value['value'] ?? '' ) : ''; }
	if ( ! is_string( $value ) || strlen( $value ) > 16384 || ! preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', $id ) || preg_match( '~\x00|</\s*(?:style|script)\b~i', $value ) ) { return array(); }
	$css = trim( $value, " \t\r\n\v\f" );
	if ( '' === $css ) { return array(); }
	$output = ''; $stack = array(); $length = strlen( $css );
	for ( $i = 0; $i < $length; ++$i ) {
		$char = $css[ $i ];
		if ( '/' === $char && '*' === ( $css[ $i + 1 ] ?? '' ) ) {
			$end = strpos( $css, '*/', $i + 2 );
			if ( false === $end ) { return array(); }
			$output .= ' '; $i = $end + 1; continue;
		}
		if ( '"' === $char || "'" === $char ) {
			$quote = $char; $start = $i;
			while ( ++$i < $length ) {
				if ( '\\' === $css[ $i ] ) { ++$i; continue; }
				if ( $quote === $css[ $i ] ) { break; }
			}
			if ( $i >= $length ) { return array(); }
			$output .= substr( $css, $start, $i - $start + 1 ); continue;
		}
		if ( '\\' === $char ) { return array(); }
		if ( preg_match( '/\G(?:(?:-webkit-)?image-set|src)\s*\(/i', $css, $match, 0, $i ) ) { return array(); }
		if ( '@' === $char && ! preg_match( '/\G@(media|supports|container|starting-style)\b/i', $css, $match, 0, $i ) ) { return array(); }
		if ( str_contains( '{([', $char ) ) { $stack[] = $char; }
		if ( str_contains( '})]', $char ) && array_pop( $stack ) !== array( '}' => '{', ')' => '(', ']' => '[' )[ $char ] ) { return array(); }
		if ( 's' === $char && ! in_array( '[', $stack, true ) && ! preg_match( '/[a-zA-Z0-9_.#-]/', $css[ $i - 1 ] ?? '' ) && preg_match( '/\Gselector(?![a-zA-Z0-9_-]|\s*\()/', $css, $match, 0, $i ) && preg_match( '/^[^{};]*\{/', substr( $css, $i + 8 ) ) ) { $output .= '&'; $i += 7; continue; }
		$output .= $char;
	}
	if ( $stack ) { return array(); }
	if ( preg_match_all( '~url\(\s*([\'"]?)(.*?)\1\s*\)~is', $output, $urls, PREG_SET_ORDER ) ) {
		foreach ( $urls as $url ) {
			if ( ! preg_match( '~^(?:/(?!/)|\.{1,2}/|[a-zA-Z0-9_-])[a-zA-Z0-9_./?%=&#+\x7e-]*$~D', trim( $url[2] ) ) ) { return array(); }
		}
	}
	$class = 'rdct-css-' . $id . '-' . hash( 'fnv1a32', $css );
	return array( 'class' => $class, 'css' => '.' . $class . '{' . $output . "\n}" );
}
