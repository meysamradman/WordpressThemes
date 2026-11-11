<?php

namespace RadmanLibrary\Catalog\Model;

defined( 'ABSPATH' ) || exit;

const META = '_radman_content_data';

function defaults(): array {
	return array( 'version' => 1, 'code' => '', 'gallery' => array(), 'specifications' => array(), 'pricing' => array( 'mode' => 'contact', 'text' => '' ) );
}

function validate( $value ): ?array {
	if ( ! is_array( $value ) || 1 !== ( $value['version'] ?? null ) || ! is_string( $value['code'] ?? null ) || strlen( $value['code'] ) > 160 || ! is_array( $value['gallery'] ?? null ) || ! is_array( $value['specifications'] ?? null ) || count( $value['gallery'] ) > 20 || count( $value['specifications'] ) > 40 || array_values( $value['gallery'] ) !== $value['gallery'] || array_values( $value['specifications'] ) !== $value['specifications'] ) {
		return null;
	}
	$result = defaults();
	$pricing = array_key_exists( 'pricing', $value ) ? $value['pricing'] : $result['pricing'];
	if ( ! is_array( $pricing ) || ! in_array( $pricing['mode'] ?? null, array( 'contact', 'price' ), true ) || ! is_string( $pricing['text'] ?? null ) || strlen( $pricing['text'] ) > 320 ) { return null; }
	$result['pricing'] = array( 'mode' => $pricing['mode'], 'text' => sanitize_text_field( $pricing['text'] ) );
	$result['code'] = sanitize_text_field( $value['code'] );
	foreach ( $value['gallery'] as $id ) {
		if ( ! is_int( $id ) || $id < 1 ) {
			return null;
		}
		$result['gallery'][] = $id;
	}
	$result['gallery'] = array_values( array_unique( $result['gallery'] ) );
	$ids = array();
	foreach ( $value['specifications'] as $row ) {
		if ( ! is_array( $row ) || ! is_string( $row['id'] ?? null ) || ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,47}$/D', $row['id'] ) || isset( $ids[ $row['id'] ] ) || ! is_string( $row['label'] ?? null ) || ! is_string( $row['value'] ?? null ) || ! is_string( $row['unit'] ?? null ) || strlen( $row['label'] ) > 400 || strlen( $row['value'] ) > 2000 || strlen( $row['unit'] ) > 100 ) {
			return null;
		}
		$ids[ $row['id'] ] = true;
		$result['specifications'][] = array( 'id' => $row['id'], 'label' => sanitize_text_field( $row['label'] ), 'value' => sanitize_textarea_field( $row['value'] ), 'unit' => sanitize_text_field( $row['unit'] ) );
	}
	return $result;
}

function read( int $post_id ): array {
	return validate( get_post_meta( $post_id, META, true ) ) ?? defaults();
}

function sanitize( $value ): array {
	return validate( $value ) ?? defaults();
}

function register( string $type ): void {
	register_post_meta( $type, META, array( 'single' => true, 'type' => 'object', 'show_in_rest' => false, 'default' => defaults(), 'revisions_enabled' => true, 'sanitize_callback' => __NAMESPACE__ . '\\sanitize', 'auth_callback' => static function ( $allowed, $key, $post_id ): bool { return current_user_can( 'edit_post', $post_id ); } ) );
}
