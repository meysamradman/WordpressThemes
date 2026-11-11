<?php
defined( 'ABSPATH' ) || exit;

function rdct_portfolio_check( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}

global $wp_query, $wp_the_query;
$rdct_saved_query = $wp_query;
$rdct_saved_main = $wp_the_query;
$rdct_saved_get = $_GET;
try {
	$ids = array();
	for ( $rdct_test_page = 1; $rdct_test_page <= 3; $rdct_test_page++ ) {
		$wp_query = new WP_Query(); $wp_the_query = $wp_query;
		$wp_query->query( array( 'post_type' => 'radman_portfolio', 'paged' => $rdct_test_page ) );
		rdct_portfolio_check( 8 === (int) $wp_query->get( 'posts_per_page' ), 'Custom main archive page size' );
		// A request-local bounded query exercises pagination with the existing six fixtures.
		$wp_query->set( 'posts_per_page', 2 );
		$override = static function ( $query ) { if ( $query->is_main_query() ) { $query->set( 'posts_per_page', 2 ); } };
		add_action( 'pre_get_posts', $override, 99 );
		$wp_query->query( array( 'post_type' => 'radman_portfolio', 'paged' => $rdct_test_page ) );
		remove_action( 'pre_get_posts', $override, 99 );
		$_GET['_rdct_fragment'] = 'portfolio';
		ob_start(); \RadmanCT\Archives\view( 'portfolio', false ); $html = ob_get_clean();
		preg_match_all( '/<article id="post-(\d+)"/', $html, $matches );
		rdct_portfolio_check( 2 === count( $matches[1] ), 'Bounded page rendering' );
		rdct_portfolio_check( ! array_intersect( $ids, $matches[1] ), 'Non-overlapping pages' );
		$ids = array_merge( $ids, $matches[1] );
		rdct_portfolio_check( str_contains( $html, 'data-rdct-archive-items' ), 'Append target' );
		rdct_portfolio_check( ( $rdct_test_page < 3 ) === str_contains( $html, 'data-rdct-load-more' ), 'Next link or terminal page' );
		rdct_portfolio_check( ! str_contains( $html, '_rdct_fragment=' ), 'Clean pagination URLs' );
	}
	$terms = get_terms( array( 'taxonomy' => 'radman_portfolio_cat', 'hide_empty' => true, 'number' => 1 ) );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$wp_query = new WP_Query(); $wp_the_query = $wp_query;
		$wp_query->query( array( 'radman_portfolio_cat' => $terms[0]->slug ) );
		rdct_portfolio_check( 8 === (int) $wp_query->get( 'posts_per_page' ), 'Taxonomy archive uses custom page size' );
		foreach ( $wp_query->posts as $entry ) { rdct_portfolio_check( has_term( $terms[0]->term_id, 'radman_portfolio_cat', $entry ), 'Taxonomy constraint retained' ); }
	}
	$secondary = new WP_Query( array( 'post_type' => 'radman_portfolio', 'posts_per_page' => 1 ) );
	rdct_portfolio_check( 1 === (int) $secondary->get( 'posts_per_page' ), 'Secondary queries untouched' );
	echo "Portfolio archive contract passed\n";
} finally { $wp_query = $rdct_saved_query; $wp_the_query = $rdct_saved_main; $_GET = $rdct_saved_get; wp_reset_postdata(); }
