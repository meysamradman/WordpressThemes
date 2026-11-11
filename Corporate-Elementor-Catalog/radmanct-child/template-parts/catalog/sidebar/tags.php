<?php
defined( 'ABSPATH' ) || exit;
$rdct_tags = get_terms( array( 'taxonomy' => 'radman_product_tag', 'hide_empty' => true, 'number' => 30 ) );
if ( is_wp_error( $rdct_tags ) || ! $rdct_tags ) { return; }
$rdct_values = \RadmanCTChild\Catalog\Archive\values();

$rdct_current = 0;
foreach ( $rdct_tags as $rdct_tag ) { if ( $rdct_tag->slug === $rdct_values['rm_tag'] ) { $rdct_current = (int) $rdct_tag->term_id; } }
if ( ! function_exists( '\\RadmanCTComponents\\UI\\term_chips' ) ) { return; }
\RadmanCTComponents\UI\term_chips( $rdct_tags, array(
	'current' => $rdct_current,
	'class' => 'tagcloud rdct-catalog-tags',
	'url' => static function ( $term ) use ( $rdct_values ): string {
		return add_query_arg( array_filter( array_replace( $rdct_values, array( 'rm_tag' => $term->slug ) ), static fn( $value ) => '' !== $value ), get_post_type_archive_link( 'radman_product' ) );
	},
	'link_attributes' => static fn( $term ): array => array( 'data-rdct-archive-link' => '', 'data-rdct-tag' => $term->slug ),
) );
