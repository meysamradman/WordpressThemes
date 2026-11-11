<?php

namespace RadmanCT\Templates;

defined( 'ABSPATH' ) || exit;

function content_pagination(): void {
	wp_link_pages( array( 'before' => '<nav class="rdct-pg" aria-label="' . esc_attr__( 'Content pages', 'radmanct' ) . '">', 'after' => '</nav>' ) );
}

function brand(): void {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf( '<a class="rdct-brand" href="%s" rel="home">%s</a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
}

function title(): void {
	if ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		echo esc_html( $posts_page ? get_the_title( $posts_page ) : __( 'Latest posts', 'radmanct' ) );
	} elseif ( is_search() ) {
		printf( esc_html__( 'Search results for: %s', 'radmanct' ), esc_html( get_search_query( false ) ) );
	} else {
		the_archive_title();
	}
}
