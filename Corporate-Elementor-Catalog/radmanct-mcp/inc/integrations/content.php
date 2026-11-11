<?php

namespace RadmanCT\Mcp\Integrations\Content;

defined( 'ABSPATH' ) || exit;

function types(): array {
	return function_exists( '\\RadmanCTContent\\Modules\\types' ) ? array_values( array_filter( \RadmanCTContent\Modules\types(), 'post_type_exists' ) ) : array();
}

function read( int $id ): ?array {
	return in_array( get_post_type( $id ), types(), true ) && function_exists( '\\RadmanCTContent\\Model\\read' ) ? \RadmanCTContent\Model\read( $id ) : null;
}

function validate( string $type, array $details ) {
	if ( ! in_array( $type, types(), true ) || ! function_exists( '\\RadmanCTContent\\Model\\validate' ) || strlen( wp_json_encode( $details ) ) > 131072 ) { return new \WP_Error( 'radmanct_mcp_invalid', 'Details require an active content module.' ); }
	$result = \RadmanCTContent\Model\validate( $details );
	if ( ! $result ) { return new \WP_Error( 'radmanct_mcp_invalid', 'Invalid content details.' ); }
	foreach ( $result['gallery'] as $image ) { if ( ! wp_attachment_is_image( $image ) || ! current_user_can( 'read_post', $image ) ) { return new \WP_Error( 'radmanct_mcp_invalid', 'Gallery image is unavailable.' ); } }
	return $result;
}

function save( int $id, array $details ) {
	$old = read( $id );
	if ( null === $old || ( $old !== $details && ! update_post_meta( $id, \RadmanCTContent\Model\META, wp_slash( $details ) ) ) ) { return new \WP_Error( 'radmanct_mcp_partial', 'Content saved, but details could not be saved. Read the record before retrying.', array( 'id' => $id ) ); }
	if ( $old !== $details ) { wp_save_post_revision( $id ); }
	return true;
}
