<?php

namespace RadmanCT\Content\Access;

defined( 'ABSPATH' ) || exit;

function allowed( int $post_id, bool $product = false ): bool {
	if ( $post_id < 1 ) { return false; }
	$type = get_post_type( $post_id );
	$module = array( 'radman_product' => 'catalog', 'radman_portfolio' => 'portfolio' )[ $type ] ?? '';
	if ( ! $module || ( $product && 'catalog' !== $module ) || ! function_exists( '\\RadmanCTContent\\Modules\\enabled' ) || ! \RadmanCTContent\Modules\enabled( $module ) || post_password_required( $post_id ) ) { return false; }
	$status = get_post_status( $post_id );
	if ( 'publish' === $status ) { return true; }
	return in_array( $status, array( 'draft', 'pending', 'future', 'private', 'auto-draft' ), true ) && is_preview() && is_singular( $type ) && $post_id === (int) get_queried_object_id() && current_user_can( 'edit_post', $post_id );
}
