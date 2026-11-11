<?php

namespace RadmanCT\BlogArchive;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/archive-filters.php';
require_once __DIR__ . '/ui/fields.php';
const TAXONOMIES = array( 'rdct_category' => 'category', 'rdct_tag' => 'post_tag' );

function input( string $key, int $limit = 200 ): string {
	$value = $_GET[ $key ] ?? '';
	return is_string( $value ) && strlen( $value ) <= $limit ? sanitize_text_field( wp_unslash( $value ) ) : '';
}

function values(): array {
	$values = array( 'rdct_search' => input( 'rdct_search', 320 ), 'rdct_sort' => input( 'rdct_sort', 16 ) );
	if ( ! isset( $_GET['rdct_search'] ) && 'post' === ( $_GET['post_type'] ?? '' ) ) { $values['rdct_search'] = input( 's', 320 ); }
	foreach ( TAXONOMIES as $key => $taxonomy ) { $values[ $key ] = sanitize_title( input( $key ) ); }
	if ( ! in_array( $values['rdct_sort'], array( '', 'newest', 'oldest', 'title' ), true ) ) { $values['rdct_sort'] = ''; }
	return $values;
}

function query( \WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || $query->is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) ) { return; }
	$values = values();
	$query->set( 'post_type', 'post' );
	$query->set( 'posts_per_page', min( 48, max( 1, (int) ( $query->get( 'posts_per_page' ) ?: get_option( 'posts_per_page', 10 ) ) ) ) );
	if ( '' !== $values['rdct_search'] ) { $query->set( 's', $values['rdct_search'] ); }
	if ( $values['rdct_sort'] ) {
		$order = 'oldest' === $values['rdct_sort'] ? 'ASC' : 'DESC';
		$query->set( 'orderby', 'title' === $values['rdct_sort'] ? array( 'title' => 'ASC', 'ID' => 'ASC' ) : array( 'date' => $order, 'ID' => $order ) );
	} elseif ( '' === $query->get( 'orderby' ) && '' === (string) $query->get( 's' ) ) {
		$order = 'ASC' === strtoupper( (string) $query->get( 'order' ) ) ? 'ASC' : 'DESC';
		$orderby = array( 'date' => $order, 'ID' => $order );
		$query->set( 'orderby', $orderby );
	}
	if ( array_filter( $values ) ) { $query->set( 'ignore_sticky_posts', true ); }
	$clauses = array( 'relation' => 'AND' );
	$existing = $query->get( 'tax_query' );
	if ( is_array( $existing ) && $existing ) { $clauses[] = $existing; }
	foreach ( TAXONOMIES as $key => $taxonomy ) {
		if ( '' !== $values[ $key ] ) { $clauses[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => array( $values[ $key ] ) ); }
	}
	if ( count( $clauses ) > 1 ) { $query->set( 'tax_query', $clauses ); }
}

function filters(): void {
	$values = values();
	$active = array();
	$sorts = \RadmanCT\ArchiveFilters\sorts();
	if ( '' !== $values['rdct_search'] ) { $active['rdct_search'] = __( 'Search articles', 'radmanct' ) . ': ' . $values['rdct_search']; }
	$action = remove_query_arg( array_merge( array_keys( $values ), array( 'paged', 'page', '_rdct_fragment' ) ), get_pagenum_link( 1, false ) );
	$query = wp_parse_url( $action, PHP_URL_QUERY ); $routing = array();
	if ( is_string( $query ) ) { parse_str( $query, $routing ); }
	$route_keys = array( 'page_id', 'p', 'cat', 'category_name', 'tag', 'author', 'author_name', 'year', 'monthnum', 'day', 'm', 'post_type', 'lang' );
	$discard = array_diff( array_keys( $routing ), $route_keys );
	foreach ( $routing as $key => $value ) { if ( ! is_scalar( $value ) ) { $discard[] = $key; } }
	if ( $discard ) { $action = remove_query_arg( $discard, $action ); }
	echo '<form class="rdct-cat-filter rdct-form" data-rdct-archive-form role="search" method="get" action="' . esc_url( $action ) . '">';
	foreach ( $route_keys as $key ) {
		if ( isset( $routing[ $key ] ) && is_scalar( $routing[ $key ] ) ) { echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $routing[ $key ] ) . '">'; }
	}
	\RadmanCT\UI\input_field( 'rdct_search', __( 'Search articles', 'radmanct' ), $values['rdct_search'], array( 'type' => 'search', 'maxlength' => 80 ) );
	foreach ( TAXONOMIES as $key => $taxonomy ) {
		$terms = \RadmanCT\ArchiveFilters\terms( $taxonomy, $values[ $key ] );
		$label = 'category' === $taxonomy ? __( 'Category', 'radmanct' ) : __( 'Tag', 'radmanct' );
		$options = array( array( '', __( 'All', 'radmanct' ) ) );
		foreach ( $terms as $term ) {
			$options[] = array( $term->slug, $term->name );
			if ( '' !== $values[ $key ] && $term->slug === $values[ $key ] ) { $active[ $key ] = $label . ': ' . $term->name; }
		}
		if ( '' !== $values[ $key ] && ! isset( $active[ $key ] ) ) { $active[ $key ] = $label . ': ' . $values[ $key ]; }
		\RadmanCT\UI\select_field( $key, $label, $options, $values[ $key ], array( 'searchable' => 'auto' ) );
	}
	\RadmanCT\UI\select_field( 'rdct_sort', __( 'Sort by', 'radmanct' ), array_map( static fn( $key, $label ): array => array( $key, $label ), array_keys( $sorts ), $sorts ), $values['rdct_sort'] );
	echo '<button class="rdct-button rdct-form-submit" type="submit">' . esc_html__( 'Apply filters', 'radmanct' ) . '</button><a data-rdct-archive-link href="' . esc_url( $action ) . '">' . esc_html__( 'Clear filters', 'radmanct' ) . '</a></form>';
	if ( $values['rdct_sort'] ) { $active['rdct_sort'] = __( 'Sort by', 'radmanct' ) . ': ' . $sorts[ $values['rdct_sort'] ]; }
	\RadmanCT\ArchiveFilters\active( $active, $values, $action );
}

add_action( 'pre_get_posts', __NAMESPACE__ . '\\query' );

function assets(): void {
	if ( 'blog' !== \RadmanCT\Archives\scope() || ! apply_filters( 'radmanct/blog/show_controls', true ) ) { return; }
	$values = values();
	$combobox = false;
	foreach ( TAXONOMIES as $key => $taxonomy ) {
		$terms = \RadmanCT\ArchiveFilters\terms( $taxonomy, $values[ $key ] );
		$options = array_map( static fn( $term ): array => array( $term->slug, $term->name ), $terms );
		$combobox = $combobox || \RadmanCT\UI\searchable_options( $options );
	}
	\RadmanCT\UI\field_assets( true, $combobox );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 21 );
