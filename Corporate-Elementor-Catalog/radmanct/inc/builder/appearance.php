<?php

namespace RadmanCT\Builder;

defined( 'ABSPATH' ) || exit;

function appearance( $input, bool $row ): ?array {
	if ( ! is_array( $input ) ) {
		return null;
	}
	$result = array( 'visibility' => 'all', 'align' => 'start', 'width' => 'equal', 'customWidth' => 50, 'color' => '', 'fontSize' => 0, 'mobileFontSize' => 0 );
	if ( $row ) {
		$result = array( 'visibility' => 'all', 'align' => 'grid' === ( $input['rowLayout'] ?? 'flex' ) ? 'start' : 'center', 'rowLayout' => 'flex', 'columns' => 3, 'mobileColumns' => 1, 'gap' => 24, 'padding' => 16, 'background' => '', 'justify' => 'start', 'color' => '', 'mobileLayout' => 'stack', 'mobileGap' => null, 'mobilePadding' => null );
	}
	if ( $row ) { $result += array( 'direction' => 'row', 'wrap' => 'wrap' ); }
	if ( $row ) { $result['backgroundOptions'] = array(); }
	$responsive = Responsive\defaults( $row );
	$result += $responsive;
	foreach ( $input as $key => $value ) {
		if ( ! array_key_exists( $key, $result ) ) {
			continue;
		}
		if ( 'backgroundOptions' === $key ) {
			if ( ! function_exists( '\\RadmanCTComponents\\Background\\normalize' ) ) { if ( array() !== $value ) { return null; } }
			else { $value = \RadmanCTComponents\Background\normalize( $value, array( 'color', 'gradient' ) ); if ( null === $value ) { return null; } }
			$result[ $key ] = $value;
		} elseif ( array_key_exists( $key, $responsive ) ) {
			if ( ! Responsive\valid( $key, $value, $row ) ) { return null; }
			$result[ $key ] = $value;
		} elseif ( 'visibility' === $key && in_array( $value, array( 'all', 'desktop', 'mobile' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( 'align' === $key && in_array( $value, $row ? array( 'start', 'center', 'end', 'stretch' ) : array( 'start', 'center', 'end' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( 'customWidth' === $key && is_int( $value ) && $value >= 5 && $value <= 100 ) {
			$result[ $key ] = $value;
		} elseif ( 'width' === $key && in_array( $value, array( 'equal', 'auto', 'full', 'custom' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( 'direction' === $key && $row && in_array( $value, array( 'row', 'column' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( 'wrap' === $key && $row && in_array( $value, array( 'wrap', 'nowrap' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( 'rowLayout' === $key && in_array( $value, array( 'flex', 'grid' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( in_array( $key, array( 'columns', 'mobileColumns' ), true ) && is_int( $value ) && $value >= 1 && $value <= ( 'columns' === $key ? 6 : 3 ) ) {
			$result[ $key ] = $value;
		} elseif ( 'justify' === $key && in_array( $value, array( 'start', 'center', 'end', 'space-between' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( in_array( $key, array( 'gap', 'padding' ), true ) && is_int( $value ) && $value >= 0 && $value <= ( 'gap' === $key ? 64 : 120 ) ) {
			$result[ $key ] = $value;
		} elseif ( in_array( $key, array( 'mobileGap', 'mobilePadding' ), true ) && ( null === $value || ( is_int( $value ) && $value >= 0 && $value <= ( 'mobileGap' === $key ? 64 : 120 ) ) ) ) {
			$result[ $key ] = $value;
		} elseif ( 'mobileLayout' === $key && in_array( $value, array( 'stack', 'inline' ), true ) ) {
			$result[ $key ] = $value;
		} elseif ( in_array( $key, array( 'fontSize', 'mobileFontSize' ), true ) && is_int( $value ) && $value >= 0 && $value <= 96 ) {
			$result[ $key ] = $value;
		} elseif ( in_array( $key, array( 'background', 'color' ), true ) && is_string( $value ) && ( '' === $value || preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) ) {
			$result[ $key ] = strtolower( $value );
		} else {
			return null;
		}
	}
	return $result;
}

function appearance_attributes( array $appearance, bool $row, array $presentation = array() ): string {
	$class = ( $row ? 'rdct-bld__row' : 'rdct-bld__item' ) . ' rdct-bld--' . $appearance['visibility'];
	if ( ! $row ) {
		$class .= ' rdct-bld__item--' . $appearance['width'];
	}
	$style = '--builder-align:' . $appearance['align'] . ';';
	[ $responsive_class, $responsive_style ] = Responsive\attributes( $appearance, $row );
	$class .= $responsive_class;
	$style .= $responsive_style;
	if ( '' !== ( $appearance['color'] ?? '' ) ) {
		$class .= ' rdct-bld--colored';
		$style .= '--builder-text-color:' . $appearance['color'] . ';';
	}
	if ( ! $row ) {
		if ( in_array( 'custom', array_intersect_key( $appearance, array_flip( array( 'width', 'laptopWidth', 'tabletWidth', 'mobileWidth' ) ) ), true ) ) {
			$style .= '--builder-custom-width:' . ( $appearance['customWidth'] ?? 50 ) . '%;';
			foreach ( array( 'laptop', 'tablet', 'mobile' ) as $device ) { if ( isset( $appearance[ $device . 'CustomWidth'] ) ) { $style .= '--builder-' . $device . '-custom-width:' . $appearance[ $device . 'CustomWidth'] . '%;'; } }
		}
		foreach ( array( 'fontSize' => 'font', 'mobileFontSize' => 'mobile-font' ) as $property => $name ) {
			if ( ( $appearance[ $property ] ?? 0 ) > 0 ) {
				$class .= ' rdct-bld--' . $name . '-custom';
				$style .= '--builder-' . $name . '-size:' . $appearance[ $property ] . 'px;';
			}
		}
	}
	if ( $row ) {
		$class .= ' rdct-bld__row--flow-' . ( $appearance['direction'] ?? 'row' );
		$style .= '--builder-wrap:' . ( $appearance['wrap'] ?? 'wrap' ) . ';';
		if ( 'grid' === ( $appearance['rowLayout'] ?? 'flex' ) ) {
			$class .= ' rdct-bld__row--grid';
			$style .= '--builder-columns:' . $appearance['columns'] . ';--builder-mobile-columns:' . $appearance['mobileColumns'] . ';';
		}
		if ( 'inline' === ( $appearance['mobileLayout'] ?? 'stack' ) ) {
			$class .= ' rdct-bld__row--mobile-inline';
		}
		$background = $appearance['background'] ?: 'transparent';
		if ( ! empty( $appearance['backgroundOptions'] ) && function_exists( '\\RadmanCTComponents\\Background\\paint' ) ) { $background = \RadmanCTComponents\Background\paint( $appearance['backgroundOptions'], $background ); }
		$style .= '--builder-gap:' . $appearance['gap'] . 'px;--builder-padding:' . $appearance['padding'] . 'px;--builder-background:' . $background . ';--builder-justify:' . $appearance['justify'] . ';';
		foreach ( array( 'mobileGap' => 'gap', 'mobilePadding' => 'padding' ) as $property => $name ) {
			if ( null !== ( $appearance[ $property ] ?? null ) ) {
				$style .= '--builder-mobile-' . $name . ':' . $appearance[ $property ] . 'px;';
			}
		}
	}
	if ( ! empty( $presentation['class'] ) ) { $class .= ' ' . $presentation['class']; }
	$style .= $presentation['style'] ?? '';
	return 'class="' . esc_attr( $class ) . '" style="' . esc_attr( $style ) . '"';
}
