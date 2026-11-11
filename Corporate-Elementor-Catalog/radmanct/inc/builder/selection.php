<?php

namespace RadmanCT\Builder;

defined( 'ABSPATH' ) || exit;

function read( string $kind, string $id = 'default' ): array {
	$stored = get_option( Storage\key( $kind, $id ), array() );
	$layout = validate( $stored );
	$revision = is_array( $stored ) && is_int( $stored['revision'] ?? null ) ? max( 0, $stored['revision'] ) : 0;
	return array( 'layout' => $layout ?? array( 'version' => 1, 'enabled' => false, 'rows' => array() ), 'revision' => $revision );
}

function active( string $kind ): bool {
	if ( ! in_array( $kind, array( 'header', 'footer' ), true ) || ! apply_filters( 'radmanct/builder/allowed', true, $kind ) ) {
		return false;
	}
	if ( is_singular( 'page' ) && 'elementor_canvas' === get_page_template_slug( get_queried_object_id() ) ) {
		return false;
	}
	$id = selection( $kind );
	return 'none' === $id || (bool) get_theme_mod( Storage\key( $kind, $id ), false );
}

function selection( string $kind ): string {
	if ( is_singular( 'page' ) || is_home() ) {
		$id = get_post_meta( get_queried_object_id(), '_radman_' . $kind . '_layout', true );
		if ( 'none' === $id || ( Storage\valid_id( $id ) && Storage\exists( $kind, $id ) ) ) {
			return (string) apply_filters( 'radmanct/builder/selected', $id, $kind );
		}
	}
	$defaults = get_theme_mod( 'radman_builder_defaults', array() );
	$defaults = is_array( $defaults ) ? $defaults : array();
	$lang = apply_filters( 'wpml_current_language', null );
	$id = is_string( $lang ) ? ( $defaults[ $kind ][ $lang ] ?? null ) : null;
	$id = $id ?? ( $defaults[ $kind ]['all'] ?? 'default' );
	$id = 'none' === $id || ( Storage\valid_id( $id ) && Storage\exists( $kind, $id ) ) ? $id : ( Storage\exists( $kind, 'default' ) ? 'default' : 'none' );
	return (string) apply_filters( 'radmanct/builder/selected', $id, $kind );
}

function translate( string $value, string $kind, string $id, string $property, string $layout_id = 'default' ): string {
	if ( function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\owns_current_language' ) && \RadmanCT\Integrations\Wpml\Builder\owns_current_language( $kind, $layout_id ) ) { return $value; }
	return (string) apply_filters( 'wpml_translate_single_string', $value, 'Radman Builder', Storage\string_key( $kind, $layout_id, $id, $property ) );
}
