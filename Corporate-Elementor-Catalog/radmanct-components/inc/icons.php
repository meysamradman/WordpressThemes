<?php

namespace RadmanCTComponents\Icons;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/components/presentation.php';

function catalog(): array {
	static $icons = null;
	if ( null === $icons ) {
		$icons = require __DIR__ . '/icons/catalog.php';
	}
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\catalog' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	return \RadmanCTShared\Icons\catalog( $icons );
}

function labels(): array {
	$labels = array(
		'arrow-left' => __( 'Arrow left', 'radmanct-components' ), 'arrow-right' => __( 'Arrow right', 'radmanct-components' ),
		'chevron-left' => __( 'Chevron left', 'radmanct-components' ), 'chevron-right' => __( 'Chevron right', 'radmanct-components' ),
		'chevron-up' => __( 'Chevron up', 'radmanct-components' ), 'chevron-down' => __( 'Chevron down', 'radmanct-components' ),
		'menu' => __( 'Menu', 'radmanct-components' ), 'x' => __( 'Close', 'radmanct-components' ), 'check' => __( 'Check', 'radmanct-components' ),
		'search' => __( 'Search', 'radmanct-components' ), 'phone' => __( 'Phone', 'radmanct-components' ), 'mail' => __( 'Email', 'radmanct-components' ),
		'map-pin' => __( 'Location', 'radmanct-components' ), 'external-link' => __( 'External link', 'radmanct-components' ),
		'download' => __( 'Download', 'radmanct-components' ), 'play' => __( 'Play', 'radmanct-components' ), 'pause' => __( 'Pause', 'radmanct-components' ),
		'clock' => __( 'Clock', 'radmanct-components' ), 'plus' => __( 'Plus', 'radmanct-components' ), 'minus' => __( 'Minus', 'radmanct-components' ),
	);
	$result = array();
	foreach ( catalog() as $name => $icon ) {
		$result[ $name ] = $labels[ $name ] ?? __( $icon['label'], 'radmanct-components' );
	}
	return $result;
}

function render( string $name, array $options = array() ): string {
	$icon = catalog()[ $name ] ?? null;
	if ( ! $icon ) {
		return '';
	}
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\render' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	$html = \RadmanCTShared\Icons\render( $icon, $options );
	return \RadmanCTComponents\Components\Presentation\render( $html, $options, 'icon' );
}

function picker_data(): array {
	if ( ! function_exists( '\\RadmanCTShared\\Icons\\render' ) ) { require_once __DIR__ . '/icons/engine.php'; }
	return \RadmanCTShared\Icons\picker_data( catalog(), labels() );
}
