<?php

namespace RadmanCTChild\Blog\Archive;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/cards.php';

function available(): bool {
	global $wp_query;
	return is_home() && ! is_paged() && is_main_query() && 'blog' === \RadmanCT\Archives\scope() && ! array_filter( \RadmanCT\BlogArchive\values() ) && $wp_query->post_count > 0;
}

function lead(): void {
	global $wp_query;
	if ( ! available() || -1 !== $wp_query->current_post ) { return; }
	$count = min( 3, (int) $wp_query->post_count );
	if ( ! $count ) { return; }
	\RadmanCTChild\Blog\fragment_styles( array( 'src/css/pages/blog/lead.css' ) );
	echo '<section class="rdct-client-blog-lead-grid" data-count="' . (int) $count . '" aria-labelledby="rdct-blog-featured-title"><h2 id="rdct-blog-featured-title" class="screen-reader-text">' . esc_html__( 'Featured articles', 'radmanct-child' ) . '</h2>';
	foreach ( array_slice( $wp_query->posts, 0, $count ) as $index => $entry ) {
		get_template_part( 'template-parts/blog/archive/lead-card', null, array( 'primary' => 0 === $index, 'post_id' => $entry->ID ) );
	}
	echo '</section>';
}

function assets(): void {
	if ( ! available() ) { return; }
	\RadmanCT\Assets\style( 'rdct-client-blog-archive', 'src/css/pages/blog/lead.css', 'child', array( 'radmanct-child', 'rdct-listing' ) );
	global $wp_query;
	if ( $wp_query->post_count > 3 ) {
		\RadmanCTChild\Blog\card_assets();
		\RadmanCT\Assets\style( 'rdct-client-blog-section', 'src/css/pages/blog/section.css', 'child', array( 'radmanct-child' ) );
	}
}

function section(): void {
	global $wp_query;
	if ( ! available() || -1 !== $wp_query->current_post ) { return; }
	$count = min( 4, $wp_query->post_count - 3 );
	if ( $count < 1 ) { return; }
	$common = null; $terms = array();
	$posts = array_slice( $wp_query->posts, 3, $count );
	foreach ( $posts as $entry ) {
		$categories = get_the_category( $entry->ID );
		$ids = wp_list_pluck( $categories, 'term_id' );
		$common = null === $common ? $ids : array_intersect( $common, $ids );
		foreach ( $categories as $category ) { $terms[ $category->term_id ] = $category; }
	}
	$title = $common ? $terms[ reset( $common ) ]->name : __( 'Latest articles', 'radmanct-child' );
	\RadmanCTChild\Blog\fragment_styles( array( 'src/css/blog/cards.css', 'src/css/pages/blog/section.css' ) );
	get_template_part( 'template-parts/blog/archive/section', null, array( 'posts' => $posts, 'title' => $title ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 25 );
add_action( 'radmanct/blog/before_results', __NAMESPACE__ . '\\lead' );
add_action( 'radmanct/blog/before_grid', __NAMESPACE__ . '\\section' );
add_filter( 'radmanct/blog/show_controls', '__return_false' );
