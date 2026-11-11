<?php

namespace RadmanCTContent\Catalog;

defined( 'ABSPATH' ) || exit;

const FILTERS = array( 'rm_category' => 'radman_product_cat', 'rm_brand' => 'radman_product_brand', 'rm_feature' => 'radman_product_feature', 'rm_tag' => 'radman_product_tag' );

function input( string $key, int $limit = 200 ): string {
	$value = $_GET[ $key ] ?? '';
	return is_string( $value ) && strlen( $value ) <= $limit ? sanitize_text_field( wp_unslash( $value ) ) : '';
}

function filters(): array {
	$values = array( 'rm_search' => input( 'rm_search', 320 ), 'rm_sort' => input( 'rm_sort', 16 ) );
	if ( ! isset( $_GET['rm_search'] ) && 'radman_product' === ( $_GET['post_type'] ?? '' ) ) { $values['rm_search'] = input( 's', 320 ); }
	foreach ( FILTERS as $key => $taxonomy ) {
		$values[ $key ] = sanitize_title( input( $key ) );
	}
	if ( ! in_array( $values['rm_sort'], array( '', 'newest', 'oldest', 'title' ), true ) ) {
		$values['rm_sort'] = '';
	}
	return $values;
}

function query( \WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || $query->is_feed() || ( ! $query->is_post_type_archive( 'radman_product' ) && ! $query->is_tax( array_values( FILTERS ) ) && ! ( $query->is_search() && 'radman_product' === $query->get( 'post_type' ) ) ) ) {
		return;
	}
	$values = filters();
	$query->set( 'post_type', 'radman_product' );
	$query->set( 'posts_per_page', min( 24, max( 1, (int) apply_filters( 'radman_core/catalog/page_size', 12 ) ) ) );
	if ( '' !== $values['rm_search'] ) {
		$query->set( 's', $values['rm_search'] );
	}
	if ( $values['rm_sort'] ) {
		$query->set( 'orderby', 'title' === $values['rm_sort'] ? array( 'title' => 'ASC', 'ID' => 'ASC' ) : array( 'date' => 'oldest' === $values['rm_sort'] ? 'ASC' : 'DESC', 'ID' => 'oldest' === $values['rm_sort'] ? 'ASC' : 'DESC' ) );
	} elseif ( '' === $query->get( 'orderby' ) && '' === (string) $query->get( 's' ) ) {
		$order = 'ASC' === strtoupper( (string) $query->get( 'order' ) ) ? 'ASC' : 'DESC';
		$orderby = array( 'date' => $order, 'ID' => $order );
		$query->set( 'orderby', $orderby );
	}
	$clauses = array( 'relation' => 'AND' );
	$existing = $query->get( 'tax_query' );
	if ( is_array( $existing ) && $existing ) {
		$clauses[] = $existing;
	}
	foreach ( FILTERS as $key => $taxonomy ) {
		if ( '' !== $values[ $key ] ) {
			$clauses[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => array( $values[ $key ] ) );
		}
	}
	if ( count( $clauses ) > 1 ) {
		$query->set( 'tax_query', $clauses );
	}
}

function contact_url(): string {
	$page = (int) get_option( 'radman_core_inquiry_page', 0 );
	if ( ! $page ) {
		return '';
	}
	$page = (int) apply_filters( 'wpml_object_id', $page, 'page', false );
	$post = $page ? get_post( $page ) : null;
	if ( ! $post || 'page' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password ) {
		return '';
	}
	return get_permalink( $page ) ?: '';
}

function inquiry_url( int $product_id ): string {
	if ( 'radman_product' !== get_post_type( $product_id ) || 'publish' !== get_post_status( $product_id ) ) { return ''; }
	return contact_url();
}

add_action( 'pre_get_posts', __NAMESPACE__ . '\\query' );
