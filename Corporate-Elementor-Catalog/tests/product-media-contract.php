<?php

namespace RadmanCTContent\Modules { function enabled( $module ) { return $GLOBALS['enabled'] ?? true; } }
namespace RadmanCTContent\Model {
	function read( $id ) { return array( 'code' => '', 'specifications' => array(), 'gallery' => array( 1 => array( 10, 11, 12 ), 2 => array( 11, 12 ), 3 => array( 99, 11 ), 5 => array( 11 ) )[ $id ] ?? array() ); }
}
namespace {
	define( 'ABSPATH', '/' );
	function add_action( ...$args ) {}
	function add_shortcode( ...$args ) {}
	function get_post_type( $id ) { return 7 === $id ? 'radman_portfolio' : 'radman_product'; }
	function get_post_status( $id ) { return 5 === $id ? 'draft' : 'publish'; }
	function post_password_required( $id ) { return 4 === $id; }
	function is_preview() { return $GLOBALS['preview'] ?? false; }
	function is_singular( $type ) { return true; }
	function get_queried_object_id() { return 5; }
	function current_user_can( $cap, $id ) { return 5 === $id; }
	function get_post_thumbnail_id( $id ) { return in_array( $id, array( 1, 7 ), true ) ? 10 : 0; }
	function wp_attachment_is_image( $id ) { return true; }
	function apply_filters( $hook, $value, ...$args ) {
		if ( 'wpml_current_language' === $hook ) { return $GLOBALS['language'] ?? ''; }
		if ( 'wpml_object_id' === $hook && 'en' === ( $GLOBALS['language'] ?? '' ) ) { return $value + 100; }
		return $value;
	}
	function get_posts( $args ) { $GLOBALS['gallery_queries'] = ( $GLOBALS['gallery_queries'] ?? 0 ) + 1; return array_map( static fn( $id ) => (object) array( 'ID' => $id ), $args['post__in'] ); }
	function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
	function esc_url( $text ) { return esc_attr( $text ); }
	function esc_attr__( $text, $domain ) { return esc_attr( $text ); }
	function esc_html__( $text, $domain ) { return esc_attr( $text ); }
	function wp_get_attachment_image_url( $id, $size ) { return 99 === $id ? false : '/images/' . $id . '.jpg'; }
	function wp_get_attachment_image( $id, $size ) { return 99 === $id ? '' : '<img src="/images/' . $id . '.jpg" alt="" data-size="' . $size . '">'; }
	function get_post_field( $field, $id ) { return 'Caption "<unsafe>"'; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	require __DIR__ . '/../radmanct/inc/integrations/content.php';
	require __DIR__ . '/../radmanct/inc/integrations/product-media.php';
	expect( array( 10, 11, 12 ) === \RadmanCT\Content\ProductMedia\ids( 1 ), 'Featured image was duplicated or reordered.' );
	ob_start(); \RadmanCT\Content\ProductMedia\render( 1 ); $html = ob_get_clean();
	expect( 1 === substr_count( $html, 'data-rdct-gallery' ) && 3 === substr_count( $html, '<a ' ) && str_contains( $html, 'data-size="large"' ) && str_contains( $html, 'data-size="thumbnail"' ), 'Product images did not share one gallery with correctly sized thumbnails.' );
	expect( ! str_contains( $html, '<unsafe>' ) && str_contains( $html, '&lt;unsafe&gt;' ), 'Image caption was not escaped.' );
	foreach ( array( 2, 3 ) as $id ) {
		ob_start(); \RadmanCT\Content\ProductMedia\render( $id ); $html = ob_get_clean();
		expect( str_contains( $html, 'href="/images/11.jpg" class="rdct-prd-media__image"' ) && ! str_contains( $html, '/99.jpg' ), 'Missing featured or broken image did not fall back to the gallery.' );
	}
	foreach ( array( 4, 5, 6 ) as $id ) {
		ob_start(); \RadmanCT\Content\ProductMedia\render( $id ); $html = ob_get_clean();
		expect( ! str_contains( $html, 'data-rdct-gallery' ) && str_contains( $html, 'rdct-image-placeholder' ), 'Protected, draft or empty product activated a gallery.' );
	}
	$preview = true;
	expect( array( 11 ) === \RadmanCT\Content\ProductMedia\ids( 5 ), 'Authorized draft preview lost its gallery.' );
	ob_start(); \RadmanCT\Content\Media\render( 7 ); $html = ob_get_clean();
	expect( str_contains( $html, 'rdct-prj-media__image' ) && 1 === substr_count( $html, 'data-rdct-gallery' ) && 1 === substr_count( $html, '<a ' ), 'Featured-only project did not get its own gallery presentation.' );
	expect( array() === \RadmanCT\Content\ProductMedia\ids( 7 ), 'Product-only adapter accepted a project.' );
	$language = 'en'; $before = $gallery_queries;
	expect( array( 110, 111, 112 ) === \RadmanCT\Content\ProductMedia\ids( 1 ), 'Gallery cache reused image IDs from another language.' );
	expect( $before + 1 === $gallery_queries, 'New language did not use one bounded gallery query.' );
	\RadmanCT\Content\ProductMedia\ids( 1 );
	expect( $before + 1 === $gallery_queries, 'Repeated language rendering repeated the gallery query.' );
	$language = '';
	expect( array( 10, 11, 12 ) === \RadmanCT\Content\ProductMedia\ids( 1 ) && $before + 1 === $gallery_queries, 'Restoring the language lost its cached gallery.' );
	$enabled = false;
	expect( array() === \RadmanCT\Content\ProductMedia\ids( 1 ), 'Disabled catalog exposed image IDs.' );
	ob_start(); \RadmanCT\Content\Media\render( 7 ); $html = ob_get_clean();
	expect( '' === $html, 'Disabled portfolio exposed gallery output.' );
	echo "Passed: featured/gallery deduplication, one dialog group, image fallback, caption escaping and protected draft isolation.\n";
}
