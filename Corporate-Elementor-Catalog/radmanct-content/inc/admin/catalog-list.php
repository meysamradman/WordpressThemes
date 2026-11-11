<?php

namespace RadmanCTContent\Admin\CatalogList;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-list.php';

function columns( array $columns ): array {
	return \RadmanCTContent\Admin\ContentList\columns( $columns, true );
}

function cell( string $column, int $post_id ): void {
	if ( in_array( $column, array( 'rdct_image', 'rdct_code' ), true ) ) {
		\RadmanCTContent\Admin\ContentList\cell( $column, $post_id );
		return;
	}
	if ( 'rdct_pricing' !== $column ) { return; }
	$data = \RadmanCTContent\Model\read( $post_id );
	$pricing = $data['pricing'];
	if ( 'contact' === $pricing['mode'] ) { echo esc_html__( 'Contact us', 'radmanct-content' ); }
	else { echo '' !== $pricing['text'] ? '<bdi dir="auto">' . esc_html( $pricing['text'] ) . '</bdi>' : esc_html__( 'Price not entered', 'radmanct-content' ); }
}

function filters( string $post_type, string $which ): void {
	if ( 'radman_product' !== $post_type || 'top' !== $which ) { return; }
	foreach ( \RadmanCTContent\Catalog\FILTERS as $key => $taxonomy ) {
		$object = get_taxonomy( $taxonomy );
		if ( ! $object ) { continue; }
		$value = sanitize_title( \RadmanCTContent\Catalog\input( $key ) );
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => 100, 'orderby' => 'name' ) );
		$terms = is_wp_error( $terms ) ? array() : $terms;
		if ( '' !== $value && ! in_array( $value, wp_list_pluck( $terms, 'slug' ), true ) ) {
			$selected_term = get_term_by( 'slug', $value, $taxonomy );
			if ( $selected_term ) { $terms[] = $selected_term; }
		}
		$id = 'rdct-filter-' . $key;
		echo '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">' . esc_html( $object->labels->name ) . '</label><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '"><option value="">' . esc_html( sprintf( __( 'All: %s', 'radmanct-content' ), $object->labels->name ) ) . '</option>';
		foreach ( $terms as $term ) { echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( $value, $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>'; }
		echo '</select>';
	}
}

function query( \WP_Query $query ): void {
	global $pagenow;
	if ( ! is_admin() || 'edit.php' !== $pagenow || ! $query->is_main_query() || 'radman_product' !== $query->get( 'post_type' ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
	$clauses = array( 'relation' => 'AND' );
	$existing = $query->get( 'tax_query' );
	if ( is_array( $existing ) && $existing ) { $clauses[] = $existing; }
	foreach ( \RadmanCTContent\Catalog\FILTERS as $key => $taxonomy ) {
		$value = sanitize_title( \RadmanCTContent\Catalog\input( $key ) );
		if ( '' !== $value ) { $clauses[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => array( $value ) ); }
	}
	if ( count( $clauses ) > 1 ) { $query->set( 'tax_query', $clauses ); }
}

add_filter( 'manage_radman_product_posts_columns', __NAMESPACE__ . '\\columns' );
add_action( 'manage_radman_product_posts_custom_column', __NAMESPACE__ . '\\cell', 10, 2 );
add_action( 'restrict_manage_posts', __NAMESPACE__ . '\\filters', 10, 2 );
add_action( 'pre_get_posts', __NAMESPACE__ . '\\query' );
