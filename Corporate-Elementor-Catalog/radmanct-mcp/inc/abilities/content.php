<?php

namespace RadmanCT\Mcp\Content;

use RadmanCT\Mcp\Integrations\Content as ContentModule;

defined( 'ABSPATH' ) || exit;

function types(): array {
	return array_merge( array( 'post', 'page' ), ContentModule\types() );
}

function error( string $message, string $code = 'radmanct_mcp_invalid' ): \WP_Error {
	return new \WP_Error( $code, $message );
}

function type( string $name ) {
	$object = in_array( $name, types(), true ) ? get_post_type_object( $name ) : null;
	return $object && $object->show_in_rest ? $object : null;
}

function route( $type ): string {
	return '/' . ( $type->rest_namespace ?: 'wp/v2' ) . '/' . ( $type->rest_base ?: $type->name );
}

function request( string $method, string $route, array $params, bool $collection = false ) {
	$request = new \WP_REST_Request( $method, $route );
	$request->set_query_params( $params );
	$response = rest_do_request( $request );
	if ( $response->is_error() ) { return $response->as_error(); }
	if ( ! $collection ) { return $response->get_data(); }
	$headers = $response->get_headers();
	return array( 'items' => $response->get_data(), 'page' => (int) ( $params['page'] ?? 1 ), 'total' => (int) ( $headers['X-WP-Total'] ?? 0 ), 'total_pages' => (int) ( $headers['X-WP-TotalPages'] ?? 0 ) );
}

function taxonomies( string $type ): array {
	return array_filter( get_object_taxonomies( $type, 'objects' ), static function ( $tax ): bool { return (bool) $tax->show_in_rest; } );
}

function snapshot( int $id ) {
	$post = get_post( $id );
	if ( ! $post || ! type( $post->post_type ) || ! current_user_can( 'edit_post', $id ) || 'trash' === $post->post_status ) { return error( 'Content is unavailable or not editable.', 'radmanct_mcp_forbidden' ); }
	$terms = array();
	foreach ( taxonomies( $post->post_type ) as $name => $tax ) {
		$ids = wp_get_object_terms( $id, $name, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $ids ) ) { return $ids; }
		sort( $ids ); $terms[ $tax->rest_base ?: $name ] = $ids;
	}
	$details = ContentModule\read( $id );
	$fields = array( 'title' => $post->post_title, 'content' => $post->post_content, 'excerpt' => $post->post_excerpt, 'status' => $post->post_status, 'slug' => $post->post_name, 'featured_media' => get_post_thumbnail_id( $id ), 'template' => get_page_template_slug( $id ) ?: '' ) + $terms;
	return array( 'id' => $id, 'type' => $post->post_type, 'owner' => \RadmanCT\Mcp\Integrations\Elementor\owns( $id ) ? 'elementor' : 'wordpress', 'version' => hash( 'sha256', serialize( array( $post->post_modified_gmt, $fields, $details ) ) ), 'fields' => $fields, 'details' => $details, 'url' => get_permalink( $id ), 'edit_url' => get_edit_post_link( $id, 'raw' ) );
}

function listing( array $input ) {
	$type = type( $input['type'] );
	if ( ! $type ) { return error( 'Content module is unavailable.' ); }
	return request( 'GET', route( $type ), array( 'context' => 'edit', 'per_page' => $input['per_page'] ?? 10, 'page' => $input['page'] ?? 1, 'search' => $input['search'] ?? '', '_fields' => 'id,type,status,title,modified_gmt,link' ), true );
}

function media( array $input ) {
	return request( 'GET', '/wp/v2/media', array( 'context' => 'edit', 'media_type' => 'image', 'per_page' => $input['per_page'] ?? 10, 'page' => $input['page'] ?? 1, 'search' => $input['search'] ?? '', '_fields' => 'id,title,alt_text,mime_type,source_url,media_details.width,media_details.height,media_details.sizes.thumbnail' ), true );
}

function terms( array $input ) {
	$type = type( $input['type'] );
	$tax = $type ? ( taxonomies( $type->name )[ $input['taxonomy'] ] ?? null ) : null;
	if ( ! $tax || ! current_user_can( $tax->cap->assign_terms ) ) { return error( 'Taxonomy is unavailable.', 'radmanct_mcp_forbidden' ); }
	return request( 'GET', '/' . ( $tax->rest_namespace ?: 'wp/v2' ) . '/' . ( $tax->rest_base ?: $tax->name ), array( 'per_page' => 40, 'page' => $input['page'] ?? 1, 'search' => $input['search'] ?? '', 'hide_empty' => false, '_fields' => 'id,name,slug,parent' ), true );
}

function save( array $input ) {
	$id = $input['id'] ?? 0;
	$old = $id ? snapshot( $id ) : null;
	if ( is_wp_error( $old ) ) { return $old; }
	$type = type( $old['type'] ?? $input['type'] ?? '' );
	if ( ! $type ) { return error( 'Content module is unavailable.' ); }
	if ( $id && ! hash_equals( $old['version'], $input['version'] ?? '' ) ) { return error( 'Content changed. Read it again before saving.', 'radmanct_mcp_conflict' ); }
	if ( $id ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
		if ( wp_check_post_lock( $id ) ) { return error( 'Another user is editing this content.', 'radmanct_mcp_conflict' ); }
	}
	$fields = $input['fields'];
	$allowed = array( 'title', 'content', 'excerpt', 'slug', 'status', 'featured_media', 'template' );
	foreach ( taxonomies( $type->name ) as $name => $tax ) { $allowed[] = $tax->rest_base ?: $name; }
	if ( array_diff( array_keys( $fields ), $allowed ) ) { return error( 'Unsupported content fields.' ); }
	if ( isset( $fields['content'] ) && 'elementor' === ( $old['owner'] ?? '' ) ) { return error( 'Use the official Elementor abilities to change this document content.', 'radmanct_mcp_owner' ); }
	foreach ( array( 'content', 'excerpt' ) as $key ) { if ( isset( $fields[ $key ] ) ) { $fields[ $key ] = wp_kses_post( $fields[ $key ] ); } }
	$details = $input['details'] ?? null;
	if ( null !== $details ) {
		$details = ContentModule\validate( $type->name, $details );
		if ( is_wp_error( $details ) ) { return $details; }
	}
	if ( ! $id ) { $fields += array( 'status' => 'draft' ); }
	$result = request( 'POST', route( $type ) . ( $id ? '/' . $id : '' ), $fields );
	if ( is_wp_error( $result ) ) { return $result; }
	$id = (int) $result['id'];
	if ( null !== $details ) {
		$saved = ContentModule\save( $id, $details );
		if ( is_wp_error( $saved ) ) { return $saved; }
	}
	return snapshot( $id );
}
