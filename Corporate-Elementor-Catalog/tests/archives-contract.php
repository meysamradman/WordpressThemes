<?php

namespace RadmanCT\Renderers {
	function uses_elementor() { return $GLOBALS['pro'] ?? false; }
	function context() { return $GLOBALS['context'] ?? array( 'catalog', 'archive' ); }
}
namespace {
	define( 'ABSPATH', '/' );
	function add_action( ...$args ) {}
	function add_filter( ...$args ) {}
	function remove_filter( ...$args ) {}
	function is_admin() { return false; }
	function is_feed() { return false; }
	function is_preview() { return false; }
	function is_search() { return false; }
	function is_tax( $types = array() ) { return false; }
	function __( $value, $domain ) { return $value; }
	function sanitize_text_field( $value ) { return strip_tags( $value ); }
	function sanitize_title( $value ) { return strtolower( $value ); }
	function wp_unslash( $value ) { return $value; }
	function get_option( $key, $default ) { return 12; }
	function apply_filters( $hook, $value ) { return $GLOBALS['page_size'] ?? $value; }
	function nocache_headers() {}
	function wp_get_document_title() { return 'Archive &amp; test'; }
	function wp_specialchars_decode( $value, $flags ) { return htmlspecialchars_decode( $value, $flags ); }
	function wp_send_json_error( $data, $status ) { throw new \RuntimeException( (string) $status ); }
	function wp_send_json_success( $data ) { $GLOBALS['response'] = $data; throw new \RuntimeException( '200' ); }
	function get_template_part( $path ) { $GLOBALS['part'] = $path; echo '<p>Results only</p>'; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \LogicException( $message ); } }
	class WP_Query {
		public $main = true, $blog = true, $feed = false, $catalog = false, $taxonomy = false;
		public $values = array( 'tax_query' => array( array( 'taxonomy' => 'external' ) ) );
		function is_main_query() { return $this->main; }
		function is_feed() { return $this->feed; }
		function is_search() { return false; }
		function is_home() { return $this->blog; }
		function is_category() { return false; }
		function is_tag() { return false; }
		function is_author() { return false; }
		function is_date() { return false; }
		function is_post_type_archive( $type ) { return $this->catalog; }
		function is_tax( $types ) { return $this->taxonomy; }
		function get( $key ) { return $this->values[ $key ] ?? ''; }
		function set( $key, $value ) { $this->values[ $key ] = $value; }
	}
	require __DIR__ . '/../radmanct/inc/archives.php';
	require __DIR__ . '/../radmanct-content/inc/catalog.php';
	$_GET = array( 'rdct_search' => 'needle', 'rdct_category' => 'news', 'rdct_tag' => array( 'bad' ), 'rdct_sort' => 'title' );
	$query = new WP_Query(); \RadmanCT\BlogArchive\query( $query );
	expect( 'post' === $query->get( 'post_type' ) && 12 === $query->get( 'posts_per_page' ) && 'needle' === $query->get( 's' ), 'Blog search lost native page size or scope.' );
	expect( 'external' === $query->get( 'tax_query' )[0][0]['taxonomy'] && 3 === count( $query->get( 'tax_query' ) ), 'Blog filters lost an existing constraint or accepted an array.' );
	expect( true === $query->get( 'ignore_sticky_posts' ), 'Filtered blog can promote unrelated sticky posts.' );
	foreach ( array( 'blog', 'catalog' ) as $scope ) {
		$run = 'catalog' === $scope ? '\\RadmanCTContent\\Catalog\\query' : '\\RadmanCT\\BlogArchive\\query';
		$prefix = 'catalog' === $scope ? 'rm_' : 'rdct_';
		$_GET = array();
		$q = new WP_Query(); $q->catalog = 'catalog' === $scope; $q->blog = ! $q->catalog;
		$run( $q );
		expect( array( 'date' => 'DESC', 'ID' => 'DESC' ) === $q->get( 'orderby' ), 'Default pagination has no stable tie breaker.' );
		expect( '' === $q->get( 'ignore_sticky_posts' ), 'Unfiltered blog lost its native sticky policy.' );
		$q->values['orderby'] = array( 'menu_order' => 'ASC' ); $run( $q );
		expect( array( 'menu_order' => 'ASC' ) === $q->get( 'orderby' ), 'Default order replaced an existing custom order.' );
		$q->values['orderby'] = false; $run( $q );
		expect( false === $q->get( 'orderby' ), 'Explicitly disabled ordering was replaced.' );
		unset( $q->values['orderby'] ); $q->values['order'] = 'ASC'; $run( $q );
		expect( array( 'date' => 'ASC', 'ID' => 'ASC' ) === $q->get( 'orderby' ), 'Default tie breaker lost the configured direction.' );
		$_GET = array( $prefix . 'search' => 'needle' ); unset( $q->values['orderby'], $q->values['order'] ); $run( $q );
		expect( '' === $q->get( 'orderby' ), 'Default search must preserve native relevance ordering.' );
		foreach ( array( 'newest', 'oldest', 'title' ) as $sort ) {
			$_GET = array( $prefix . 'sort' => $sort ); $run( $q );
			$direction = 'newest' === $sort ? 'DESC' : 'ASC';
			expect( array( 'title' === $sort ? 'title' : 'date' => $direction, 'ID' => $direction ) === $q->get( 'orderby' ), 'Explicit sort lost direction or its tie breaker.' );
		}
		foreach ( array( 'main', 'feed' ) as $flag ) {
			$q->$flag = 'feed' === $flag; $before = $q->values; $run( $q );
			expect( $before === $q->values, 'Secondary query or feed was modified.' );
			$q->$flag = 'main' === $flag;
		}
	}
	$_GET = array( 'rdct_category' => 'news' ); $q = new WP_Query(); \RadmanCT\BlogArchive\query( $q );
	expect( true === $q->get( 'ignore_sticky_posts' ), 'Category-only filter still promotes sticky posts.' );
	$_GET = array( 'rm_brand' => 'industrial' ); $q = new WP_Query(); $q->catalog = true; $q->taxonomy = true;
	$q->values['tax_query'] = array( 'relation' => 'OR', array( 'taxonomy' => 'external-a' ), array( 'taxonomy' => 'external-b' ) );
	$page_size = 999; \RadmanCTContent\Catalog\query( $q );
	expect( 24 === $q->get( 'posts_per_page' ) && 'AND' === $q->get( 'tax_query' )['relation'] && 'OR' === $q->get( 'tax_query' )[0]['relation'], 'Catalog lost its page cap or nested taxonomy constraints.' );
	unset( $page_size );
	foreach ( array( 'main', 'blog' ) as $flag ) { $q = new WP_Query(); $q->$flag = false; $before = $q->values; \RadmanCT\BlogArchive\query( $q ); expect( $before === $q->values, 'Unrelated query was modified.' ); }
	$_GET = array( '_rdct_fragment' => 'catalog' );
	$_SERVER['REQUEST_METHOD'] = 'GET';
	try { \RadmanCT\Archives\fragment(); } catch ( \RuntimeException $e ) { expect( '200' === $e->getMessage(), 'Fragment failed.' ); }
	expect( '<p>Results only</p>' === $response['html'] && 'Archive & test' === $response['title'] && str_ends_with( $part, 'catalog-results' ), 'Fragment rendered more than the shared result template.' );
	$context = array( 'portfolio', 'archive' ); $_GET = array( '_rdct_fragment' => 'portfolio' );
	try { \RadmanCT\Archives\fragment(); } catch ( \RuntimeException $e ) { expect( '200' === $e->getMessage(), 'Portfolio fragment failed.' ); }
	expect( 'portfolio' === $response['scope'] && str_ends_with( $part, 'portfolio-results' ), 'Portfolio did not use its own cards and shared transport.' );
	foreach ( array( true, false ) as $pro ) {
		if ( ! $pro ) { $context = array( 'portfolio', 'single' ); }
		try { \RadmanCT\Archives\fragment(); throw new \LogicException( 'Excluded renderer accepted.' ); } catch ( \RuntimeException $e ) { expect( '409' === $e->getMessage(), 'Excluded renderer was not rejected.' ); }
	}
	echo "Passed: bounded blog query, existing constraints, renderer isolation and shared fragment-only response.\n";
}
