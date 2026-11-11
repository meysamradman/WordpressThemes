<?php

namespace RadmanCT\Builder\HeaderColors;

defined( 'ABSPATH' ) || exit;

function validate( $input ): ?array {
	if ( ! is_array( $input ) || ! is_bool( $input['enabled'] ?? false ) ) { return null; }
	$result = array( 'enabled' => $input['enabled'] ?? false );
	foreach ( array( 'light', 'dark', 'sticky' ) as $state ) {
		$color = $input[ $state ] ?? '';
		if ( ! is_string( $color ) || ( '' !== $color && ! preg_match( '/^#[a-f0-9]{6}$/iD', $color ) ) ) { return null; }
		$result[ $state ] = $color;
	}
	return $result;
}

function style( array $layout ): string {
	$colors = validate( $layout['headerColors'] ?? array() );
	if ( ! $colors || ! $colors['enabled'] ) { return ''; }
	$css = '';
	foreach ( array( 'light', 'dark', 'sticky' ) as $state ) {
		if ( '' !== $colors[ $state ] ) { $css .= '--rdct-header-tone-' . $state . ':' . $colors[ $state ] . ';'; }
	}
	return $css;
}

function item_mode( array $item ): string {
	$mode = $item['headerColorMode'] ?? 'auto';
	if ( 'auto' !== $mode ) { return $mode; }
	$manual = ! empty( $item['appearance']['color'] ) || ! empty( $item['triggerColor'] ) || ! empty( $item['buttonOptions']['color'] );
	return $manual ? 'fixed' : 'inherit';
}
