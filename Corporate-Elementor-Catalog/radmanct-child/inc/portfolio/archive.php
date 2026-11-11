<?php

namespace RadmanCTChild\Portfolio;

defined( 'ABSPATH' ) || exit;

function query( \WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || $query->is_feed() || $query->is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
	if ( ! $query->is_post_type_archive( 'radman_portfolio' ) && ! $query->is_tax( 'radman_portfolio_cat' ) ) { return; }
	if ( \RadmanCT\Renderers\uses_elementor() ) { return; }
	$query->set( 'posts_per_page', 8 );
	$query->set( 'orderby', array( 'date' => 'DESC', 'ID' => 'DESC' ) );
}

function assets(): void {
	if ( 'portfolio' !== \RadmanCT\Archives\scope() ) { return; }
	\RadmanCT\Assets\style( 'rdct-client-portfolio', 'src/css/pages/portfolio/archive.css', 'child', array( 'radmanct-child', 'rdct-portfolio' ) );
	$script = \RadmanCT\Assets\entry( 'src/js/portfolio/media.js', 'child' );
	if ( $script ) { wp_enqueue_script_module( 'rdct-client-portfolio-media', \RadmanCT\Assets\url( $script['file'], 'child' ), array(), null ); }
}

add_action( 'pre_get_posts', __NAMESPACE__ . '\\query', 20 );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 21 );

add_filter( 'radmanct/portfolio/archive_style', static function ( string $entry ): string { return 'src/css/portfolio-shell.css'; } );
