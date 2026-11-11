<?php

namespace RadmanCTComponents\Components\TextReveal;

defined( 'ABSPATH' ) || exit;

function segment_shadow_fields(): array {
	return array(
		'shadow_color' => array( 'label' => __( 'Shadow color', 'radmanct-components' ), 'default' => '#0000004d', 'color' => true ),
		'shadow_horizontal' => array( 'label' => __( 'Horizontal shadow', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'shadow_vertical' => array( 'label' => __( 'Vertical shadow', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'shadow_blur' => array( 'label' => __( 'Shadow blur', 'radmanct-components' ), 'default' => 10, 'min' => 0, 'max' => 100 ),
	);
}

function segment_shadow_metadata(): array {
	$result = array();
	$mode = array( 'label' => __( 'Text shadow', 'radmanct-components' ), 'default' => 'inherit', 'options' => array( 'inherit' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'custom' => __( 'Custom', 'radmanct-components' ) ) );
	foreach ( array( '' => '', '_tablet' => __( 'Tablet', 'radmanct-components' ), '_mobile' => __( 'Mobile', 'radmanct-components' ) ) as $suffix => $device ) {
		$result[ 'shadow_mode' . $suffix ] = $mode;
		if ( $device ) { $result[ 'shadow_mode' . $suffix ]['label'] = sprintf( __( '%1$s (%2$s)', 'radmanct-components' ), $mode['label'], $device ); }
		$result[ 'shadow_layers' . $suffix ] = array( 'label' => __( 'Shadow layers', 'radmanct-components' ), 'default' => '1', 'options' => array( '1' => '1', '2' => '2', '3' => '3' ), 'condition' => array( 'shadow_mode' . $suffix => 'custom' ) );
		for ( $layer = 1; $layer <= 3; ++$layer ) {
			$prefix = 1 === $layer ? 'shadow_' : 'shadow_' . $layer . '_';
			$fields = segment_shadow_fields();
			$fields['shadow_unit'] = array( 'label' => __( 'Text shadow unit', 'radmanct-components' ), 'default' => 'px', 'options' => array( 'px' => 'px', 'em' => 'em', 'rem' => 'rem' ) );
			foreach ( $fields as $key => $field ) {
				$part = substr( $key, 7 ); $name = $prefix . $part . $suffix;
				$field['condition'] = array( 'shadow_mode' . $suffix => 'custom' );
				if ( $layer > 1 ) { $field['condition'][ 'shadow_layers' . $suffix ] = 2 === $layer ? array( '2', '3' ) : '3'; $field['label'] = sprintf( __( '%1$s — layer %2$d', 'radmanct-components' ), $field['label'], $layer ); }
				if ( $device ) { $field['label'] = sprintf( __( '%1$s (%2$s)', 'radmanct-components' ), $field['label'], $device ); }
				if ( isset( $field['min'] ) ) { $field['float'] = true; $field['step'] = .1; }
				if ( 1 === $layer && 'unit' !== $part ) { $field['classic_shadow'] = true; }
				$result[ $name ] = $field;
			}
		}
	}
	foreach ( $result as &$field ) { $field['group'] = 'shadow'; }
	unset( $field );
	return $result;
}

function segment_shadow_value( array $row, string $device = 'desktop' ): ?string {
	$suffix = 'desktop' === $device ? '' : '_' . $device;
	$mode = $row[ 'shadow_mode' . $suffix ] ?? 'inherit';
	if ( 'none' === $mode ) { return 'none'; }
	if ( 'custom' !== $mode ) { return null; }
	$merged = $row;
	if ( $suffix ) {
		foreach ( $row as $key => $value ) { if ( str_starts_with( $key, 'shadow' ) && str_ends_with( $key, $suffix ) ) { $merged[ substr( $key, 0, -strlen( $suffix ) ) ] = $value; } }
		if ( ! isset( $row[ 'shadow' . $suffix ] ) ) { unset( $merged['shadow'] ); }
	}
	$count = (int) number( $merged['shadow_layers'] ?? 1, 1, 1, 3 ); $layers = array();
	for ( $layer = 1; $layer <= $count; ++$layer ) {
		$prefix = 1 === $layer ? 'shadow_' : 'shadow_' . $layer . '_';
		$native = 1 === $layer && is_array( $merged['shadow'] ?? null ) ? $merged['shadow'] : array();
		$unit = is_string( $merged[ $prefix . 'unit' ] ?? null ) && in_array( $merged[ $prefix . 'unit' ], array( 'px', 'em', 'rem' ), true ) ? $merged[ $prefix . 'unit' ] : 'px';
		$parts = array();
		foreach ( array( 'horizontal', 'vertical', 'blur' ) as $part ) { $parts[] = number( $native[ $part ] ?? ( $merged[ $prefix . $part ] ?? null ), 'blur' === $part ? 10 : 0, 'blur' === $part ? 0 : -100, 100 ) . $unit; }
		$parts[] = segment_color( $native['color'] ?? ( $merged[ $prefix . 'color' ] ?? null ), '#0000004d' );
		$layers[] = implode( ' ', $parts );
	}
	return implode( ', ', $layers );
}

function segment_shadow( array $row ): string {
	$base = segment_shadow_value( $row );
	$style = null === $base ? '' : 'text-shadow:' . $base . ';';
	if ( null !== segment_shadow_value( $row, 'tablet' ) || null !== segment_shadow_value( $row, 'mobile' ) ) { $style .= 'text-shadow:var(--rdct-segment-shadow,' . ( $base ?? 'inherit' ) . ');'; }
	return $style;
}

