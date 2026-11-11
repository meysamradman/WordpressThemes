<?php

namespace RadmanCT\CardMedia;

defined( 'ABSPATH' ) || exit;
const VIDEO_KEY = '_rdct_card_video_id';
const MODE_KEY = '_rdct_card_video_mode';

function types(): array {
	return array_values( array_filter( array( 'post', 'radman_product', 'radman_portfolio' ), 'post_type_exists' ) );
}

function valid_id( $value ): int {
	$id = filter_var( $value, FILTER_VALIDATE_INT );
	if ( false === $id || $id < 1 || ! function_exists( '\\RadmanCTComponents\\Components\\CardPreview\\video' ) ) { return 0; }
	return \RadmanCTComponents\Components\CardPreview\video( $id ) ? $id : 0;
}

function mode( $value ): string { return 'hover' === $value ? 'hover' : 'auto'; }

function register(): void {
	if ( ! function_exists( '\\RadmanCTComponents\\Components\\CardPreview\\video' ) ) { return; }
	foreach ( types() as $type ) {
		$base = array( 'single' => true, 'show_in_rest' => false, 'auth_callback' => static fn( $allowed, $key, $id ): bool => current_user_can( 'edit_post', (int) $id ) );
		register_post_meta( $type, VIDEO_KEY, $base + array( 'type' => 'integer', 'default' => 0, 'sanitize_callback' => __NAMESPACE__ . '\\valid_id' ) );
		register_post_meta( $type, MODE_KEY, $base + array( 'type' => 'string', 'default' => 'auto', 'sanitize_callback' => __NAMESPACE__ . '\\mode' ) );
	}
}

function frame( int $post_id, string $image ): array {
	$result = array( 'html' => $image, 'control' => '' );
	if ( $post_id < 1 || ! $image || ! in_array( get_post_type( $post_id ), types(), true ) || post_password_required( $post_id ) || ! function_exists( '\\RadmanCTComponents\\Components\\CardPreview\\frame' ) ) { return $result; }
	return \RadmanCTComponents\Components\CardPreview\frame( $image, array( 'video_id' => get_post_meta( $post_id, VIDEO_KEY, true ), 'mode' => get_post_meta( $post_id, MODE_KEY, true ) ) );
}

function prime( array $posts ): void {
	$ids = array();
	foreach ( $posts as $post ) {
		if ( ! $post instanceof \WP_Post || ! in_array( $post->post_type, types(), true ) || post_password_required( $post ) ) { continue; }
		$id = filter_var( get_post_meta( $post->ID, VIDEO_KEY, true ), FILTER_VALIDATE_INT );
		if ( false !== $id && $id > 0 ) { $ids[] = $id; }
	}
	if ( $ids ) { _prime_post_caches( array_values( array_unique( $ids ) ), false, true ); }
}

function prepare(): void {
	if ( \RadmanCT\Renderers\uses_elementor() || 'archive' !== ( \RadmanCT\Renderers\context()[1] ?? '' ) ) { return; }
	global $wp_query;
	$posts = $wp_query instanceof \WP_Query ? $wp_query->posts : array();
	prime( $posts );
	foreach ( $posts as $post ) {
		if ( $post instanceof \WP_Post && in_array( $post->post_type, types(), true ) && ! post_password_required( $post ) && has_post_thumbnail( $post ) && valid_id( get_post_meta( $post->ID, VIDEO_KEY, true ) ) ) {
			\RadmanCTComponents\Components\enqueue( 'card-preview' ); break;
		}
	}
}

function shortcode( $attributes ): string {
	$attributes = shortcode_atts( array( 'post_id' => get_the_ID(), 'size' => 'medium_large' ), (array) $attributes, 'rdct_card_preview' );
	$id = absint( $attributes['post_id'] );
	if ( ! $id || ! in_array( get_post_type( $id ), types(), true ) || post_password_required( $id ) || ( 'publish' !== get_post_status( $id ) && ! current_user_can( 'read_post', $id ) ) ) { return ''; }
	$size = in_array( $attributes['size'], array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' ), true ) ? $attributes['size'] : 'medium_large';
	$frame = frame( $id, get_the_post_thumbnail( $id, $size ) );
	return $frame['html'] ? '<figure class="rdct-card-preview-owner">' . $frame['html'] . $frame['control'] . '</figure>' : '';
}

add_action( 'init', __NAMESPACE__ . '\\register', 30 );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\prepare', 24 );
add_shortcode( 'rdct_card_preview', __NAMESPACE__ . '\\shortcode' );
