<?php
namespace RadmanCTComponents\Background;
defined( 'ABSPATH' ) || exit;

function normalize( $input, array $types = array( 'color', 'gradient', 'image', 'video' ) ): ?array {
	if ( ! is_array( $input ) ) { return null; }
	$result = array( 'type' => 'color', 'color' => '', 'second' => '#233640', 'gradient' => 'linear', 'angle' => 135, 'start' => 0, 'end' => 100, 'image' => 0, 'video' => 0 );
	foreach ( $input as $key => $value ) {
		if ( ! array_key_exists( $key, $result ) ) { return null; }
		if ( 'type' === $key ) { if ( ! in_array( $value, $types, true ) ) { return null; } }
		elseif ( in_array( $key, array( 'color', 'second' ), true ) ) {
			if ( ! is_string( $value ) || ( '' !== $value && ! preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) ) { return null; }
			$value = strtolower( $value );
		} elseif ( 'gradient' === $key ) { if ( ! in_array( $value, array( 'linear', 'radial' ), true ) ) { return null; } }
		elseif ( ! is_int( $value ) || $value < 0 || $value > ( in_array( $key, array( 'image', 'video' ), true ) ? PHP_INT_MAX : ( 'angle' === $key ? 360 : 100 ) ) ) { return null; }
		$result[ $key ] = $value;
	}
	if ( $result['start'] > $result['end'] ) { return null; }
	return $result;
}

function paint( array $input, string $fallback = 'var(--rdct-paper,#fff)' ): string {
	$s = normalize( $input );
	if ( null === $s ) { return $fallback; }
	$color = $s['color'] ?: $fallback;
	if ( 'gradient' !== $s['type'] ) { return $color; }
	return ( 'radial' === $s['gradient'] ? 'radial-gradient(circle,' : 'linear-gradient(' . $s['angle'] . 'deg,' ) . $color . ' ' . $s['start'] . '%,' . ( $s['second'] ?: $color ) . ' ' . $s['end'] . '%)';
}
