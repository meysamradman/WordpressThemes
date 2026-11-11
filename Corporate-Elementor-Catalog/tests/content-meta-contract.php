<?php

namespace RadmanCTContent\Modules {
	function enabled( $module ) { return $GLOBALS['modules'][ $module ] ?? false; }
}
namespace RadmanCT\Content {
	function data( $post_id ) { ++$GLOBALS['data_reads']; return array( 'code' => 'R-123 <script>', 'gallery' => array(), 'specifications' => array(), 'pricing' => array( 'mode' => $GLOBALS['pricing_mode'] ?? 'price', 'text' => '0 <price>' ) ); }
}
namespace RadmanCTContent\Catalog {
	function contact_url() { return '/contact/'; }
}
namespace RadmanCT\Assets {
	function style( $handle, $source, $scope, $dependencies ) { $GLOBALS['styles'][] = $handle; return true; }
}
namespace {
	define( 'ABSPATH', '/' );
	$modules = array( 'catalog' => true, 'portfolio' => true );
	$data_reads = $term_reads = 0;
	$source_id = 1;
	$styles = array();
	function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	function get_post_type( $id ) { return array( 1 => 'radman_product', 2 => 'radman_portfolio', 3 => 'radman_product', 4 => 'radman_product', 5 => 'post' )[ $id ] ?? false; }
	function get_post_status( $id ) { return 3 === $id ? 'draft' : 'publish'; }
	function post_password_required( $id ) { return 4 === $id; }
	function __( $value, $domain ) { return $value; }
	function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
	function esc_html__( $value, $domain ) { return esc_html( $value ); }
	function esc_url( $value ) { return esc_html( $value ); }
	function get_the_terms( $id, $taxonomy ) { ++$GLOBALS['term_reads']; return array_map( static fn( $n ) => (object) array( 'term_id' => $n, 'taxonomy' => $taxonomy, 'name' => 'Term <' . $n . '>' ), range( 1, 30 ) ); }
	function get_term_link( $term ) { return 1 === $term->term_id ? false : '/terms/' . $term->term_id; }
	function is_wp_error( $value ) { return false === $value; }
	function get_post_type_archive_link( $type ) { return '/archive/' . $type . '/?lang=fa'; }
	function get_queried_object_id() { return $GLOBALS['source_id']; }
	function get_the_ID() { return $GLOBALS['loop_id'] ?? 0; }
	function is_preview() { return $GLOBALS['preview'] ?? false; }
	function current_user_can( $capability, $id ) { return ( $GLOBALS['editable_id'] ?? 0 ) === $id; }
	function is_singular( $types ) { return in_array( get_post_type( $GLOBALS['source_id'] ), (array) $types, true ); }
	function wp_style_is( ...$args ) { return false; }
	function did_action( $hook ) { return false; }
	function add_shortcode( ...$args ) {}
	require __DIR__ . '/../radmanct/inc/integrations/content-meta.php';
	require __DIR__ . '/../radmanct/inc/integrations/content-shortcodes.php';
	$html = \RadmanCT\Content\Meta\html( 1 );
	expect( 4 === $term_reads && str_contains( $html, '<dt>Tags</dt>' ) && str_contains( $html, '<bdi>R-123 &lt;script&gt;</bdi>' ) && ! str_contains( $html, 'Term <' ), 'Product metadata escaping or taxonomy scope failed.' );
	expect( 76 === substr_count( $html, '<a href=' ) && ! str_contains( $html, '/terms/21' ), 'Term output was not bounded or failed term links hid text.' );
	expect( str_contains( \RadmanCT\Content\Meta\archive( 1 ), 'lang=fa' ), 'Native archive language route was lost.' );
	$before = $term_reads;
	$html = \RadmanCT\Content\Meta\html( 2 );
	expect( $before + 1 === $term_reads && ! str_contains( $html, '<dt>Brand</dt>' ) && ! str_contains( $html, 'Reference code' ) && ! str_contains( $html, 'R-123' ), 'Portfolio consumed product taxonomy metadata.' );
	$before = $data_reads + $term_reads;
	foreach ( array( 3, 4, 5 ) as $id ) {
		expect( '' === \RadmanCT\Content\Meta\html( $id ) && '' === \RadmanCT\Content\Meta\archive( $id ), 'Private or unrelated metadata was exposed.' );
	}
	$modules['catalog'] = false;
	expect( '' === \RadmanCT\Content\Meta\html( 1 ) && $before === $data_reads + $term_reads, 'Disabled module performed metadata work.' );
	$modules['catalog'] = true;
	$html = \RadmanCT\Content\Shortcodes\metadata();
	expect( str_contains( $html, 'rdct-cnt-meta' ) && array( 'rdct-content-meta' ) === $styles, 'Elementor shortcode did not use isolated metadata rendering and CSS.' );
	$source_id = 4;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata(), 'Protected shortcode exposed metadata.' );
	$source_id = 1;
	$loop_id = 2;
	$html = \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop' ) );
	expect( str_contains( $html, 'rdct-cnt-meta' ) && ! str_contains( $html, '<dt>Brand</dt>' ), 'Loop metadata used the outer product instead of the current project.' );
	$loop_id = 1;
	$before = $term_reads;
	$html = \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop', 'compact' => 'yes' ) );
	expect( $before + 1 === $term_reads && str_contains( $html, 'rdct-cnt-meta--compact' ), 'Compact loop metadata read unused product taxonomies.' );
	$loop_id = 5;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop' ) ), 'Unrelated loop item fell back to the outer product.' );
	$loop_id = 4;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop' ) ), 'Protected loop product exposed metadata.' );
	$loop_id = 1; $modules['catalog'] = false; $before = $data_reads + $term_reads;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop' ) ) && $before === $data_reads + $term_reads, 'Disabled loop module performed metadata reads.' );
	$modules['catalog'] = true; $source_id = 3; $editable_id = 3; $preview = false;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata(), 'Capability alone exposed an unpublished product.' );
	$preview = true; $editable_id = 0;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata(), 'Preview parameter alone exposed an unpublished product.' );
	$editable_id = 3;
	expect( str_contains( \RadmanCT\Content\Shortcodes\metadata(), 'rdct-cnt-meta' ), 'Authorized native draft preview lost product metadata.' );
	expect( str_contains( \RadmanCT\Content\Shortcodes\product_display(), '0 &lt;price&gt;' ), 'Authorized draft preview lost its escaped zero price.' );
	$pricing_mode = 'contact';
	expect( str_contains( \RadmanCT\Content\Shortcodes\product_display(), 'href="/contact/"' ), 'Authorized draft preview lost its contact destination.' );
	$source_id = 1; $loop_id = 3;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata( array( 'context' => 'loop' ) ), 'Draft preview permission leaked another loop item.' );
	$source_id = 4; $editable_id = 4;
	expect( '' === \RadmanCT\Content\Shortcodes\metadata() && '' === \RadmanCT\Content\Shortcodes\product_display(), 'Preview bypassed a password.' );
	echo "Passed: product/portfolio metadata, safe native archive links, bounded linked terms, protected/disabled isolation and shared shortcode rendering.\n";
}
