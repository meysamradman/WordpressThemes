<?php

namespace RadmanCT\Integrations\Wpml\Builder;

defined( 'ABSPATH' ) || exit;

const POST_TYPE = 'rdct_layout';
const ELEMENT_TYPE = 'post_rdct_layout';
const INDEX = 'radman_builder_wpml';

function enabled(): bool { return defined( 'ICL_SITEPRESS_VERSION' ); }

function register(): void {
	if ( ! enabled() ) { return; }
	register_post_type( POST_TYPE, array(
		'label' => __( 'Radman CT layout translations', 'radmanct' ),
		'public' => false, 'publicly_queryable' => false, 'show_ui' => false, 'show_in_rest' => false,
		'show_in_menu' => false, 'exclude_from_search' => true, 'rewrite' => false, 'query_var' => false,
		'can_export' => false, 'supports' => array( 'title' ), 'map_meta_cap' => false,
		'capabilities' => array_fill_keys( array( 'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'delete_private_posts', 'delete_published_posts', 'delete_others_posts', 'edit_private_posts', 'edit_published_posts', 'create_posts' ), 'edit_theme_options' ),
	) );
}

function index(): array { $value = get_option( INDEX, array() ); return is_array( $value ) ? $value : array(); }

function reference( string $kind, string $id ): int {
	if ( ! enabled() || ! in_array( $kind, array( 'header', 'footer' ), true ) || ! \RadmanCT\Builder\Storage\valid_id( $id ) ) { return 0; }
	$value = index()[ $kind ][ $id ] ?? 0;
	if ( ! is_int( $value ) || $value < 1 ) { return 0; }
	return layout_id( $kind, $value ) === $id ? $value : 0;
}

function layout_id( string $kind, int $post_id ): ?string {
	if ( POST_TYPE !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) || $kind !== get_post_meta( $post_id, '_rdct_builder_kind', true ) ) { return null; }
	$id = get_post_meta( $post_id, '_rdct_builder_layout', true );
	return \RadmanCT\Builder\Storage\valid_id( $id ) && \RadmanCT\Builder\Storage\exists( $kind, $id ) && ( index()[ $kind ][ $id ] ?? 0 ) === $post_id ? $id : null;
}

function details( int $post_id ): ?object {
	$value = apply_filters( 'wpml_element_language_details', null, array( 'element_id' => $post_id, 'element_type' => POST_TYPE ) );
	return is_object( $value ) && ! empty( $value->trid ) && is_string( $value->language_code ?? null ) ? $value : null;
}

function resolve( string $id, string $kind ): string {
	return resolve_language( $id, $kind, apply_filters( 'wpml_current_language', null ) );
}

function resolve_language( string $id, string $kind, $language ): string {
	if ( ! enabled() || 'none' === $id ) { return $id; }
	$post_id = reference( $kind, $id );
	if ( ! $post_id || ! is_string( $language ) || '' === $language || 'all' === $language ) { return $id; }
	$translated = (int) apply_filters( 'wpml_object_id', $post_id, POST_TYPE, false, $language );
	$layout = $translated ? layout_id( $kind, $translated ) : null;
	return $layout && get_theme_mod( \RadmanCT\Builder\Storage\key( $kind, $layout ), false ) ? $layout : $id;
}

function owns_current_language( string $kind, string $id ): bool {
	static $cache = array();
	$language = apply_filters( 'wpml_current_language', null );
	if ( ! is_string( $language ) ) { return false; }
	$key = $kind . '/' . $id . '/' . $language;
	if ( ! array_key_exists( $key, $cache ) ) {
		$post_id = reference( $kind, $id );
		$cache[ $key ] = $post_id && ( details( $post_id )->language_code ?? null ) === $language;
	}
	return $cache[ $key ];
}

function source_language( string $kind, string $id ): ?string {
	$post_id = reference( $kind, $id );
	return $post_id ? ( details( $post_id )->language_code ?? null ) : null;
}

add_action( 'init', __NAMESPACE__ . '\\register', 9 );
add_filter( 'radmanct/builder/selected', __NAMESPACE__ . '\\resolve', 10, 2 );
