<?php
namespace RadmanCT\Icons;

defined( 'ABSPATH' ) || exit;

function catalog(): array {
	if ( function_exists( '\\RadmanCTComponents\\Icons\\catalog' ) ) { return \RadmanCTComponents\Icons\catalog(); }
	static $base = null;
	if ( null === $base ) { $base = require __DIR__ . '/icons/catalog.php'; }
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\catalog' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	return \RadmanCTShared\Icons\catalog( $base );
}

function labels(): array {
	$result = array();
	foreach ( catalog() as $name => $icon ) { $result[ $name ] = __( $icon['label'], 'radmanct' ); }
	return $result;
}

function picker_data(): array {
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\render' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	return \RadmanCTShared\Icons\picker_data( catalog(), labels() );
}

function render( string $name, array $options = array() ): string {
	$icon = catalog()[ $name ] ?? null;
	if ( ! $icon ) { return ''; }
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\render' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	return \RadmanCTShared\Icons\render( $icon, $options );
}
