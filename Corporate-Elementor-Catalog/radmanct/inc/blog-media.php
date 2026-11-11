<?php

namespace RadmanCT\BlogMedia;

defined( 'ABSPATH' ) || exit;
const META_KEY = '_rdct_blog_media_type';

function labels(): array {
	return array( '' => __( 'Inherit from parent category', 'radmanct' ), 'standard' => __( 'Article (no media badge)', 'radmanct' ), 'audio' => __( 'Podcast / audio', 'radmanct' ), 'video' => __( 'Video', 'radmanct' ) );
}

function sanitize( $value ): string {
	return is_string( $value ) && in_array( $value, array( '', 'standard', 'audio', 'video' ), true ) ? $value : '';
}

function register(): void {
	register_term_meta( 'category', META_KEY, array(
		'type' => 'string', 'single' => true, 'default' => '', 'show_in_rest' => false,
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize',
		'auth_callback' => static fn( $allowed, $key, $term_id ): bool => current_user_can( 'edit_term', (int) $term_id ),
	) );
}

function branches( int $post_id ): array {
	$terms = get_the_terms( $post_id, 'category' );
	if ( ! is_array( $terms ) ) { return array(); }
	$branches = array(); $parents = array();
	foreach ( $terms as $term ) {
		$ancestors = array_slice( get_ancestors( $term->term_id, 'category', 'taxonomy' ), 0, 20 );
		$branches[ $term->term_id ] = array_merge( array( $term->term_id ), $ancestors );
		$parents = array_merge( $parents, $ancestors );
	}
	return array_diff_key( $branches, array_fill_keys( $parents, true ) );
}

function prime( array $posts ): void {
	$ids = array();
	foreach ( $posts as $post ) {
		if ( $post instanceof \WP_Post && 'post' === $post->post_type ) {
			foreach ( branches( $post->ID ) as $branch ) { $ids = array_merge( $ids, $branch ); }
		}
	}
	if ( $ids ) { update_meta_cache( 'term', array_unique( $ids ) ); }
}

function type( int $post_id ): string {
	if ( $post_id < 1 || 'post' !== get_post_type( $post_id ) || post_password_required( $post_id ) ) { return ''; }
	$branches = branches( $post_id );
	$ids = array();
	foreach ( $branches as $branch ) { $ids = array_merge( $ids, $branch ); }
	if ( $ids ) { update_meta_cache( 'term', array_unique( $ids ) ); }
	$types = array();
	foreach ( $branches as $branch ) {
		foreach ( $branch as $id ) {
			$value = sanitize( get_term_meta( $id, META_KEY, true ) );
			if ( '' !== $value ) { $types[] = $value; break; }
		}
	}
	foreach ( array( 'video', 'audio' ) as $type ) { if ( in_array( $type, $types, true ) ) { return $type; } }
	if ( in_array( 'standard', $types, true ) ) { return ''; }
	$format = get_post_format( $post_id );
	return in_array( $format, array( 'video', 'audio' ), true ) ? $format : '';
}

function assets(): void {
	\RadmanCT\Assets\style( 'rdct-blog-media', 'src/css/components/blog-media-badge.css' );
}

function presentation( int $post_id ): array {
	$type = type( $post_id );
	return match ( $type ) {
		'audio' => array( 'type' => $type, 'icon' => 'custom-music', 'label' => __( 'Podcast', 'radmanct' ) ),
		'video' => array( 'type' => $type, 'icon' => 'play', 'label' => __( 'Video article', 'radmanct' ) ),
		default => array( 'type' => '', 'icon' => 'file-text', 'label' => __( 'Articles', 'radmanct' ) ),
	};
}

function prepare(): void {
	if ( \RadmanCT\Renderers\uses_elementor() || 'blog' !== ( \RadmanCT\Renderers\context()[0] ?? '' ) ) { return; }
	global $wp_query;
	$posts = $wp_query instanceof \WP_Query ? $wp_query->posts : array();
	prime( $posts );
	foreach ( $posts as $post ) {
		if ( $post instanceof \WP_Post && has_post_thumbnail( $post->ID ) && type( $post->ID ) ) { assets(); break; }
	}
}

function badge( int $post_id ): void {
	$media = presentation( $post_id );
	if ( ! $media['type'] ) { return; }
	assets();
	if ( ( did_action( 'wp_head' ) || 'blog' === ( $_GET['_rdct_fragment'] ?? '' ) ) && ! wp_style_is( 'rdct-blog-media', 'done' ) ) { wp_print_styles( 'rdct-blog-media' ); }
	require_once __DIR__ . '/icons.php';
	echo '<span class="rdct-blog-media-badge" role="img" aria-label="' . esc_attr( $media['label'] ) . '">' . \RadmanCT\Icons\render( $media['icon'], array( 'size' => 22, 'stroke' => 1.75 ) ) . '</span>';
}

add_action( 'init', __NAMESPACE__ . '\\register' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\prepare', 24 );
