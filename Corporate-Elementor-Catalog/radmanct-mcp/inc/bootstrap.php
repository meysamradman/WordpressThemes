<?php

namespace RadmanCT\Mcp;

defined( 'ABSPATH' ) || exit;

function enabled(): bool {
	return '1' === (string) get_option( 'radmanct_mcp_enabled', '0' );
}

function permission( bool $write = false ): bool {
	return enabled() && current_user_can( 'manage_options' ) && ( ! $write || '1' === (string) get_option( 'radmanct_mcp_write', '0' ) );
}

function boot(): void {
	if ( ! enabled() ) { return; }
	add_action( 'wp_abilities_api_init', __NAMESPACE__ . '\\register_abilities' );
	add_action( 'wp_abilities_api_categories_init', static function (): void {
		wp_register_ability_category( 'radmanct', array( 'label' => 'Radman CT', 'description' => 'Optional content and PHP layout management.' ) );
	} );
}

function register_abilities(): void {
	if ( ! enabled() ) { return; }
	require_once __DIR__ . '/integrations/content.php';
	require_once __DIR__ . '/integrations/elementor.php';
	require_once __DIR__ . '/integrations/theme.php';
	require_once __DIR__ . '/abilities/content.php';
	require_once __DIR__ . '/abilities/registry.php';
	register();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\boot', 40 );
