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

add_action( 'widgets_init', __NAMESPACE__ . '\\register' );
