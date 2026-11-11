<?php

namespace RadmanCT\Integrations\Rocket;

defined( 'ABSPATH' ) || exit;

function delay_exclusions( array $excluded ): array {
	$urls = array( get_template_directory_uri() . '/dist/js/', get_stylesheet_directory_uri() . '/dist/js/' );
	if ( defined( 'RDCT_COMPONENTS_FILE' ) ) { $urls[] = plugins_url( 'dist/js/', RDCT_COMPONENTS_FILE ); }
	foreach ( $urls as $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( is_string( $path ) && '' !== $path ) { $excluded[] = preg_quote( $path, '#' ); }
	}
	$excluded[] = 'rdct-color-scheme-init';
	$excluded[] = 'rdct-preloader';
	return array_values( array_unique( $excluded ) );
}

function inline_defer_exclusions( array $excluded ): array {
	$excluded[] = 'rdct-preloader';
	return array_values( array_unique( $excluded ) );
}

function purge(): void {
	if ( function_exists( 'rocket_clean_domain' ) ) { rocket_clean_domain(); }
}

function activation_notice( bool $show ): bool {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && str_contains( $screen->id, '_page_rdct-' ) ? false : $show;
}

function queue_purge(): void {
	if ( ! has_action( 'shutdown', __NAMESPACE__ . '\\purge' ) ) { add_action( 'shutdown', __NAMESPACE__ . '\\purge' ); }
}

function option_changed( string $option ): void {
	if ( 'radman_settings' === $option || 'radman_builder_library' === $option || str_starts_with( $option, 'radman_builder_' ) || 'theme_mods_' . get_stylesheet() === $option ) {
		queue_purge();
	}
}

add_filter( 'rocket_delay_js_exclusions', __NAMESPACE__ . '\\delay_exclusions' );
add_filter( 'rocket_defer_inline_exclusions', __NAMESPACE__ . '\\inline_defer_exclusions' );
add_filter( 'rocket_display_default_notice', __NAMESPACE__ . '\\activation_notice' );
add_action( 'updated_option', __NAMESPACE__ . '\\option_changed', 10, 1 );
add_action( 'added_option', __NAMESPACE__ . '\\option_changed', 10, 1 );
add_action( 'deleted_option', __NAMESPACE__ . '\\option_changed', 10, 1 );
add_action( 'radmanct/builder/saved', __NAMESPACE__ . '\\queue_purge', 10, 0 );
