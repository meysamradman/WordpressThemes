<?php

namespace RadmanCT\Content\CatalogFilters;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/archive-filters.php';
require_once dirname( __DIR__ ) . '/ui/fields.php';

function prepare_fields( array $keys ): void {
	if ( ! $keys ) { return; }
	$values = \RadmanCTContent\Catalog\filters();
	$combobox = false;
	foreach ( $keys as $key ) {
		$taxonomy = \RadmanCTContent\Catalog\FILTERS[ $key ] ?? '';
		if ( ! $taxonomy ) { continue; }
		$terms = \RadmanCT\ArchiveFilters\terms( $taxonomy, $values[ $key ] );
		$options = array_map( static fn( $term ): array => array( $term->slug, $term->name ), $terms );
		$combobox = $combobox || \RadmanCT\UI\searchable_options( $options );
	}
	\RadmanCT\UI\field_assets( true, $combobox );
}

function action(): string {
	if ( is_tax() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) { return $link; }
	}
	return (string) get_post_type_archive_link( 'radman_product' );
}

function routing( string $action ): array {
	$result = array();
	$query = wp_parse_url( $action, PHP_URL_QUERY );
	$arguments = array();
	if ( is_string( $query ) ) { parse_str( $query, $arguments ); }
	$lang = $arguments['lang'] ?? '';
	if ( is_string( $lang ) && preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,19}$/D', $lang ) ) { $result['lang'] = $lang; }
	if ( ! get_option( 'permalink_structure' ) ) {
		if ( is_tax() ) {
			$term = get_queried_object();
			$result += array( 'taxonomy' => $term->taxonomy, 'term' => $term->slug );
		} else { $result['post_type'] = 'radman_product'; }
	}
	return $result;
}

function render(): void {
	if ( ! function_exists( '\\RadmanCTContent\\Catalog\\filters' ) || array( 'catalog', 'archive' ) !== \RadmanCT\Renderers\context() ) { return; }
	$values = \RadmanCTContent\Catalog\filters();
	$action = action();
	$labels = array( 'rm_category' => __( 'Category', 'radmanct' ), 'rm_brand' => __( 'Brand', 'radmanct' ), 'rm_feature' => __( 'Feature', 'radmanct' ), 'rm_tag' => __( 'Tags', 'default' ) );
	$sorts = \RadmanCT\ArchiveFilters\sorts();
	$active = array();
	if ( '' !== $values['rm_search'] ) { $active['rm_search'] = __( 'Search products', 'radmanct' ) . ': ' . $values['rm_search']; }
	echo '<section class="rdct-cat-tools" aria-label="' . esc_attr__( 'Catalog filters', 'radmanct' ) . '"><form class="rdct-cat-filter rdct-form" data-rdct-archive-form role="search" method="get" action="' . esc_url( $action ) . '">';
	foreach ( routing( $action ) as $name => $value ) {
		echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
	}
	\RadmanCT\UI\input_field( 'rm_search', __( 'Search products', 'radmanct' ), $values['rm_search'], array( 'type' => 'search', 'maxlength' => 80 ) );
	foreach ( $labels as $key => $label ) {
		$terms = \RadmanCT\ArchiveFilters\terms( \RadmanCTContent\Catalog\FILTERS[ $key ], $values[ $key ] );
		$options = array( array( '', __( 'All', 'radmanct' ) ) );
		foreach ( $terms as $term ) {
			$options[] = array( $term->slug, $term->name );
			if ( $term->slug === $values[ $key ] ) { $active[ $key ] = $label . ': ' . $term->name; }
		}
		if ( '' !== $values[ $key ] && ! isset( $active[ $key ] ) ) { $active[ $key ] = $label . ': ' . $values[ $key ]; }
		\RadmanCT\UI\select_field( $key, $label, $options, $values[ $key ], array( 'searchable' => 'auto' ) );
	}
	\RadmanCT\UI\select_field( 'rm_sort', __( 'Sort by', 'radmanct' ), array_map( static fn( $key, $label ): array => array( $key, $label ), array_keys( $sorts ), $sorts ), $values['rm_sort'] );
	if ( $values['rm_sort'] ) { $active['rm_sort'] = __( 'Sort by', 'radmanct' ) . ': ' . $sorts[ $values['rm_sort'] ]; }
	echo '<button type="submit" class="rdct-button rdct-form-submit">' . esc_html__( 'Apply filters', 'radmanct' ) . '</button><a data-rdct-archive-link href="' . esc_url( $action ) . '">' . esc_html__( 'Clear filters', 'radmanct' ) . '</a></form>';
	global $wp_query, $wp_the_query;
	$query = $wp_the_query instanceof \WP_Query ? $wp_the_query : $wp_query;
	if ( $query instanceof \WP_Query ) {
		echo '<p class="rdct-cat-results" data-rdct-result-count>' . sprintf( esc_html__( 'Results: %s', 'radmanct' ), esc_html( number_format_i18n( max( 0, (int) $query->found_posts ) ) ) ) . '</p>';
	}
	\RadmanCT\ArchiveFilters\active( $active, $values, $action );
	echo '</section>';
}
