<?php

namespace RadmanCT\Navigation\Templates;

defined( 'ABSPATH' ) || exit;

function options(): array {
	static $cache = array();
	$language = apply_filters( 'wpml_current_language', null ); $key = is_string( $language ) ? $language : '';
	if ( isset( $cache[ $key ] ) ) { return $cache[ $key ]; }
	$result = array( 0 => __( 'None', 'radmanct' ) );
	if ( post_type_exists( 'elementor_library' ) ) {
		foreach ( get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false, 'has_password' => false, 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false ) ) as $post ) { $result[ (int) $post->ID ] = get_the_title( $post ); }
	}
	return $cache[ $key ] = $result;
}

function render( int $id ): string {
	static $active = array();
	$id = (int) apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
	if ( ! $id || isset( $active[ $id ] ) || count( $active ) >= 4 || 'elementor_library' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) || post_password_required( $id ) || ! class_exists( '\\Elementor\\Plugin' ) ) { return ''; }
	$active[ $id ] = true;
	try { return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true ); }
	finally { unset( $active[ $id ] ); }
}
