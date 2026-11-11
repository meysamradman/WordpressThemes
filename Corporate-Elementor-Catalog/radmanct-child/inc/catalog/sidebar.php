<?php

namespace RadmanCTChild\Catalog\Sidebar;

defined( 'ABSPATH' ) || exit;

function embedded( bool $embedded, string $partial ): bool {
	return 'template-parts/catalog/archive' === $partial || $embedded;
}

function register(): void {
	if ( ! function_exists( '\\RadmanCTContent\\Modules\\enabled' ) || ! \RadmanCTContent\Modules\enabled( 'catalog' ) ) { return; }
	require_once __DIR__ . '/widgets/archive.php';
	register_widget( Archive_Widget::class );
}

function assets(): void {
	$sidebar = \RadmanCT\Sidebars\current();
	if ( ! $sidebar || ! str_starts_with( $sidebar['scope'], 'catalog_' ) ) { return; }
	$areas = wp_get_sidebars_widgets();
	$instances = get_option( 'widget_rdct-catalog-section', array() );
	foreach ( $areas[ $sidebar['area'] ] ?? array() as $widget ) {
		if ( ! str_starts_with( $widget, 'rdct-catalog-section-' ) ) { continue; }
		$number = (int) substr( $widget, strlen( 'rdct-catalog-section-' ) );
		$kind = $instances[ $number ]['kind'] ?? 'categories';
		if ( 'categories' === $kind && function_exists( '\\RadmanCTComponents\\Components\\use_component' ) ) { \RadmanCTComponents\Components\use_component( 'category-tree' ); }
		if ( 'tags' === $kind && function_exists( '\\RadmanCTComponents\\Components\\use_component' ) ) { \RadmanCTComponents\Components\use_component( 'taxonomy-chips' ); }
		if ( 'search' === $kind ) {
			require_once get_template_directory() . '/inc/integrations/catalog-filters.php';
			\RadmanCT\Content\CatalogFilters\prepare_fields( array( 'rm_brand', 'rm_feature' ) );
		}
	}
	\RadmanCT\Assets\style( 'rdct-client-catalog-sidebar', 'src/css/catalog/sidebar.css', 'child', array( 'rdct-content-sidebar' ) );
	$script = \RadmanCT\Assets\entry( 'src/js/catalog/sidebar.js', 'child' );
	if ( $script ) { wp_enqueue_script_module( 'rdct-client-catalog-sidebar', \RadmanCT\Assets\url( $script['file'], 'child' ), array(), null ); }
}

add_action( 'widgets_init', __NAMESPACE__ . '\\register' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 25 );
add_filter( 'radmanct/sidebar/embedded', __NAMESPACE__ . '\\embedded', 10, 2 );

add_filter( 'radmanct/catalog/filter_fields', static function ( array $keys ): array {
	$sidebar = \RadmanCT\Sidebars\current();
	return $sidebar && str_starts_with( $sidebar['scope'], 'catalog_' ) ? array() : $keys;
} );
