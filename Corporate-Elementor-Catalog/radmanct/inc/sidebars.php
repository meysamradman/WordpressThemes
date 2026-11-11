<?php

namespace RadmanCT\Sidebars;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/ui/category-widget.php';
require_once __DIR__ . '/ui/search-widget.php';

function register(): void {
	$sections = array( 'blog' => __( 'Blog', 'radmanct' ), 'catalog' => __( 'Catalog', 'radmanct' ), 'portfolio' => __( 'Portfolio', 'radmanct' ) );
	$views = array( 'archive' => __( 'Archive', 'radmanct' ), 'single' => __( 'Single', 'radmanct' ) );
	foreach ( $sections as $section => $title ) {
		if ( 'blog' !== $section && ( ! function_exists( '\\RadmanCTContent\\Modules\\enabled' ) || ! \RadmanCTContent\Modules\enabled( $section ) ) ) { continue; }
		foreach ( $views as $view => $label ) {
			register_sidebar( array(
				'id' => 'rdct-' . $section . '-' . $view,
				'name' => $title . ' — ' . $label,
				'before_widget' => '<section id="%1$s" class="rdct-widget widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h2 class="rdct-widget__title">', 'after_title' => '</h2>',
			) );
		}
	}
}

function current(): array {
	if ( is_admin() || is_feed() ) { return array(); }
	$context = \RadmanCT\Renderers\context();
	$scope = $context ? implode( '_', $context ) : '';
	if ( ! in_array( $scope, scopes(), true ) ) { return array(); }
	$values = \RadmanCT\Settings\values();
	if ( empty( $values[ $scope . '_sidebar_enabled' ] ) ) { return array(); }
	if ( \RadmanCT\Renderers\uses_elementor() ) { return array(); }
	$area = $values[ $scope . '_sidebar_area' ];
	global $wp_registered_sidebars;
	if ( ! isset( $wp_registered_sidebars[ $area ] ) || ! is_active_sidebar( $area ) ) { return array(); }
	return array( 'scope' => $scope, 'area' => $area, 'position' => $values[ $scope . '_sidebar_position' ], 'width' => $values[ $scope . '_sidebar_width' ] );
}

function render( string $partial, array $args = array() ): void {
	$sidebar = current();
	if ( apply_filters( 'radmanct/sidebar/embedded', false, $partial ) ) { get_template_part( $partial, null, $args + array( 'sidebar' => $sidebar ) ); return; }
	if ( ! $sidebar ) { get_template_part( $partial, null, $args ); return; }
	layout( static function () use ( $partial, $args ): void { get_template_part( $partial, null, $args ); }, $sidebar );
}

function layout( callable $content, array $sidebar, bool $contained = true ): void {
	if ( ! $sidebar ) { $content(); return; }
	echo '<div class="' . ( $contained ? 'rdct-ctr ' : '' ) . 'rdct-content-layout" data-sidebar-position="' . esc_attr( $sidebar['position'] ) . '" style="--rdct-sidebar-width:' . (int) $sidebar['width'] . 'px">';
	$content();
	get_template_part( 'template-parts/content/sidebar', null, $sidebar );
	echo '</div>';
}

add_action( 'widgets_init', __NAMESPACE__ . '\\register' );
add_action( 'wp_enqueue_scripts', static function (): void {
	$sidebar = current();
	if ( ! $sidebar ) { return; }
	\RadmanCT\Assets\style( 'rdct-content-sidebar', 'src/css/content-sidebar.css', 'parent', array( 'rdct-theme' ) );
	$areas = wp_get_sidebars_widgets();
	$needs_fields = false;
	foreach ( $areas[ $sidebar['area'] ] ?? array() as $id ) {
		$fields = str_starts_with( $id, 'search-' );
		if ( $fields && function_exists( '\\RadmanCTComponents\\Components\\enqueue' ) ) { \RadmanCTComponents\Components\enqueue( 'archive-search' ); }
		if ( str_starts_with( $id, 'categories-' ) ) {
			$instances = get_option( 'widget_categories', array() );
			$fields = ! empty( $instances[ (int) substr( $id, strlen( 'categories-' ) ) ]['dropdown'] );
			if ( ! $fields && function_exists( '\\RadmanCTComponents\\Components\\use_component' ) ) { \RadmanCTComponents\Components\use_component( 'category-tree' ); }
		}
		if ( str_starts_with( $id, 'block-' ) ) {
			$instances = get_option( 'widget_block', array() );
			$content = $instances[ (int) substr( $id, strlen( 'block-' ) ) ]['content'] ?? '';
			$fields = has_block( 'core/search', $content ) || str_contains( $content, '<select' ) || str_contains( $content, '<input' );
		}
		$needs_fields = $needs_fields || $fields;
	}
	if ( $needs_fields && function_exists( '\\RadmanCTComponents\\UI\\field_assets' ) ) { \RadmanCTComponents\UI\field_assets(); }
}, 20 );
