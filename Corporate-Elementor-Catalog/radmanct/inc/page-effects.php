<?php

namespace RadmanCT\PageEffects;

defined( 'ABSPATH' ) || exit;

const KEYS = array( 'scroll_reveal', 'smooth_scroll', 'custom_cursor', 'scroll_progress', 'preloader' );

function normalize( $value ): array {
	$result = array();
	if ( is_array( $value ) ) {
		foreach ( KEYS as $key ) {
			if ( in_array( $value[ $key ] ?? null, array( 'on', 'off' ), true ) ) {
				$result[ $key ] = $value[ $key ];
			}
		}
	}
	return $result;
}

function resolve( array $settings, $overrides ): array {
	foreach ( normalize( $overrides ) as $key => $value ) {
		$settings[ $key ] = 'on' === $value;
	}
	return $settings;
}

function values(): array {
	$settings = \RadmanCT\Settings\values();
	if ( is_singular( 'page' ) ) {
		$settings = resolve( $settings, get_post_meta( get_queried_object_id(), '_radman_page_effects', true ) );
	}
	return $settings;
}
