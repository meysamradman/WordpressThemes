<?php

namespace RadmanCT\Builder\ButtonComponent;

defined( 'ABSPATH' ) || exit;

function available(): bool {
	if ( ! defined( 'RDCT_COMPONENTS_PATH' ) || ! is_readable( RDCT_COMPONENTS_PATH . '/inc/components/button.php' ) ) { return false; }
	require_once RDCT_COMPONENTS_PATH . '/inc/components/button.php';
	return function_exists( '\\RadmanCTComponents\\Components\\Button\\render' );
}

function fields(): array {
	if ( ! available() ) { return array(); }
	return array_diff_key( \RadmanCTComponents\Components\Button\fields(), array_flip( array( 'label', 'url', 'new_tab', 'width' ) ) );
}

function validate( $input ): ?array {
	if ( ! is_array( $input ) || count( $input ) > 256 || strlen( wp_json_encode( $input ) ?: '' ) > 16384 ) { return null; }
	$check = static function ( $value, int $depth = 0 ) use ( &$check ): bool {
		if ( $depth > 5 ) { return false; }
		if ( is_array( $value ) ) { foreach ( $value as $key => $child ) { if ( ! preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', (string) $key ) || ! $check( $child, $depth + 1 ) ) { return false; } } return true; }
		return null === $value || is_bool( $value ) || ( is_numeric( $value ) && is_finite( (float) $value ) ) || ( is_string( $value ) && strlen( $value ) <= 2500 );
	};
	if ( ! $check( $input ) ) { return null; }
	if ( ! available() ) { return $input; }
	$clean = array(); $settings = \RadmanCTComponents\Components\Button\settings( $input );
	foreach ( fields() as $key => $field ) { if ( array_key_exists( $key, $input ) ) { $clean[ $key ] = $settings[ $key ]; } }
	foreach ( \RadmanCTComponents\Components\Button\presentation() as $key => $field ) { if ( isset( $input[ $key . '_responsive' ] ) ) { $clean[ $key . '_responsive' ] = $input[ $key . '_responsive' ]; } }
	return $clean;
}

function options( array $item, string $text = '', string $url = '' ): array {
	$legacy = array( 'style' => $item['buttonStyle'] ?? 'solid', 'background' => $item['buttonBackground'] ?? '', 'padding_x' => $item['buttonPaddingX'] ?? 19, 'padding_y' => $item['buttonPaddingY'] ?? 10 );
	if ( isset( $item['buttonRadius'] ) ) { $legacy['radius'] = $item['buttonRadius']; }
	return array_replace( $legacy, $item['buttonOptions'] ?? array(), array( 'label' => $text, 'url' => $url, 'new_tab' => ! empty( $item['newTab'] ), 'width' => 'full' === ( $item['appearance']['width'] ?? '' ) ? '100%' : 'auto' ) );
}

function assets( array $item ): void {
	if ( '' !== trim( $item['text'] ?? '' ) && available() ) { \RadmanCTComponents\Components\enqueue( 'button', options( $item ) ); }
}
