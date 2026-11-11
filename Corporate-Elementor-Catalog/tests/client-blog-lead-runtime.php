<?php

require getenv( 'RDCT_WP_ROOT' ) . '/wp-load.php';

$expect = static function ( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } };
$original_query = $GLOBALS['wp_query'];
$original_main = $GLOBALS['wp_the_query'];
$original_post = $GLOBALS['post'] ?? null;
$original_get = $_GET;
$settings = \RadmanCT\Settings\normalize( array( 'blog_archive_renderer' => 'custom' ), \RadmanCT\Settings\defaults() );
$filter = static fn( $pre, $option ) => 'radman_settings' === $option ? $settings : $pre;
add_filter( 'pre_option', $filter, 10, 2 );

try {
	foreach ( array( array( 0, 1, false ), array( 1, 1, false ), array( 2, 1, false ), array( 3, 1, false ), array( 7, 1, false ), array( 10, 1, false ), array( 7, 2, false ), array( 7, 1, true ) ) as [ $count, $test_page, $filtered ] ) {
		$query = new WP_Query();
		$query->is_home = true;
		$query->is_paged = $test_page > 1;
		$query->query_vars = $query->fill_query_vars( array( 'paged' => $test_page, 'posts_per_page' => 7, 'update_post_term_cache' => true, 'update_post_meta_cache' => true ) );
		$query->posts = array();
		for ( $index = 0; $index < $count; ++$index ) {
			$query->posts[] = new WP_Post( (object) array( 'ID' => 99000000 + $index, 'post_author' => 1, 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Article ' . $index, 'post_name' => 'article-' . $index, 'post_date' => '2026-10-01 10:00:00', 'post_date_gmt' => '2026-10-01 06:30:00' ) );
			wp_cache_set( 99000000 + $index, $query->posts[ $index ], 'posts' );
		}
		$query->post_count = $count;
		$query->found_posts = $count;
		$query->max_num_pages = $count ? 2 : 0;
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = $query;
		$_GET = $filtered ? array( 'rdct_search' => 'Article' ) : array();
		ob_start();
		\RadmanCT\Archives\view( 'blog' );
		$html = ob_get_clean();
		preg_match_all( '/<article id="post-(\d+)"/', $html, $matches );
		$expect( $count === count( $matches[1] ) && $count === count( array_unique( $matches[1] ) ), 'A post was lost or repeated.' );
		$featured = $test_page === 1 && ! $filtered ? min( 3, $count ) : 0;
		$expect( $featured === substr_count( $html, 'aria-labelledby="rdct-blog-lead-title-' ), 'Wrong featured count for ' . $count . ' posts on page ' . $test_page . ': ' . substr_count( $html, 'aria-labelledby="rdct-blog-lead-title-' ) );
		$expect( ! str_contains( $html, 'data-rdct-archive-form' ) && str_contains( $html, 'data-rdct-result-count class="screen-reader-text"' ), 'Removed controls returned or result announcements were lost.' );
		$expect( ( $featured && $count > 3 ? 1 : 0 ) === substr_count( $html, 'class="rdct-blog-section"' ), 'Section eligibility was lost.' );
		$journal_count = $count;
		preg_match_all( '/<article[^>]+class="[^"]*rdct-blog-journal-card/', $html, $journal );
		$expect( $journal_count === count( $journal[0] ), 'Journal repeated an editorial post or omitted remaining results.' );
		preg_match_all( '/<article\b[^>]*>(.*?)<\/article>/s', $html, $articles );
		foreach ( $articles[1] as $article ) { $expect( 1 === preg_match( '/<h4\b/', $article ) && ! preg_match( '/<h[123]\b/', $article ), 'A card title did not use H4.' ); }
		if ( $count ) { $expect( str_contains( $html, 'navigation pagination' ), 'Native pagination was removed.' ); }
		if ( $featured && $count > 3 ) { $expect( strpos( $html, 'rdct-client-blog-lead-grid' ) < strpos( $html, 'class="rdct-blog-section"' ), 'Section moved above the full-width lead.' ); }
	}
	echo "Passed: 0/1/2/3/7/10 posts, no duplicates or omissions, lead/section order, no duplicate controls and page-two/filter exclusion. No content or settings saved.\n";
} finally {
	for ( $index = 0; $index < 7; ++$index ) { wp_cache_delete( 99000000 + $index, 'posts' ); }
	$GLOBALS['wp_query'] = $original_query;
	$GLOBALS['wp_the_query'] = $original_main;
	$GLOBALS['post'] = $original_post;
	$_GET = $original_get;
	remove_filter( 'pre_option', $filter, 10 );
}
