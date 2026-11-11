<?php

namespace RadmanCT\Builder\Responsive;

defined( 'ABSPATH' ) || exit;

function defaults( bool $row ): array {
	$result = array( 'hiddenDevices' => array() );
	foreach ( array( 'laptop', 'tablet', 'mobile' ) as $device ) {
		$result[ $device . 'Align' ] = 'inherit';
		if ( $row ) { $result[ $device . 'Justify' ] = 'inherit'; $result[ $device . 'Direction' ] = 'inherit'; }
	}
	foreach ( array( 'laptop', 'tablet' ) as $device ) {
		foreach ( $row ? array( 'Gap', 'Padding', 'Columns' ) : array( 'FontSize', 'Width' ) as $field ) {
			$result[ $device . $field ] = $row ? null : ( 'Width' === $field ? 'inherit' : 0 );
		}
		if ( $row ) { $result[ $device . 'Layout' ] = 'inherit'; }
	}
	if ( ! $row ) { $result['mobileWidth'] = 'inherit'; foreach ( array( 'laptop', 'tablet', 'mobile' ) as $device ) { $result[ $device . 'CustomWidth' ] = null; } }
	return $result;
}

function valid( string $key, $value, bool $row ): bool {
	if ( 'hiddenDevices' === $key ) {
		if ( ! is_array( $value ) || array_values( $value ) !== $value || count( $value ) > 4 ) { return false; }
		foreach ( $value as $device ) { if ( ! is_string( $device ) || ! in_array( $device, array( 'desktop', 'laptop', 'tablet', 'mobile' ), true ) ) { return false; } }
		return count( array_unique( $value ) ) === count( $value );
	}
	if ( str_ends_with( $key, 'Direction' ) ) { return $row && in_array( $value, array( 'inherit', 'row', 'column' ), true ); }
	if ( str_ends_with( $key, 'Align' ) ) { return in_array( $value, $row ? array( 'inherit', 'start', 'center', 'end', 'stretch' ) : array( 'inherit', 'start', 'center', 'end' ), true ); }
	if ( str_ends_with( $key, 'Justify' ) ) { return in_array( $value, array( 'inherit', 'start', 'center', 'end', 'space-between' ), true ); }
	if ( str_ends_with( $key, 'Layout' ) ) { return in_array( $value, array( 'inherit', 'stack', 'inline' ), true ); }
	if ( ! $row ) {
		if ( str_ends_with( $key, 'CustomWidth' ) ) { return null === $value || ( is_int( $value ) && $value >= 5 && $value <= 100 ); }
		return str_ends_with( $key, 'Width' ) ? in_array( $value, array( 'inherit', 'equal', 'auto', 'full', 'custom' ), true ) : ( is_int( $value ) && $value >= 0 && $value <= 96 );
	}
	if ( null === $value ) { return true; }
	$columns = str_ends_with( $key, 'Columns' );
	return is_int( $value ) && $value >= ( $columns ? 1 : 0 ) && $value <= ( $columns ? 6 : ( str_ends_with( $key, 'Gap' ) ? 64 : 120 ) );
}

function attributes( array $appearance, bool $row ): array {
	$class = ''; $style = '';
	foreach ( $appearance['hiddenDevices'] ?? array() as $device ) { $class .= ' rdct-bld--hide-' . $device; }
	foreach ( array( 'laptop', 'tablet', 'mobile' ) as $device ) {
		foreach ( $row ? array( 'Align' => 'align', 'Justify' => 'justify' ) : array( 'Align' => 'align' ) as $field => $name ) {
			$value = $appearance[ $device . $field ] ?? 'inherit';
			if ( 'inherit' !== $value ) { $class .= ' rdct-bld--' . $device . '-' . $name; $style .= '--builder-' . $device . '-' . $name . ':' . $value . ';'; }
		}
		if ( $row && 'inherit' !== ( $appearance[ $device . 'Direction' ] ?? 'inherit' ) ) { $class .= ' rdct-bld__row--' . $device . '-flow-' . $appearance[ $device . 'Direction' ]; }
		if ( $row && 'mobile' !== $device && 'inherit' !== ( $appearance[ $device . 'Layout' ] ?? 'inherit' ) ) { $class .= ' rdct-bld__row--' . $device . '-' . $appearance[ $device . 'Layout' ]; }
	}
	foreach ( array( 'laptop', 'tablet' ) as $device ) {
		if ( $row ) {
			foreach ( array( 'Gap' => 'gap', 'Padding' => 'padding', 'Columns' => 'columns' ) as $key => $name ) {
				if ( isset( $appearance[ $device . $key ] ) ) { $style .= '--builder-' . $device . '-' . $name . ':' . $appearance[ $device . $key ] . ( 'Columns' === $key ? '' : 'px' ) . ';'; }
			}
		} elseif ( ( $appearance[ $device . 'FontSize' ] ?? 0 ) > 0 ) {
			$class .= ' rdct-bld--' . $device . '-font-custom';
			$style .= '--builder-' . $device . '-font-size:' . $appearance[ $device . 'FontSize' ] . 'px;';
		}
	}
	if ( ! $row ) {
		foreach ( array( 'laptop', 'tablet', 'mobile' ) as $device ) {
			$width = $appearance[ $device . 'Width' ] ?? 'inherit';
			if ( 'inherit' !== $width ) { $class .= ' rdct-bld__item--' . $device . '-' . $width; }
		}
	}
	return array( $class, $style );
}

function collapse( array $row, array $items ): array {
	$hidden = $row['hiddenDevices'] ?? array();
	foreach ( array( 'desktop', 'laptop', 'tablet', 'mobile' ) as $device ) {
		$visible = false;
		foreach ( $items as $item ) {
			$legacy = $item['visibility'] ?? 'all';
			if ( ! in_array( $device, $item['hiddenDevices'] ?? array(), true ) && ! ( 'desktop' === $legacy && 'mobile' === $device ) && ! ( 'mobile' === $legacy && 'mobile' !== $device ) ) { $visible = true; break; }
		}
		if ( ! $visible ) { $hidden[] = $device; }
	}
	$row['hiddenDevices'] = array_values( array_unique( $hidden ) );
	return $row;
}
