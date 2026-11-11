<?php

namespace RadmanCTChild\Home;

defined( 'ABSPATH' ) || exit;

const TEMPLATE = 'templates/client-home.php';
require_once __DIR__ . '/home/hero-content.php';

function data( int $post_id ): array {
	return hero_content();
}

function image_id( int $post_id ): int {
	$id = HERO_IMAGE_ID;
	return $id && wp_attachment_is_image( $id ) ? $id : 0;
}

function heading( string $text, string $tag, string $color, bool $rotating = false ): void {
	if ( '' === $text ) { return; }
	$settings = \RadmanCT\Settings\values();
	$role = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $tag : 'body';
	$color = $settings[ $role . '_color' ] ?: ( 'body' !== $role ? $settings['headings_color'] : '' ) ?: $color;
	$options = array( 'mode' => 'static', 'tag' => $tag, 'active' => $color, 'highlight' => 'none' );
	$phrases = $rotating ? array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', $text ) ?: array() ), static fn( $line ) => '' !== $line ) ) : array();
	if ( count( $phrases ) > 1 ) {
		$options['mode'] = 'rotate';
		$options['word_color'] = $color;
		$options['items'] = array_map( static fn( $line ) => array( 'title' => $line ), array_slice( $phrases, 0, 10 ) );
	}
	if ( function_exists( 'rdct_text_reveal' ) ) { \rdct_text_reveal( 'rotate' === $options['mode'] ? '' : $text, $options ); }
	else { echo '<' . $tag . '>' . nl2br( esc_html( $text ) ) . '</' . $tag . '>'; }
}

function media( int $post_id ): void {
	$id = image_id( $post_id );
	if ( $id && function_exists( 'rdct_image' ) ) { \rdct_image( array( 'image_id' => $id, 'image_size' => 'full', 'image_loading' => 'high' ) ); }
	elseif ( $id ) { echo wp_get_attachment_image( $id, 'full', false, array( 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '100vw' ) ); }
}

function preload_image( array $resources ): array {
	if ( ! is_page_template( TEMPLATE ) || post_password_required() ) { return $resources; }
	$id = image_id( get_queried_object_id() );
	$image = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
	if ( ! $image ) { return $resources; }
	foreach ( $resources as $resource ) { if ( is_array( $resource ) && ( $resource['href'] ?? '' ) === $image[0] ) { return $resources; } }
	$preload = array( 'href' => $image[0], 'as' => 'image', 'fetchpriority' => 'high' );
	$srcset = wp_get_attachment_image_srcset( $id, 'full' );
	if ( $srcset ) { $preload['imagesrcset'] = $srcset; $preload['imagesizes'] = wp_get_attachment_image_sizes( $id, 'full' ); }
	$resources[] = $preload;
	return $resources;
}

add_filter( 'wp_preload_resources', __NAMESPACE__ . '\\preload_image' );

add_filter( 'body_class', static function ( array $classes ): array { if ( is_page_template( TEMPLATE ) ) { $classes[] = 'rdct-clt-home'; } return $classes; } );

if ( is_admin() ) {
	add_action( 'load-post.php', static function (): void {
		$id = isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( $id && TEMPLATE === get_page_template_slug( $id ) ) { remove_post_type_support( 'page', 'thumbnail' ); }
	} );
}
