<?php

namespace RadmanCTChild\Blog\Sidebar;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/cards.php';

function register(): void {
	require_once __DIR__ . '/widgets/recent-posts.php';
	require_once __DIR__ . '/widgets/article-cover.php';
	register_widget( Recent_Posts::class );
	register_widget( Article_Cover::class );
}

function assets(): void {
	$sidebar = \RadmanCT\Sidebars\current();
	if ( ! $sidebar || ! str_starts_with( $sidebar['scope'], 'blog_' ) ) { return; }
	\RadmanCT\Assets\style( 'rdct-client-blog-sidebar', 'src/css/blog/sidebar.css', 'child', array( 'rdct-content-sidebar' ) );
	$script = \RadmanCT\Assets\entry( 'src/js/blog/sidebar.js', 'child' );
	if ( $script ) { wp_enqueue_script_module( 'rdct-client-blog-sidebar', \RadmanCT\Assets\url( $script['file'], 'child' ), array(), null ); }
	$areas = wp_get_sidebars_widgets();
	foreach ( $areas[ $sidebar['area'] ] ?? array() as $widget ) {

		if ( str_starts_with( $widget, 'rdct-blog-posts-' ) ) { \RadmanCTChild\Blog\card_assets(); }
	}
}

add_action( 'widgets_init', __NAMESPACE__ . '\\register' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 25 );
add_filter( 'radmanct/sidebar/embedded', static function ( bool $embedded, string $partial ): bool { return 'template-parts/blog/archive' === $partial || $embedded; }, 10, 2 );
add_filter( 'dynamic_sidebar_params', static function ( array $params ): array {
	$sidebar = \RadmanCT\Sidebars\current();
	if ( $sidebar && str_starts_with( $sidebar['scope'], 'blog_' ) && ( $params[0]['id'] ?? '' ) === $sidebar['area'] ) {
		$params[0]['before_title'] = '<h3 class="rdct-widget__title">';
		$params[0]['after_title'] = '</h3>';
	}
	return $params;
} );
