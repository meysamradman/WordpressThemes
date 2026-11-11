<?php

namespace RadmanCTComponents\Components\Values;

defined( 'ABSPATH' ) || exit;

function fields( array $input, array $schema ): array {
	$result = array();
	foreach ( $schema as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( ! empty( $field['media'] ) || isset( $field['min'] ) ) {
			$value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'] ?? 0, min( $field['max'] ?? PHP_INT_MAX, (float) $value ) ) : $field['default'];
			if ( is_int( $field['default'] ) ) { $value = (int) $value; }
		} elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ? $value : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 'yes', 'true' ), true ); }
		elseif ( ! empty( $field['multiline'] ) ) { $value = is_string( $value ) ? wp_kses_post( substr( $value, 0, 16000 ) ) : $field['default']; }
		else { $value = is_string( $value ) ? sanitize_text_field( substr( $value, 0, 500 ) ) : $field['default']; }
		$result[ $key ] = $value;
	}
	return $result;
}
