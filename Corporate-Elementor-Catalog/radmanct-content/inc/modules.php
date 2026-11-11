<?php

namespace RadmanCTContent\Modules;

defined( 'ABSPATH' ) || exit;

function options(): array {
	$raw = get_option( 'radman_core_modules', array() );
	$raw = is_array( $raw ) ? $raw : array();
	return array( 'catalog' => in_array( $raw['catalog'] ?? false, array( true, 1, '1' ), true ), 'portfolio' => in_array( $raw['portfolio'] ?? false, array( true, 1, '1' ), true ) );
}

function enabled( string $module ): bool {
	return options()[ $module ] ?? false;
}

function types(): array {
	$result = array();
	foreach ( array( 'catalog' => 'radman_product', 'portfolio' => 'radman_portfolio' ) as $module => $type ) {
		if ( enabled( $module ) ) {
			$result[] = $type;
		}
	}
	return $result;
}

function boot(): void {
	if ( ! types() ) {
		return;
	}
	require_once __DIR__ . '/model.php';
	require_once __DIR__ . '/wpml.php';
	require_once __DIR__ . '/content.php';
	if ( enabled( 'catalog' ) ) {
		require_once __DIR__ . '/catalog.php';
	}
	add_action( 'init', __NAMESPACE__ . '\\register_types' );
	if ( is_admin() ) {
		require_once __DIR__ . '/admin/content.php';
		require_once __DIR__ . '/admin/content-list.php';
		if ( enabled( 'catalog' ) ) { require_once __DIR__ . '/admin/catalog-list.php'; }
	}
}

function register_types(): void {
	if ( ! types() ) {
		return;
	}
	require_once __DIR__ . '/content.php';
	require_once __DIR__ . '/model.php';
	\RadmanCTContent\Content\register();
}
