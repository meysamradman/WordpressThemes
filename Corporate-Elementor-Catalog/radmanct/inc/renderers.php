<?php

namespace RadmanCT\Renderers;

defined( 'ABSPATH' ) || exit;

function context(): array {
	foreach ( array( 'post' => 'blog', 'radman_product' => 'catalog', 'radman_portfolio' => 'portfolio' ) as $type => $scope ) {
		if ( is_singular( $type ) ) {
			return array( $scope, 'single' );
		}
	}
	if ( is_post_type_archive( 'radman_product' ) || is_tax( array( 'radman_product_cat', 'radman_product_brand', 'radman_product_feature', 'radman_product_tag' ) ) ) {
		return array( 'catalog', 'archive' );
	}
	if ( is_post_type_archive( 'radman_portfolio' ) || is_tax( 'radman_portfolio_cat' ) ) {
		return array( 'portfolio', 'archive' );
	}
	if ( is_search() ) {
		$type = get_query_var( 'post_type' );
		if ( 'radman_product' === $type && post_type_exists( $type ) ) { return array( 'catalog', 'archive' ); }
		if ( ! $type || 'post' === $type ) { return array( 'blog', 'archive' ); }
	}
	if ( is_home() || is_category() || is_tag() || is_author() || is_date() ) {
		return array( 'blog', 'archive' );
	}
	return array();
}

function selected( string $location ): bool {
	$context = context();
	if ( ! $context || $location !== $context[1] ) {
		return false;
	}
	return 'elementor' === \RadmanCT\Settings\values()[ $context[0] . '_' . $context[1] . '_renderer' ];
}

function uses_elementor(): bool {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! function_exists( 'elementor_location_exits' ) ) {
		return false;
	}
	if ( is_singular() && post_password_required() ) {
		return false;
	}
	$context = context();
	return $context && selected( $context[1] ) && elementor_location_exits( $context[1], true );
}

function render(): bool {
	if ( ! uses_elementor() || ( is_singular() && post_password_required() ) ) {
		return false;
	}
	return (bool) elementor_theme_do_location( context()[1] );
}
