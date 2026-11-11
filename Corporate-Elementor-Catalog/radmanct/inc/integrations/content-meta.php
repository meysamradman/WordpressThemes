<?php

namespace RadmanCT\Content\Meta;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-access.php';

function allowed( int $post_id ): bool {
	return \RadmanCT\Content\Access\allowed( $post_id );
}

function rows( int $post_id, bool $compact = false ): array {
	if ( ! allowed( $post_id ) ) { return array(); }
	$product = 'radman_product' === get_post_type( $post_id );
	$taxonomies = $product ? array( 'radman_product_cat' => __( 'Category', 'radmanct' ), 'radman_product_brand' => __( 'Brand', 'radmanct' ), 'radman_product_feature' => __( 'Feature', 'radmanct' ), 'radman_product_tag' => __( 'Tags', 'default' ) ) : array( 'radman_portfolio_cat' => __( 'Category', 'radmanct' ) );
	if ( $compact && $product ) { $taxonomies = array( 'radman_product_brand' => __( 'Brand', 'radmanct' ) ); }
	$rows = array();
	$code = $product ? \RadmanCT\Content\data( $post_id )['code'] : '';
	if ( '' !== $code ) { $rows[] = array( 'label' => __( 'Reference code', 'radmanct' ), 'html' => '<bdi>' . esc_html( $code ) . '</bdi>' ); }
	foreach ( $taxonomies as $taxonomy => $label ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! is_array( $terms ) ) { continue; }
		$links = array();
		foreach ( array_slice( $terms, 0, 20 ) as $term ) {
			$url = get_term_link( $term );
			$links[] = is_wp_error( $url ) ? esc_html( $term->name ) : '<a href="' . esc_url( $url ) . '">' . esc_html( $term->name ) . '</a>';
		}
		if ( $links ) { $rows[] = array( 'label' => $label, 'html' => implode( '<span aria-hidden="true"> / </span><span class="screen-reader-text">, </span>', $links ) ); }
	}
	return $rows;
}

function html( int $post_id, bool $compact = false ): string {
	$rows = rows( $post_id, $compact );
	if ( ! $rows ) { return ''; }
	$html = '<dl class="rdct-cnt-meta' . ( $compact ? ' rdct-cnt-meta--compact' : '' ) . '">';
	foreach ( $rows as $row ) {
		$html .= '<div><dt>' . esc_html( $row['label'] ) . '</dt><dd>' . $row['html'] . '</dd></div>';
	}
	return $html . '</dl>';
}

function render( int $post_id ): void {
	echo html( $post_id );
}

function archive( int $post_id ): string {
	return allowed( $post_id ) ? ( get_post_type_archive_link( get_post_type( $post_id ) ) ?: '' ) : '';
}
