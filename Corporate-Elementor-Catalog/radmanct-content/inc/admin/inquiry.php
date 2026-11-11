<?php

namespace RadmanCTContent\Admin\Inquiry;

defined( 'ABSPATH' ) || exit;

function search(): string {
	$value = $_GET['inquiry_search'] ?? '';
	return is_string( $value ) && strlen( $value ) <= 320 ? sanitize_text_field( wp_unslash( $value ) ) : '';
}

function public_page( $post ): bool {
	return $post instanceof \WP_Post && 'page' === $post->post_type && 'publish' === $post->post_status && '' === $post->post_password;
}

function candidates( int $selected ): array {
	if ( ! \RadmanCTContent\Modules\enabled( 'catalog' ) ) { return array(); }
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => 20, 's' => search(), 'orderby' => array( 'title' => 'ASC', 'ID' => 'ASC' ), 'no_found_rows' => true, 'update_post_term_cache' => false, 'update_post_meta_cache' => false, 'suppress_filters' => false ) );
	$pages = array_values( array_filter( $pages, __NAMESPACE__ . '\\public_page' ) );
	if ( $selected && ! in_array( $selected, wp_list_pluck( $pages, 'ID' ), true ) ) {
		$post = get_post( $selected );
		if ( public_page( $post ) ) { $pages[] = $post; }
	}
	return $pages;
}

function search_form(): void {
	if ( ! \RadmanCTContent\Modules\enabled( 'catalog' ) ) { return; }
	echo '<form class="rdct-cor-page-search" method="get" action="' . esc_url( admin_url( 'admin.php' ) ) . '"><input type="hidden" name="page" value="radmanct-content"><label for="rdct-inquiry-search">' . esc_html__( 'Find a contact page', 'radmanct-content' ) . '</label><input type="search" id="rdct-inquiry-search" name="inquiry_search" maxlength="80" value="' . esc_attr( search() ) . '"><button type="submit" class="button">' . esc_html__( 'Search pages', 'radmanct-content' ) . '</button></form><p class="description">' . esc_html__( 'Save your changes before running another page search.', 'radmanct-content' ) . '</p>';
}

function field(): void {
	if ( ! \RadmanCTContent\Modules\enabled( 'catalog' ) ) { return; }
	$selected = (int) get_option( 'radman_core_inquiry_page', 0 );
	$pages = candidates( $selected );
	echo '<section class="rdct-cor-inquiry"><h2>' . esc_html__( 'Product contact link', 'radmanct-content' ) . '</h2><label for="rdct-inquiry-page">' . esc_html__( 'Contact page', 'radmanct-content' ) . '</label><select name="radman_core_inquiry_page" id="rdct-inquiry-page"><option value="0">' . esc_html__( 'No contact link', 'radmanct-content' ) . '</option>';
	foreach ( $pages as $post ) {
		echo '<option value="' . esc_attr( (string) $post->ID ) . '"' . selected( $selected, (int) $post->ID, false ) . '>' . esc_html( get_the_title( $post ) ?: __( 'Untitled page', 'radmanct-content' ) ) . '</option>';
	}
	echo '</select><p class="description">' . esc_html__( 'Shows up to 20 matching public pages and the saved selection. Use the page search above to find another destination.', 'radmanct-content' ) . '</p>';
	if ( ! $pages ) { echo '<p>' . esc_html__( 'No matching public pages. Search again or publish a contact page first.', 'radmanct-content' ) . '</p>'; }
	echo '<p class="description">' . esc_html__( 'Used when a product displays Contact us instead of a price. This does not create a form or submit requests.', 'radmanct-content' ) . '</p></section>';
}
