<?php

namespace RadmanCT\Archives;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/blog-archive.php';

function scope(): string {
	if ( is_admin() || is_feed() || is_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || \RadmanCT\Renderers\uses_elementor() ) { return ''; }
	$context = \RadmanCT\Renderers\context();
	return isset( $context[1] ) && 'archive' === $context[1] && in_array( $context[0], array( 'blog', 'catalog', 'portfolio' ), true ) ? $context[0] : '';
}

function attributes( string $scope ): string {
	return 'data-rdct-archive="' . esc_attr( $scope ) . '" data-loading="' . esc_attr__( 'Loading results...', 'radmanct' ) . '" data-error="' . esc_attr__( 'Results could not be loaded. Your previous results are still available.', 'radmanct' ) . '"';
}

function status(): void {
	echo '<p class="rdct-archive-status" data-rdct-archive-status role="status" aria-live="polite"></p><button class="rdct-button" data-rdct-archive-retry type="button" hidden>' . esc_html__( 'Try again', 'radmanct' ) . '</button>';
}

function clean_page_link( string $url ): string { return remove_query_arg( '_rdct_fragment', $url ); }

function intro( string $scope ): void {
	if ( 'blog' === $scope && have_posts() ) { do_action( 'radmanct/blog/before_results' ); }
}

function view( string $scope, bool $include_intro = true ): void {
	if ( $include_intro ) { intro( $scope ); }
	add_filter( 'get_pagenum_link', __NAMESPACE__ . '\\clean_page_link' );
	try { get_template_part( 'template-parts/content/' . ( in_array( $scope, array( 'catalog', 'portfolio' ), true ) ? $scope : 'blog' ) . '-results' ); }
	finally { remove_filter( 'get_pagenum_link', __NAMESPACE__ . '\\clean_page_link' ); }
}

function filter_values( string $scope ): array {
	if ( 'catalog' === $scope && function_exists( '\\RadmanCTContent\\Catalog\\filters' ) ) {
		$values = \RadmanCTContent\Catalog\filters();
		if ( is_tax( array_values( \RadmanCTContent\Catalog\FILTERS ) ) ) {
			$term = get_queried_object();
			$key = array_search( $term->taxonomy, \RadmanCTContent\Catalog\FILTERS, true );
			if ( $key && '' === $values[ $key ] ) { $values[ $key ] = $term->slug; }
		}
		return $values;
	}
	$values = 'blog' === $scope ? \RadmanCT\BlogArchive\values() : array();
	if ( 'blog' === $scope && is_search() ) { $values['rdct_search'] = get_search_query( false ); }
	return $values;
}

function fragment(): void {
	$requested = $_GET['_rdct_fragment'] ?? null;
	if ( null === $requested ) { return; }
	if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { wp_send_json_error( array( 'message' => __( 'This archive is not available.', 'radmanct' ) ), 405 ); }
	if ( ! is_string( $requested ) || ! in_array( $requested, array( 'blog', 'catalog', 'portfolio' ), true ) || $requested !== scope() ) {
		wp_send_json_error( array( 'message' => __( 'This archive is not available.', 'radmanct' ) ), 409 );
	}
	nocache_headers();
	ob_start(); get_template_part( 'template-parts/content/archive-header', null, array( 'scope' => $requested ) ); $header = ob_get_clean();
	ob_start(); intro( $requested ); $intro = ob_get_clean();
	ob_start(); view( $requested, false ); $html = ob_get_clean();
	ob_start(); \RadmanCT\PageTitle\render(); $page_title = ob_get_clean();
	wp_send_json_success( array( 'scope' => $requested, 'filters' => filter_values( $requested ), 'header' => $header, 'intro' => $intro, 'html' => $html, 'pageTitle' => $page_title, 'title' => wp_specialchars_decode( wp_get_document_title(), ENT_QUOTES ) ) );
}

add_action( 'template_redirect', __NAMESPACE__ . '\\fragment', 20 );
