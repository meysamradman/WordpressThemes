<?php

namespace RadmanCTTests\BlogMedia;

defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/admin/blog-media.php';

function expect( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new \RuntimeException( $message ); }
}

$terms = array(); $posts = array(); $user = get_current_user_id(); $input = $_POST; $query_input = $_GET;
$prefix = 'rdct-media-contract-' . wp_generate_uuid4();
$key = \RadmanCT\BlogMedia\META_KEY;
try {
	foreach ( array( 'audio', 'child', 'standard', 'video' ) as $name ) {
		$args = in_array( $name, array( 'child', 'standard' ), true ) ? array( 'parent' => $terms['audio'] ) : array();
		$term = wp_insert_term( $prefix . '-' . $name, 'category', $args );
		expect( ! is_wp_error( $term ), 'Test category creation failed.' ); $terms[ $name ] = (int) $term['term_id'];
	}
	update_term_meta( $terms['audio'], $key, 'audio' );
	update_term_meta( $terms['video'], $key, 'video' );
	update_term_meta( $terms['standard'], $key, 'standard' );
	$post_id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => $prefix, 'post_category' => array( $terms['child'] ) ), true );
	expect( is_int( $post_id ) && $post_id > 0, 'Test post creation failed.' );
	$posts[] = $post_id; set_post_thumbnail( $post_id, 12 );
	set_post_format( $post_id, 'video' );
	expect( 'audio' === \RadmanCT\BlogMedia\type( $post_id ), 'Category inheritance or category priority over legacy format failed.' );
	wp_update_term( $terms['audio'], 'category', array( 'name' => $prefix . '-renamed' ) );
	expect( 'audio' === \RadmanCT\BlogMedia\type( $post_id ), 'Classification depends on category text.' );
	wp_set_post_categories( $post_id, array( $terms['audio'], $terms['standard'] ) );
	expect( '' === \RadmanCT\BlogMedia\type( $post_id ), 'Explicit child article must override a selected audio ancestor and legacy video format.' );
	wp_set_post_categories( $post_id, array( $terms['child'], $terms['video'] ) );
	expect( 'video' === \RadmanCT\BlogMedia\type( $post_id ), 'Mixed-category video priority is not deterministic.' );
	wp_set_post_categories( $post_id, array( $terms['child'] ) );
	\RadmanCT\BlogMedia\prime( array( get_post( $post_id ) ) );
	global $wpdb;
	$queries = $wpdb->num_queries;
	\RadmanCT\BlogMedia\type( $post_id ); \RadmanCT\BlogMedia\type( $post_id );
	expect( $queries === $wpdb->num_queries, 'Primed badge classification adds database queries.' );

	$root = get_stylesheet_directory();
	foreach ( array(
		array( $root . '/template-parts/blog/shared/post-card.php', array( 'post_id' => $post_id, 'variant' => 'compact' ) ),
		array( $root . '/template-parts/blog/archive/lead-card.php', array( 'post_id' => $post_id, 'primary' => true ) ),
		array( $root . '/template-parts/blog/card.php', array() ),
		array( get_template_directory() . '/template-parts/content/card.php', array() ),
		array( $root . '/template-parts/blog/single/cover.php', array( 'post_id' => $post_id ) ),
	) as list( $file, $args ) ) {
		$GLOBALS['post'] = get_post( $post_id ); setup_postdata( $GLOBALS['post'] );
		ob_start(); include $file; $html = ob_get_clean();
		expect( str_contains( $html, 'rdct-blog-media-badge' ) && str_contains( $html, '<svg' ), 'A blog card path lost the shared badge: ' . $file );
		expect( ! str_contains( $html, '<audio' ) && ! str_contains( $html, '<video' ), 'Archive card rendered a media player.' );
	}
	wp_reset_postdata();
	expect( '' !== \RadmanCT\Icons\render( 'custom-music' ), 'Shared music icon is missing.' );
	$_GET['_rdct_fragment'] = 'blog';
	wp_styles()->done = array_values( array_diff( wp_styles()->done, array( 'rdct-blog-media' ) ) );
	ob_start(); \RadmanCT\BlogMedia\badge( $post_id ); \RadmanCT\BlogMedia\badge( $post_id ); $html = ob_get_clean();
	expect( 1 === substr_count( $html, "id='rdct-blog-media-css'" ) + substr_count( $html, 'id="rdct-blog-media-css"' ), 'AJAX badge stylesheet is missing or duplicated.' );
	wp_update_post( array( 'ID' => $post_id, 'post_password' => 'contract-protected' ) );
	ob_start(); \RadmanCT\BlogMedia\badge( $post_id ); $html = ob_get_clean();
	expect( '' === $html, 'Password-protected media classification leaked.' );
	wp_update_post( array( 'ID' => $post_id, 'post_password' => '' ) );
	delete_term_meta( $terms['audio'], $key );
	expect( 'video' === \RadmanCT\BlogMedia\type( $post_id ), 'Existing native post format fallback was lost.' );
	set_post_format( $post_id, 'standard' );
	wp_dequeue_style( 'rdct-blog-media' );
	ob_start(); \RadmanCT\BlogMedia\badge( $post_id ); $html = ob_get_clean();
	expect( '' === $html && ! wp_style_is( 'rdct-blog-media', 'enqueued' ), 'Article-only output loads badge assets.' );

	$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
	wp_set_current_user( $admin->ID );
	$_POST = array( 'rdct_blog_media_type' => 'audio', 'rdct_blog_media_nonce' => 'invalid' );
	\RadmanCT\Admin\BlogMedia\save( $terms['audio'] );
	expect( '' === get_term_meta( $terms['audio'], $key, true ), 'Invalid nonce changed category metadata.' );
	$_POST['rdct_blog_media_nonce'] = wp_create_nonce( 'rdct_blog_media_category' );
	\RadmanCT\Admin\BlogMedia\save( $terms['audio'] );
	expect( 'audio' === get_term_meta( $terms['audio'], $key, true ), 'Native category field did not save.' );
	$_POST['rdct_blog_media_type'] = array( 'video' );
	\RadmanCT\Admin\BlogMedia\save( $terms['audio'] );
	expect( 'audio' === get_term_meta( $terms['audio'], $key, true ), 'Malformed input overwrote the category setting.' );
	wp_set_current_user( 0 );
	$_POST = array( 'rdct_blog_media_type' => 'video', 'rdct_blog_media_nonce' => wp_create_nonce( 'rdct_blog_media_category' ) );
	\RadmanCT\Admin\BlogMedia\save( $terms['audio'] );
	expect( 'audio' === get_term_meta( $terms['audio'], $key, true ), 'Unauthorized category change succeeded.' );

	$audio = do_blocks( '<!-- wp:audio {"src":"/contract-audio.mp3","preload":"none"} --><figure class="wp-block-audio"><audio controls src="/contract-audio.mp3" preload="none"></audio></figure><!-- /wp:audio -->' );
	expect( str_contains( $audio, '<audio' ) && str_contains( $audio, 'controls' ) && ! str_contains( $audio, 'autoplay' ), 'Native editor audio output was replaced.' );
	echo "Passed: category inheritance/override, native formats, five card paths, query caching, AJAX CSS, password protection and category-save authorization.\n";
} finally {
	$_POST = $input; $_GET = $query_input; wp_set_current_user( $user );
	foreach ( $posts as $id ) { wp_delete_post( $id, true ); }
	foreach ( array_reverse( $terms ) as $id ) { wp_delete_term( $id, 'category' ); }
	wp_reset_postdata();
}
