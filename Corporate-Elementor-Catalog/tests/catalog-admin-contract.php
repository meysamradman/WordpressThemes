<?php

namespace RadmanCTContent\Model {
	function read( $id ) { return array( 'code' => '<code>', 'pricing' => array( 'mode' => 'price', 'text' => '0 <script>' ) ); }
}
namespace RadmanCTContent\Modules {
	function enabled( $module ) { return true; }
	function types() { return array( 'radman_product', 'radman_portfolio' ); }
}
namespace {
	define( 'ABSPATH', '/' );
	function add_action( ...$args ) {}
	function add_filter( ...$args ) {}
	function __( $value, $domain ) { return $value; }
	function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
	function esc_html__( $value, $domain ) { return esc_html( $value ); }
	function sanitize_text_field( $value ) { return strip_tags( $value ); }
	function sanitize_title( $value ) { return strtolower( $value ); }
	function wp_unslash( $value ) { return $value; }
	function is_admin() { return true; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	class WP_Query {
		public $main = true;
		public $values = array( 'post_type' => 'radman_product', 'tax_query' => array( array( 'taxonomy' => 'external', 'terms' => array( 7 ) ) ) );
		function is_main_query() { return $this->main; }
		function get( $key ) { return $this->values[ $key ] ?? ''; }
		function set( $key, $value ) { $this->values[ $key ] = $value; }
	}
	require __DIR__ . '/../radmanct-content/inc/catalog.php';
	require __DIR__ . '/../radmanct-content/inc/admin/catalog-list.php';
	$pagenow = 'edit.php';
	$_GET = array( 'rm_brand' => 'example', 'rm_category' => 'tools', 'rm_feature' => array( 'invalid' ) );
	$query = new WP_Query();
	\RadmanCTContent\Admin\CatalogList\query( $query );
	$tax = $query->get( 'tax_query' );
	expect( 'AND' === $tax['relation'] && 'external' === $tax[0][0]['taxonomy'] && 4 === count( $tax ), 'Admin filters lost existing taxonomy constraints or accepted an array.' );
	foreach ( array( 'post', 'radman_portfolio' ) as $type ) {
		$query = new WP_Query(); $query->values['post_type'] = $type; $before = $query->values;
		\RadmanCTContent\Admin\CatalogList\query( $query );
		expect( $before === $query->values, 'Unrelated list was filtered.' );
	}
	$query = new WP_Query(); $query->main = false; $before = $query->values;
	\RadmanCTContent\Admin\CatalogList\query( $query );
	expect( $before === $query->values, 'Secondary query was filtered.' );
	$pagenow = 'post.php'; $query = new WP_Query(); $before = $query->values;
	\RadmanCTContent\Admin\CatalogList\query( $query );
	expect( $before === $query->values, 'Editor query was filtered.' );
	ob_start(); \RadmanCTContent\Admin\CatalogList\cell( 'rdct_code', 1 ); \RadmanCTContent\Admin\CatalogList\cell( 'rdct_pricing', 1 ); $html = ob_get_clean();
	expect( str_contains( $html, '0 &lt;script&gt;' ) && str_contains( $html, '&lt;code&gt;' ) && ! str_contains( $html, '<script>' ), 'Product list lost zero or escaped data.' );
	$columns = \RadmanCTContent\Admin\CatalogList\columns( array( 'cb' => 'Select', 'title' => 'Title', 'plugin' => 'Other plugin' ) );
	expect( isset( $columns['plugin'], $columns['rdct_image'], $columns['rdct_code'], $columns['rdct_pricing'] ), 'Product columns replaced another plugin column.' );
	$columns = \RadmanCTContent\Admin\ContentList\columns( array( 'cb' => 'Select', 'title' => 'Title', 'plugin' => 'Other plugin' ) );
	expect( isset( $columns['plugin'], $columns['rdct_image'] ) && ! isset( $columns['rdct_code'], $columns['rdct_pricing'] ), 'Project list lost shared columns or gained catalog pricing.' );
	ob_start(); \RadmanCTContent\Admin\ContentList\cell( 'rdct_code', 1 ); $html = ob_get_clean();
	expect( '&lt;code&gt;' === strip_tags( $html ), 'Shared project reference code was not escaped.' );
	echo "Passed: admin catalog filters preserve existing constraints, isolate main list and escape product fields.\n";
}
