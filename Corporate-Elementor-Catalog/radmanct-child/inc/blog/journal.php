<?php

namespace RadmanCTChild\Blog\Journal;

defined( 'ABSPATH' ) || exit;

function assets(): void {
	global $wp_query;
	if ( 'blog' !== \RadmanCT\Archives\scope() ) { return; }
	if ( $wp_query->post_count > 0 || $wp_query->max_num_pages > 1 ) {
		\RadmanCT\Assets\style( 'rdct-client-blog-journal', 'src/css/pages/blog/journal.css', 'child', array( 'radmanct-child', 'rdct-listing' ) );
	}
}

function render(): void {
	global $wp_query;
	$count = $wp_query->post_count - $wp_query->current_post - 1;
	if ( $count > 0 || $wp_query->max_num_pages > 1 ) {
		\RadmanCTChild\Blog\fragment_styles( array( 'src/css/pages/blog/journal.css' ) );
	}
	if ( $count < 1 ) { return; }
	get_template_part( 'template-parts/blog/archive/journal', null, array( 'count' => $count ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 26 );
add_action( 'radmanct/blog/before_grid', __NAMESPACE__ . '\\render', 20 );
