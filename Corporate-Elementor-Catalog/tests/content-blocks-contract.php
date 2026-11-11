<?php

define( 'ABSPATH', '/usr/src/wordpress/' );
define( 'WPINC', 'wp-includes' );
$posts = array();
$hooks = array();
$translations = array();
$rendered = array();
function get_option( $key, $default = false ) { return 'blog_charset' === $key ? 'UTF-8' : $default; }
function get_post( $id ) { return $GLOBALS['posts'][ $id ] ?? null; }
function add_filter( $name, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['hooks'][ $name ][ $priority ][] = $callback; }
function add_action( ...$args ) {}
function remove_filter( $name, $callback, $priority = 10 ) { foreach ( $GLOBALS['hooks'][ $name ][ $priority ] ?? array() as $key => $value ) { if ( $value === $callback ) { unset( $GLOBALS['hooks'][ $name ][ $priority ][ $key ] ); } } }
function apply_filters( $name, $value, ...$args ) {
	if ( 'wpml_object_id' === $name ) { return array_key_exists( $value, $GLOBALS['translations'] ) ? $GLOBALS['translations'][ $value ] : $value; }
	$callbacks = $GLOBALS['hooks'][ $name ] ?? array();
	ksort( $callbacks );
	foreach ( $callbacks as $group ) { foreach ( $group as $callback ) { $value = $callback( $value, ...$args ); } }
	return $value;
}
function wp_allowed_protocols() { return array( 'http', 'https', 'mailto', 'tel' ); }
function _doing_it_wrong( $function, $message, $version ) { throw new RuntimeException( $function . ': ' . $message ); }
function __( $message ) { return $message; }
function _canonical_charset( $charset ) { return 'UTF-8'; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require ABSPATH . WPINC . '/compat.php';
require ABSPATH . WPINC . '/compat-utf8.php';
require ABSPATH . WPINC . '/utf8.php';
require ABSPATH . WPINC . '/formatting.php';
require ABSPATH . WPINC . '/kses.php';
require ABSPATH . WPINC . '/class-wp-block-parser-block.php';
require ABSPATH . WPINC . '/class-wp-block-parser-frame.php';
require ABSPATH . WPINC . '/class-wp-block-parser.php';
require ABSPATH . WPINC . '/class-wp-token-map.php';
foreach ( array( 'html5-named-character-references', 'class-wp-html-attribute-token', 'class-wp-html-text-replacement', 'class-wp-html-span', 'class-wp-html-doctype-info', 'class-wp-html-decoder', 'class-wp-html-tag-processor' ) as $file ) {
	require ABSPATH . WPINC . '/html-api/' . $file . '.php';
}
function parse_blocks( $content ) { return ( new WP_Block_Parser() )->parse( $content ); }
function render_block( $node ) {
	$pre = apply_filters( 'pre_render_block', null, $node );
	if ( null !== $pre ) { return $pre; }
	$GLOBALS['rendered'][] = $node['blockName'];
	$index = 0;
	$html = '';
	foreach ( $node['innerContent'] as $fragment ) { $html .= is_string( $fragment ) ? $fragment : render_block( $node['innerBlocks'][ $index++ ] ); }
	return $html;
}
function pattern( $id, $content, $status = 'publish', $type = 'wp_block', $password = '' ) {
	$GLOBALS['posts'][ $id ] = (object) array( 'ID' => $id, 'post_type' => $type, 'post_status' => $status, 'post_password' => $password, 'post_content' => $content );
}
require dirname( __DIR__ ) . '/radmanct/inc/content-blocks.php';

pattern( 1, '<!-- wp:paragraph --><p id="details">Hello <a href="#details">here</a><script>bad()</script></p><!-- /wp:paragraph -->' );
$html = \RadmanCT\ContentBlocks\render( 1, 'placement-a-' );
expect( str_contains( $html, 'id="placement-a-details"' ) && str_contains( $html, 'href="#placement-a-details"' ), 'Local anchor identifiers were not scoped.' );
expect( ! str_contains( $html, '<script' ), 'Unsafe rendered markup passed KSES.' );
pattern( 2, '<!-- wp:block {"ref":1} /-->' );
expect( str_contains( \RadmanCT\ContentBlocks\render( 2, 'nested-' ), 'Hello' ), 'Published nested pattern did not render.' );
pattern( 3, '<!-- wp:paragraph --><p>Draft secret</p><!-- /wp:paragraph -->', 'draft' );
pattern( 4, '<!-- wp:block {"ref":3} /-->' );
expect( '' === \RadmanCT\ContentBlocks\render( 4, 'a-' ), 'Draft content leaked through a reference.' );
pattern( 5, '<!-- wp:block {"ref":6} /-->' );
pattern( 6, '<!-- wp:block {"ref":5} /-->' );
expect( null === \RadmanCT\ContentBlocks\inspect( 5 ), 'Cyclic reference accepted.' );
pattern( 7, '<!-- wp:shortcode -->[private]<!-- /wp:shortcode -->' );
pattern( 8, '<!-- wp:html --><script>bad()</script><!-- /wp:html -->' );
expect( '' === \RadmanCT\ContentBlocks\render( 7, 'a-' ) && '' === \RadmanCT\ContentBlocks\render( 8, 'a-' ), 'Unsupported executable content was rendered.' );
pattern( 9, 'secret', 'publish', 'post' );
pattern( 10, 'secret', 'publish', 'wp_block', 'password' );
expect( null === \RadmanCT\ContentBlocks\inspect( 9 ) && null === \RadmanCT\ContentBlocks\inspect( 10 ), 'Wrong post type or protected pattern accepted.' );
$translations[1] = null;
expect( '' === \RadmanCT\ContentBlocks\render( 1, 'a-' ), 'Missing translation fell back to the wrong language.' );
$translations[1] = 3;
expect( '' === \RadmanCT\ContentBlocks\render( 1, 'a-' ), 'Draft translation was exposed.' );
$translations[1] = 2;
expect( null === \RadmanCT\ContentBlocks\inspect( 1 ), 'Cycle through translated references accepted.' );
$translations = array();
pattern( 11, str_repeat( '<!-- wp:paragraph --><p>x</p><!-- /wp:paragraph -->', 257 ) );
expect( null === \RadmanCT\ContentBlocks\inspect( 11 ), 'Node budget was not enforced.' );
$depth = '';
for ( $id = 20; $id <= 28; ++$id ) { pattern( $id, '<!-- wp:block {"ref":' . ( $id + 1 ) . '} /-->' ); }
pattern( 29, '<!-- wp:paragraph --><p>deep</p><!-- /wp:paragraph -->' );
expect( null === \RadmanCT\ContentBlocks\inspect( 20 ), 'Reference depth was not bounded.' );
expect( empty( $hooks['pre_render_block'][ PHP_INT_MAX ] ), 'Scoped renderer guard leaked into other content.' );
require dirname( __DIR__ ) . '/radmanct/inc/builder.php';
$item = array( 'id' => 'shared1', 'type' => 'block', 'blockId' => 1 );
$header = \RadmanCT\Builder\content( 'header', $item );
$render_count = count( $rendered );
expect( $header === \RadmanCT\Builder\content( 'header', $item ) && count( $rendered ) === $render_count, 'Prepared fragment was rendered twice.' );
$footer = \RadmanCT\Builder\content( 'footer', $item );
expect( str_contains( $header, 'rdct-block-header-shared1-details' ) && str_contains( $footer, 'rdct-block-footer-shared1-details' ), 'Reused content identifiers collided between placements.' );
echo "Passed: native parser/KSES/HTML anchors, published-only nesting, translations, cycles, limits and filter cleanup; block rendering uses stubs.\n";
