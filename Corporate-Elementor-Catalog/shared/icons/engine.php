<?php

namespace RadmanCTShared\Icons;

defined( 'ABSPATH' ) || exit;

function catalog( array $base ): array {
	static $loaded = array();
	foreach ( apply_filters( 'radmanct/icons/packs', array() ) as $pack => $loader ) {
		if ( ! is_string( $pack ) || ! preg_match( '/^[a-z][a-z0-9-]{0,29}$/D', $pack ) || in_array( $pack, array( 'general', 'social' ), true ) || ! is_callable( $loader ) ) { continue; }
		if ( ! array_key_exists( $pack, $loaded ) ) {
			$loaded[ $pack ] = array();
			$icons = $loader();
			if ( is_array( $icons ) ) { $loaded[ $pack ] = $icons; }
		}
		foreach ( $loaded[ $pack ] as $name => $icon ) {
			if ( ! is_string( $name ) || ! str_starts_with( $name, $pack . '-' ) || ! preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $name ) || ! is_array( $icon ) || array_diff( array( 'label', 'viewbox', 'fill', 'stroke', 'body', 'directional' ), array_keys( $icon ) ) ) { continue; }
			$base[ $name ] ??= array_replace( $icon, array( 'pack' => $pack ) );
		}
	}
	return apply_filters( 'radmanct/icons/catalog', $base );
}

function picker_data( array $catalog, array $labels ): array {
	$result = array();
	foreach ( $catalog as $name => $icon ) {
		if ( ! is_string( $name ) || ! preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $name ) ) { continue; }
		$result[ $name ] = array( 'label' => $labels[ $name ] ?? $icon['label'], 'pack' => $icon['pack'] ?? 'general', 'src' => 'data:image/svg+xml,' . rawurlencode( render( $icon ) ) );
	}
	return $result;
}

function render( array $icon, array $options = array() ): string {
	$size = $options['size'] ?? 24;
	$size = is_numeric( $size ) && is_finite( (float) $size ) ? max( 8, min( 128, (float) $size ) ) : 24;
	$stroke = $options['stroke'] ?? 1;
	$stroke = is_numeric( $stroke ) && is_finite( (float) $stroke ) ? max( 1, min( 3, (float) $stroke ) ) : 1;
	$label = is_string( $options['label'] ?? null ) ? sanitize_text_field( $options['label'] ) : '';
	$directional = $icon['directional'] && true === ( $options['mirror'] ?? true );
	$class = 'rdct-ico' . ( $directional ? ' rdct-ico--directional' : '' );
	$accessibility = '' === $label ? ' aria-hidden="true"' : ' role="img" aria-label="' . esc_attr( $label ) . '"';
	return '<svg xmlns="http://www.w3.org/2000/svg" class="' . esc_attr( $class ) . '" viewBox="' . esc_attr( $icon['viewbox'] ) . '" width="' . esc_attr( (string) $size ) . '" height="' . esc_attr( (string) $size ) . '" fill="' . esc_attr( $icon['fill'] ) . '" stroke="' . esc_attr( $icon['stroke'] ) . '" stroke-width="' . esc_attr( (string) $stroke ) . '" stroke-linecap="round" stroke-linejoin="round" focusable="false"' . $accessibility . '>' . $icon['body'] . '</svg>';
}
