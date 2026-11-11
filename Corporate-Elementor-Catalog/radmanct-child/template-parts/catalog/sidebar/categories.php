<?php

defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/archive-filters.php';
require_once get_template_directory() . '/inc/ui/category-tree.php';
$rdct_values = \RadmanCTChild\Catalog\Archive\values();
$rdct_terms = get_terms( array( 'taxonomy' => 'radman_product_cat', 'hide_empty' => true, 'number' => 200, 'pad_counts' => true ) );
if ( is_wp_error( $rdct_terms ) || ! $rdct_terms ) { return; }
$rdct_current = get_term_by( 'slug', $rdct_values['rm_category'], 'radman_product_cat' );
\RadmanCT\UI\category_tree( $rdct_terms, array(
	'current' => $rdct_current ? (int) $rdct_current->term_id : 0,
	'ancestors' => $rdct_current ? get_ancestors( $rdct_current->term_id, 'radman_product_cat', 'taxonomy' ) : array(),
	'url' => static function ( $term ) use ( $rdct_values ): string {
		$values = array_filter( array_replace( $rdct_values, array( 'rm_category' => $term->slug ) ), static fn( $value ) => '' !== $value );
		return add_query_arg( $values, get_post_type_archive_link( 'radman_product' ) );
	},
	'link_attributes' => static fn( $term ): array => array( 'data-rdct-archive-link' => '', 'data-rdct-category' => $term->slug ),
) );
