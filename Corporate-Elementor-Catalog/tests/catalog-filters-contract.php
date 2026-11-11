<?php

define( 'ABSPATH', '/' );
$context = 'archive';
$language = 'fa';
$pretty = false;
$term_queries = 0;
$selected_queries = 0;
class WP_Query { public $found_posts = 23; }
$wp_query = new WP_Query();
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function add_action( ...$args ) {}
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function sanitize_title( $value ) { return strtolower( preg_replace( '/[^a-zA-Z0-9-]/', '', $value ) ); }
function __( $text, $domain ) { return $text; }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES ); }
function esc_html( $text ) { return esc_attr( $text ); }
function esc_url( $text ) { return esc_attr( $text ); }
function esc_attr__( $text, $domain ) { return esc_attr( $text ); }
function esc_html__( $text, $domain ) { return esc_html( $text ); }
function is_post_type_archive( $type ) { return 'archive' === $GLOBALS['context']; }
function is_tax( $types = null ) { return 'taxonomy' === $GLOBALS['context']; }
function get_queried_object() { return (object) array( 'taxonomy' => 'radman_product_cat', 'slug' => 'industrial' ); }
function get_term_link( $term ) { return 'https://local.test/?taxonomy=radman_product_cat&term=industrial&lang=fa'; }
function get_post_type_archive_link( $type ) { return 'https://local.test/?post_type=radman_product&lang=fa'; }
function get_option( $key ) { return $GLOBALS['pretty']; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function is_wp_error( $value ) { return false; }
function apply_filters( $hook, $value ) { return $GLOBALS['language']; }
function get_terms( $args ) { ++$GLOBALS['term_queries']; expect( 100 === $args['number'] && true === $args['hide_empty'], 'Term query became unbounded.' ); return array( (object) array( 'slug' => 'industrial', 'name' => '<Industrial>' ) ); }
function get_term_by( $field, $value, $taxonomy ) { ++$GLOBALS['selected_queries']; return (object) array( 'slug' => $value, 'name' => 'Selected extra' ); }
function wp_list_pluck( $items, $field ) { return array_map( static fn( $item ) => $item->$field, $items ); }
function selected( $value, $expected, $echo ) { return $value === $expected ? ' selected' : ''; }
function number_format_i18n( $number ) { return (string) $number; }
function add_query_arg( $args, $url ) { $base = strtok( $url, '?' ); parse_str( parse_url( $url, PHP_URL_QUERY ) ?: '', $query ); return $base . '?' . http_build_query( array_replace( $query, $args ) ); }
function remove_query_arg( $keys, $url ) { $base = strtok( $url, '?' ); parse_str( parse_url( $url, PHP_URL_QUERY ) ?: '', $query ); foreach ( (array) $keys as $key ) { unset( $query[ $key ] ); } return $base . ( $query ? '?' . http_build_query( $query ) : '' ); }
function get_pagenum_link( $page, $escape ) { return 'https://local.test/?page_id=42&lang=fa&paged=8&_rdct_fragment=blog&unrelated=secret'; }
require __DIR__ . '/../radmanct-content/inc/catalog.php';
require __DIR__ . '/../radmanct/inc/integrations/catalog-filters.php';
require __DIR__ . '/../radmanct/inc/blog-archive.php';
function output(): string { ob_start(); \RadmanCT\Content\CatalogFilters\render(); return ob_get_clean(); }

$_GET = array( 'rm_search' => '<b>Pump</b>', 'rm_category' => 'industrial', 'rm_brand' => 'special-brand', 'rm_sort' => 'title', 'paged' => '8', 'unrelated' => 'secret' );
$html = output();
expect( str_contains( $html, 'name="lang" value="fa"' ) && str_contains( $html, 'name="post_type" value="radman_product"' ), 'GET form lost language or plain archive routing.' );
expect( str_contains( $html, 'Results: 23' ) && str_contains( $html, 'Active filters' ) && ! str_contains( $html, '<Industrial>' ), 'Existing result count, active filters or escaping failed.' );
expect( ! str_contains( $html, 'paged=8' ) && ! str_contains( $html, 'secret' ), 'Removal URLs copied unrelated or stale paging arguments.' );
preg_match( '/href="([^"]+)" aria-label="Remove filter: Search products/', $html, $match );
parse_str( parse_url( html_entity_decode( $match[1] ), PHP_URL_QUERY ), $removed );
expect( ! isset( $removed['rm_search'] ) && 'special-brand' === $removed['rm_brand'] && 'title' === $removed['rm_sort'] && 'fa' === $removed['lang'], 'Removing one filter lost the remaining filters or language.' );
expect( 4 === $term_queries && 1 === $selected_queries, 'Unexpected filter term query count.' );
output();
expect( 4 === $term_queries && 1 === $selected_queries && str_contains( $html, 'name="rm_tag"' ), 'Repeated filter rendering repeated term queries or omitted product tags.' );
$wp_the_query = new WP_Query();
$wp_query->found_posts = 999;
expect( str_contains( output(), 'Results: 23' ), 'Secondary widget query replaced the main result count.' );
$language = 'en'; output();
expect( 8 === $term_queries && 2 === $selected_queries, 'Term memoization crossed language scopes.' );
$context = 'taxonomy';
$routing = \RadmanCT\Content\CatalogFilters\routing( 'https://local.test/?lang=fa' );
expect( 'radman_product_cat' === $routing['taxonomy'] && 'industrial' === $routing['term'] && 'fa' === $routing['lang'], 'Plain taxonomy form lost its route.' );
$pretty = true;
expect( array() === \RadmanCT\Content\CatalogFilters\routing( 'https://local.test/products/?lang[]=fa&post_type=wrong' ), 'Malformed routing query was retained.' );
$context = 'blog';
$before = $term_queries;
expect( '' === output() && $before === $term_queries, 'Catalog tools ran on an unrelated page.' );
$_GET = array( 'rdct_search' => '<b>News</b>', 'rdct_category' => 'industrial', 'rdct_tag' => 'extra-tag', 'rdct_sort' => 'title' );
ob_start(); \RadmanCT\BlogArchive\filters(); $html = ob_get_clean();
expect( str_contains( $html, 'name="page_id" value="42"' ) && str_contains( $html, 'name="lang" value="fa"' ), 'Blog form lost plain routing or language.' );
expect( 4 === substr_count( $html, 'aria-label="Remove filter:' ) && ! str_contains( $html, '<Industrial>' ) && str_contains( $html, 'Tag: Selected extra' ), 'Blog active filter labels or escaping failed.' );
expect( ! str_contains( $html, 'secret' ) && ! str_contains( $html, '_rdct_fragment' ) && ! str_contains( $html, 'paged=8' ), 'Blog links copied unrelated or stale request arguments.' );
preg_match( '/href="([^"]+)" aria-label="Remove filter: Search articles/', $html, $match );
parse_str( parse_url( html_entity_decode( $match[1] ), PHP_URL_QUERY ), $removed );
expect( ! isset( $removed['rdct_search'] ) && 'industrial' === $removed['rdct_category'] && 'extra-tag' === $removed['rdct_tag'] && 'title' === $removed['rdct_sort'] && '42' === $removed['page_id'] && 'fa' === $removed['lang'], 'Blog single-filter removal lost remaining filters or routing.' );
$before = array( $term_queries, $selected_queries );
ob_start(); \RadmanCT\BlogArchive\filters(); ob_end_clean();
expect( $before === array( $term_queries, $selected_queries ), 'Repeated blog rendering repeated term queries.' );
echo "Passed: blog/catalog active filter removal, escaping, bounded language-scoped term reuse, main result count, GET routing and isolation.\n";
