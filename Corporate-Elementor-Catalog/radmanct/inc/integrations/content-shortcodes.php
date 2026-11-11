<?php

namespace RadmanCT\Content\Shortcodes;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-access.php';

function style( string $handle, string $source ): void {
	\RadmanCT\Assets\style( $handle, $source, 'parent', array( 'rdct-theme' ) );
	if ( did_action( 'wp_head' ) ) {
		wp_print_styles( array( $handle ) );
	}
}

function source( bool $product = false ): int {
	$id = get_queried_object_id();
	$types = $product ? array( 'radman_product' ) : array( 'radman_product', 'radman_portfolio' );
	return is_singular( $types ) && \RadmanCT\Content\Access\allowed( (int) $id, $product ) ? (int) $id : 0;
}

function gallery(): string {
	$id = source();
	if ( ! $id || ! \RadmanCT\Content\gallery_ids( $id ) ) {
		return '';
	}
	ob_start();
	style( 'rdct-gallery', 'src/css/gallery.css' );
	\RadmanCT\Content\gallery( $id );
	return ob_get_clean();
}

function specifications(): string {
	$id = source( true );
	if ( ! $id || ! \RadmanCT\Content\data( $id )['specifications'] ) {
		return '';
	}
	ob_start();
	if ( ! wp_style_is( 'rdct-catalog-single', 'enqueued' ) ) {
		style( 'rdct-specifications', 'src/css/specifications.css' );
	}
	\RadmanCT\Content\specifications( $id );
	return ob_get_clean();
}

function filters(): string {
	if ( ! is_post_type_archive( 'radman_product' ) && ! is_tax( array_values( \RadmanCTContent\Catalog\FILTERS ) ) ) {
		return '';
	}
	ob_start();
	if ( ! wp_style_is( 'rdct-catalog', 'enqueued' ) ) {
		style( 'rdct-catalog-filters', 'src/css/catalog-filters.css' );
	}
	\RadmanCT\Content\filters();
	return ob_get_clean();
}

function inquiry(): string {
	return product_display();
}

function product_display(): string {
	$id = source( true );
	if ( ! $id ) { return ''; }
	require_once __DIR__ . '/product-display.php';
	$html = \RadmanCT\Content\ProductDisplay\html( $id );
	if ( '' === $html ) { return ''; }
	ob_start();
	if ( ! wp_style_is( 'rdct-catalog-single', 'enqueued' ) ) { style( 'rdct-product-display', 'src/css/product-display.css' ); }
	echo $html;
	return ob_get_clean();
}

function metadata( $attributes = array() ): string {
	$attributes = is_array( $attributes ) ? $attributes : array();
	$loop = 'loop' === ( $attributes['context'] ?? '' );
	$compact = 'yes' === ( $attributes['compact'] ?? '' );
	$id = $loop ? (int) get_the_ID() : source();
	if ( ! $id ) { return ''; }
	require_once __DIR__ . '/content-meta.php';
	$html = \RadmanCT\Content\Meta\html( $id, $compact );
	if ( '' === $html ) { return ''; }
	ob_start();
	if ( ! wp_style_is( 'rdct-catalog-single', 'enqueued' ) && ! wp_style_is( 'rdct-portfolio-single', 'enqueued' ) ) {
		style( 'rdct-content-meta', 'src/css/content-meta.css' );
	}
	echo $html;
	return ob_get_clean();
}

add_shortcode( 'radman_content_gallery', __NAMESPACE__ . '\\gallery' );
add_shortcode( 'radman_content_meta', __NAMESPACE__ . '\\metadata' );
if ( function_exists( '\\RadmanCTContent\\Modules\\enabled' ) && \RadmanCTContent\Modules\enabled( 'catalog' ) ) {
	add_shortcode( 'radman_catalog_filters', __NAMESPACE__ . '\\filters' );
	add_shortcode( 'radman_product_specs', __NAMESPACE__ . '\\specifications' );
	add_shortcode( 'radman_product_inquiry', __NAMESPACE__ . '\\inquiry' );
	add_shortcode( 'rdct_product_display', __NAMESPACE__ . '\\product_display' );
}
