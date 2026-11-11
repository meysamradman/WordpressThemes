<?php

namespace RadmanCT\Components;

defined( 'ABSPATH' ) || exit;

function definitions(): array {
	$definitions = array(
		'icons' => array( 'handle' => 'rdct-icons', 'entry' => 'src/css/components/icons.css' ),
		'theme-toggle' => array( 'handle' => 'rdct-theme-toggle', 'entry' => 'src/css/components/theme-toggle.css' ),
		'menu-hover-roll' => array( 'handle' => 'rdct-menu-hover-roll', 'entry' => 'src/css/components/menu-hover/roll.css' ),
		'menu-indicator-dot' => array( 'handle' => 'rdct-menu-indicator-dot', 'entry' => 'src/css/components/menu-indicators/dot.css' ),
		'menu-dropdown' => array( 'handle' => 'rdct-menu-dropdown', 'entry' => 'src/css/components/menu-dropdown.css' ),
		'menu-mega' => array( 'handle' => 'rdct-menu-mega', 'entry' => 'src/css/components/menu-mega.css' ),
		'menu-drawer' => array( 'handle' => 'rdct-menu-drawer', 'entry' => 'src/css/components/menu-drawer.css' ),
		'menu-panel-fullscreen' => array( 'handle' => 'rdct-menu-panel-fullscreen', 'entry' => 'src/css/components/menu-panel/fullscreen.css' ),
		'menu-panel-split' => array( 'handle' => 'rdct-menu-panel-split', 'entry' => 'src/css/components/menu-panel/split.css' ),
		'menu-skin-line-fill' => array( 'handle' => 'rdct-menu-skin-line-fill', 'entry' => 'src/css/components/menu-skins/line-fill.css' ),
		'menu-skin-color' => array( 'handle' => 'rdct-menu-skin-color', 'entry' => 'src/css/components/menu-skins/color.css' ),
		'menu-skin-pill' => array( 'handle' => 'rdct-menu-skin-pill', 'entry' => 'src/css/components/menu-skins/pill.css' ),
		'builder-links' => array( 'handle' => 'rdct-builder-links', 'entry' => 'src/css/components/builder-links.css' ),
	);
	foreach ( array( 'sidebar', 'motion', 'wave' ) as $part ) { $definitions[ 'menu-panel-' . $part ] = array( 'handle' => 'rdct-menu-panel-' . $part, 'entry' => 'src/css/components/menu-panel/' . $part . '.css' ); }
	return $definitions;
}

function register_assets(): void {
	foreach ( definitions() as $definition ) {
		if ( wp_style_is( $definition['handle'], 'registered' ) ) { continue; }
		$asset = \RadmanCT\Assets\entry( $definition['entry'] );
		if ( $asset ) {
			wp_register_style( $definition['handle'], \RadmanCT\Assets\url( $asset['file'] ), array(), null );
		}
	}
}

function use_component( string $name ): void {
	$definition = definitions()[ $name ] ?? null;
	if ( ! $definition ) {
		if ( function_exists( '\\RadmanCTComponents\\Components\\use_component' ) ) { \RadmanCTComponents\Components\use_component( $name ); }
		return;
	}
	if ( ! wp_style_is( $definition['handle'], 'registered' ) ) {
		register_assets();
	}
	wp_enqueue_style( $definition['handle'] );
}

function print_late_style( string $name ): void {
	$definition = definitions()[ $name ] ?? null;
	if ( $definition && did_action( 'wp_head' ) && ! wp_style_is( $definition['handle'], 'done' ) ) {
		wp_print_styles( array( $definition['handle'] ) );
	}
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_assets', 9 );
