<?php

namespace RadmanCTContent\WPML;

defined( 'ABSPATH' ) || exit;

function &pending(): array {
	static $items = array();
	return $items;
}

function titles( $fields ) {
	if ( ! is_array( $fields ) ) { return $fields; }
	$labels = array( 'label' => __( 'Specification label', 'radmanct-content' ), 'value' => __( 'Specification value', 'radmanct-content' ), 'unit' => __( 'Specification unit', 'radmanct-content' ), 'text' => __( 'Displayed price', 'radmanct-content' ) );
	foreach ( $fields as &$field ) {
		if ( ! is_array( $field ) || ! is_string( $field['field_type'] ?? null ) ) { continue; }
		if ( preg_match( '/^field-_radman_content_data-\d+-specifications-(\d+)-(label|value|unit)$/D', $field['field_type'], $match ) && (int) $match[1] < 40 ) {
			$field['title'] = $labels[ $match[2] ] . ' (' . ( (int) $match[1] + 1 ) . ')';
		} elseif ( preg_match( '/^field-_radman_content_data-\d+-pricing-text$/D', $field['field_type'] ) ) { $field['title'] = $labels['text']; }
	}
	unset( $field );
	return $fields;
}

function capture( $post, $job ) {
	if ( ! is_array( $post ) || ! is_object( $job ) ) { return $post; }
	$id = (int) ( $post['ID'] ?? 0 );
	$source = (int) ( $job->original_doc_id ?? 0 );
	$type = $post['post_type'] ?? '';
	if ( $id < 1 || $source < 1 || $id === $source || ! in_array( $type, \RadmanCTContent\Modules\types(), true ) || get_post_type( $id ) !== $type || get_post_type( $source ) !== $type ) { return $post; }
	$items =& pending();
	unset( $items[ $id ] );
	if ( ! metadata_exists( 'post', $id, \RadmanCTContent\Model\META ) ) { return $post; }
	$previous = \RadmanCTContent\Model\validate( get_post_meta( $id, \RadmanCTContent\Model\META, true ) );
	if ( null !== $previous ) {
		$items[ $id ] = array( 'source' => $source, 'language' => (string) ( $job->language_code ?? '' ), 'data' => $previous );
	}
	return $post;
}

function merge( array $previous, $translated ): array {
	$translated = \RadmanCTContent\Model\validate( $translated );
	if ( null === $translated ) { return $previous; }
	$rows = array();
	foreach ( $translated['specifications'] as $row ) { $rows[ $row['id'] ] = $row; }
	foreach ( $previous['specifications'] as &$row ) {
		if ( isset( $rows[ $row['id'] ] ) ) { $row = $rows[ $row['id'] ]; unset( $rows[ $row['id'] ] ); }
	}
	unset( $row );
	foreach ( $rows as $row ) {
		if ( count( $previous['specifications'] ) >= 40 ) { break; }
		$previous['specifications'][] = $row;
	}
	$previous['pricing']['text'] = $translated['pricing']['text'];
	return $previous;
}

function complete( $post_id, $fields, $job ): void {
	$id = (int) $post_id;
	$items =& pending();
	$entry = $items[ $id ] ?? null;
	unset( $items[ $id ] );
	if ( ! $entry || ! is_object( $job ) || $entry['source'] !== (int) ( $job->original_doc_id ?? 0 ) || $entry['language'] !== (string) ( $job->language_code ?? '' ) || ! in_array( get_post_type( $id ), \RadmanCTContent\Modules\types(), true ) ) { return; }
	$translated = get_post_meta( $id, \RadmanCTContent\Model\META, true );
	$value = merge( $entry['data'], $translated );
	if ( $value !== $translated ) { update_post_meta( $id, \RadmanCTContent\Model\META, wp_slash( $value ) ); }
}

add_filter( 'wpml_pre_save_pro_translation', __NAMESPACE__ . '\\capture', 20, 2 );
add_action( 'wpml_pro_translation_completed', __NAMESPACE__ . '\\complete', 20, 3 );
add_filter( 'wpml_tm_adjust_translation_fields', __NAMESPACE__ . '\\titles', 20 );
