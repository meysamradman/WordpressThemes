<?php

namespace RadmanCT\Content;

defined( 'ABSPATH' ) || exit;

function data( int $post_id ): array {
	static $records = array();
	if ( ! isset( $records[ $post_id ] ) ) {
		$records[ $post_id ] = function_exists( '\\RadmanCTContent\\Model\\read' ) ? \RadmanCTContent\Model\read( $post_id ) : array( 'code' => '', 'gallery' => array(), 'specifications' => array() );
	}
	return $records[ $post_id ];
}

function gallery_ids( int $post_id ): array {
	if ( post_password_required( $post_id ) ) {
		return array();
	}
	static $galleries = array();
	$language = apply_filters( 'wpml_current_language', '' );
	$key = $post_id . '/' . ( is_string( $language ) ? $language : '' );
	if ( isset( $galleries[ $key ] ) ) {
		return $galleries[ $key ];
	}
	$ids = array();
	foreach ( data( $post_id )['gallery'] as $id ) {
		$id = (int) apply_filters( 'wpml_object_id', $id, 'attachment', true );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}
	$ids = array_values( array_unique( $ids ) );
	if ( ! $ids ) {
		return $galleries[ $key ] = array();
	}
	$posts = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => 'image', 'post__in' => $ids, 'posts_per_page' => 20, 'orderby' => 'post__in', 'no_found_rows' => true, 'update_post_term_cache' => false, 'suppress_filters' => false ) );
	return $galleries[ $key ] = array_map( static function ( $post ): int { return (int) $post->ID; }, $posts );
}

function enqueue(): void {
	if ( \RadmanCT\Renderers\uses_elementor() ) {
		return;
	}
	if ( array( 'catalog', 'archive' ) === \RadmanCT\Renderers\context() ) {
		require_once __DIR__ . '/catalog-filters.php';
		CatalogFilters\prepare_fields( apply_filters( 'radmanct/catalog/filter_fields', array_keys( \RadmanCTContent\Catalog\FILTERS ) ) );
		\RadmanCT\Assets\style( 'rdct-catalog', 'src/css/catalog.css', 'parent', array( 'rdct-theme' ) );
	} elseif ( is_post_type_archive( 'radman_portfolio' ) || is_tax( 'radman_portfolio_cat' ) ) {
		\RadmanCT\Assets\style( 'rdct-portfolio', apply_filters( 'radmanct/portfolio/archive_style', 'src/css/portfolio.css' ), 'parent', array( 'rdct-theme' ) );
	}
	foreach ( array( 'radman_product' => 'catalog', 'radman_portfolio' => 'portfolio' ) as $type => $name ) {
		if ( is_singular( $type ) ) {
			\RadmanCT\Assets\style( 'rdct-' . $name . '-single', 'src/css/' . $name . '-single.css', 'parent', array( 'rdct-theme' ) );
			require_once __DIR__ . '/content-media.php';
			$images = Media\ids( get_queried_object_id() );
			if ( $images ) {
				\RadmanCT\Assets\style( 'rdct-gallery', 'src/css/gallery.css', 'parent', array( 'rdct-theme' ) );
			}
		}
	}
}

function gallery_attributes(): string {
	return 'aria-label="' . esc_attr__( 'Image gallery', 'radmanct' ) . '" data-rdct-gallery data-close="' . esc_attr__( 'Close', 'radmanct' ) . '" data-previous="' . esc_attr__( 'Previous image', 'radmanct' ) . '" data-next="' . esc_attr__( 'Next image', 'radmanct' ) . '" data-loading="' . esc_attr__( 'Loading image...', 'radmanct' ) . '" data-error="' . esc_attr__( 'This image could not be loaded.', 'radmanct' ) . '" data-position="' . esc_attr__( 'Image %1$s of %2$s', 'radmanct' ) . '"';
}

function gallery_link( int $id, string $size = 'medium_large', string $class = '' ): string {
	$url = wp_get_attachment_image_url( $id, 'large' );
	if ( ! $url ) { return ''; }
	$image = wp_get_attachment_image( $id, $size, false, 'large' === $size ? array() : array( 'loading' => 'lazy' ) );
	if ( ! $image ) { return ''; }
	return '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '" data-caption="' . esc_attr( get_post_field( 'post_excerpt', $id ) ) . '">' . $image . '<span class="screen-reader-text">' . esc_html__( 'Open image', 'radmanct' ) . '</span></a>';
}

function gallery( int $post_id ): void {
	$ids = gallery_ids( $post_id );
	if ( ! $ids ) {
		return;
	}
	echo '<section class="rdct-gal" ' . gallery_attributes() . '>';
	foreach ( $ids as $id ) {
		echo gallery_link( $id );
	}
	echo '</section>';
}

function specifications( int $post_id ): void {
	$rows = array_filter( data( $post_id )['specifications'], static function ( array $row ): bool { return '' !== trim( $row['label'] ) && '' !== trim( $row['value'] ); } );
	if ( ! $rows ) {
		return;
	}
	echo '<section class="rdct-spec"><h2>' . esc_html__( 'Technical specifications', 'radmanct' ) . '</h2><dl>';
	foreach ( $rows as $row ) {
		echo '<div><dt>' . esc_html( $row['label'] ) . '</dt><dd>' . nl2br( esc_html( trim( $row['value'] . ' ' . $row['unit'] ) ) ) . '</dd></div>';
	}
	echo '</dl></section>';
}

function filters(): void {
	require_once __DIR__ . '/catalog-filters.php';
	CatalogFilters\render();
}

function metadata( int $post_id ): void {
	require_once __DIR__ . '/content-meta.php';
	Meta\render( $post_id );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
require_once __DIR__ . '/content-shortcodes.php';
