<?php

namespace RadmanCTChild\Blog\Single;

defined( 'ABSPATH' ) || exit;

function cover_active(): bool {
	if ( ! is_singular( 'post' ) ) { return false; }
	$sidebar = \RadmanCT\Sidebars\current();
	if ( ! $sidebar || 'blog_single' !== $sidebar['scope'] ) { return false; }
	$areas = wp_get_sidebars_widgets();
	foreach ( $areas[ $sidebar['area'] ] ?? array() as $widget ) {
		if ( str_starts_with( $widget, 'rdct-article-cover-' ) ) { return true; }
	}
	return false;
}

function assets(): void {
	if ( array( 'blog', 'single' ) !== \RadmanCT\Renderers\context() || \RadmanCT\Renderers\uses_elementor() ) { return; }
	\RadmanCT\Assets\style( 'rdct-client-blog-single', 'src/css/pages/blog/single.css', 'child', array( 'radmanct-child', 'rdct-blog' ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 25 );
