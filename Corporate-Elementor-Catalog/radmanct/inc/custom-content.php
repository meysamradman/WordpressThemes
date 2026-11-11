<?php

namespace RadmanCT\CustomContent;

defined( 'ABSPATH' ) || exit;

function key( $value ): string {
	return is_string( $value ) && preg_match( '/^[a-z][a-z0-9-]{0,79}$/D', $value ) ? $value : '';
}

function registry( ?string $id = null, ?array $definition = null ): array {
	static $items = array();
	if ( null !== $id && null !== $definition && key( $id ) && ! isset( $items[ $id ] ) && is_string( $definition['title'] ?? null ) && is_callable( $definition['render'] ?? null ) && ( ! isset( $definition['assets'] ) || is_callable( $definition['assets'] ) ) ) {
		$items[ $id ] = $definition;
	}
	return $items;
}

function options(): array {
	$choices = array( '' => __( 'None', 'radmanct' ) );
	foreach ( registry() as $id => $definition ) { $choices[ $id ] = $definition['title']; }
	return $choices;
}

function prepare( string $id ): void {
	static $prepared = array();
	$definition = registry()[ $id ] ?? null;
	if ( ! $definition || isset( $prepared[ $id ] ) ) { return; }
	$prepared[ $id ] = true;
	try {
		if ( isset( $definition['assets'] ) ) { $definition['assets'](); }
	} catch ( \Throwable $error ) {
		unset( $prepared[ $id ] );
		throw $error;
	}
}

function render( string $id, array $context = array() ): string {
	static $active = array();
	$definition = registry()[ $id ] ?? null;
	if ( ! $definition || isset( $active[ $id ] ) || count( $active ) >= 8 ) { return ''; }
	$active[ $id ] = true;
	try {
		prepare( $id );
		$context['instance_id'] = wp_unique_id( 'rdct-content-' );
		ob_start();
		try {
			$returned = $definition['render']( $context );
			return (string) ob_get_contents() . ( is_string( $returned ) ? $returned : '' );
		} finally { ob_end_clean(); }
	} finally { unset( $active[ $id ] ); }
}
