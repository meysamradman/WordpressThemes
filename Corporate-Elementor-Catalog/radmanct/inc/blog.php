<?php

namespace RadmanCT\Blog;

defined( 'ABSPATH' ) || exit;

function categories( int $post_id ): string {
	$terms = get_the_terms( $post_id, 'category' );
	if ( ! is_array( $terms ) ) { return ''; }
	$links = array();
	foreach ( array_slice( $terms, 0, 3 ) as $term ) {
		$url = get_term_link( $term );
		$links[] = is_wp_error( $url ) ? '<span>' . esc_html( $term->name ) . '</span>' : '<a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
	}
	return implode( ' ', $links );
}

function archive_url(): string {
	$page = (int) get_option( 'page_for_posts', 0 );
	if ( $page ) {
		$page = (int) apply_filters( 'wpml_object_id', $page, 'page', false );
		return $page ? ( get_permalink( $page ) ?: '' ) : '';
	}
	return 'posts' === get_option( 'show_on_front', 'posts' ) ? home_url( '/' ) : '';
}
