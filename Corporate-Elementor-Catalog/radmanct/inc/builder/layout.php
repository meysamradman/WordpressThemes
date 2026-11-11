<?php

namespace RadmanCT\Builder\Layout;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/layout-settings.php';

function validate( array $input ): ?array {
	$result = array();
	foreach ( array( 'customWidth' => 'width', 'gutter' => 'gutter', 'laptopGutter' => 'gutter', 'tabletGutter' => 'gutter', 'mobileGutter' => 'mobile_gutter' ) as $key => $field ) {
		$value = $input[ $key ] ?? null;
		$range = \RadmanCT\Layout\fields()[ $field ];
		if ( null !== $value && ( ! is_int( $value ) || $value < $range[0] || $value > $range[1] ) ) { return null; }
		$result[ $key ] = $value;
	}
	$border = border( $input['border'] ?? array() );
	if ( null === $border ) { return null; }
	$result['border'] = $border;
	return $result;
}

function border( $input ): ?array {
	if ( ! is_array( $input ) ) { return null; }
	$result = array( 'style' => 'none', 'color' => '', 'top' => 0, 'bottom' => 0, 'start' => 0, 'end' => 0 );
	foreach ( $input as $key => $value ) {
		if ( ! array_key_exists( $key, $result ) ) { return null; }
		if ( 'style' === $key ) {
			if ( ! in_array( $value, array( 'none', 'solid', 'dashed', 'dotted', 'double' ), true ) ) { return null; }
		} elseif ( 'color' === $key ) {
			if ( ! is_string( $value ) || ( '' !== $value && ! preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) ) { return null; }
			$value = strtolower( $value );
		} elseif ( ! is_int( $value ) || $value < 0 || $value > 20 ) { return null; }
		$result[ $key ] = $value;
	}
	return $result;
}

function border_style( array $layout ): string {
	$border = border( $layout['border'] ?? array() );
	if ( null === $border || 'none' === $border['style'] ) { return ''; }
	$css = '';
	foreach ( array( 'top' => 'block-start', 'bottom' => 'block-end', 'start' => 'inline-start', 'end' => 'inline-end' ) as $key => $side ) {
		if ( $border[ $key ] > 0 ) { $css .= 'border-' . $side . ':' . $border[ $key ] . 'px ' . $border['style'] . ' ' . ( $border['color'] ?: 'currentColor' ) . ';'; }
	}
	return $css;
}

function attributes( array $layout, string $extra_css = '' ): string {
	$max = 'full' === ( $layout['width'] ?? 'full' ) ? 'none' : ( 'custom' === $layout['width'] && isset( $layout['customWidth'] ) ? $layout['customWidth'] . 'px' : 'var(--rdct-container)' );
	$css = '--builder-content-max:' . $max . ';--builder-gutter:' . ( isset( $layout['gutter'] ) ? $layout['gutter'] . 'px' : 'var(--rdct-gutter)' ) . ';--builder-mobile-gutter:' . ( isset( $layout['mobileGutter'] ) ? $layout['mobileGutter'] . 'px' : 'var(--builder-gutter)' ) . ';';
	foreach ( array( 'laptop', 'tablet' ) as $device ) {
		if ( isset( $layout[ $device . 'Gutter' ] ) ) { $css .= '--builder-' . $device . '-gutter:' . $layout[ $device . 'Gutter' ] . 'px;'; }
	}
	return ' style="' . esc_attr( $css . border_style( $layout ) . $extra_css ) . '"';
}
