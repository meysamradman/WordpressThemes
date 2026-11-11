<?php

namespace RadmanCT\Layout\Page;

defined( 'ABSPATH' ) || exit;

const META_KEY = '_rdct_page_layout';

function normalize( $input, $previous = array() ): array {
	$result = array( 'layout' => 'inherit' );
	$keys = array_merge( array( 'layout' ), array_keys( \RadmanCT\Layout\fields() ) );
	foreach ( array( $previous, $input ) as $candidate ) {
		if ( ! is_array( $candidate ) ) { continue; }
		foreach ( $keys as $key ) {
			if ( ! array_key_exists( $key, $candidate ) ) { continue; }
			$valid = false;
			$value = \RadmanCT\Layout\normalize_field( $key, $candidate[ $key ], $valid );
			if ( ! $valid ) { continue; }
			if ( null === $value ) { unset( $result[ $key ] ); } else { $result[ $key ] = $value; }
		}
	}
	return array( 'layout' => 'inherit' ) === $result ? array() : $result;
}

function resolve( array $values, $overrides ): array {
	$overrides = normalize( $overrides );
	return 'inherit' !== ( $overrides['layout'] ?? 'inherit' ) ? array_replace( $values, $overrides ) : $values;
}
