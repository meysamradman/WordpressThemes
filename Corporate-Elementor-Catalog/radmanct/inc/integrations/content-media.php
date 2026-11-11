<?php

namespace RadmanCT\Content\Media;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-access.php';

function ids( int $post_id ): array {
	if ( ! \RadmanCT\Content\Access\allowed( $post_id ) ) { return array(); }
	$ids = \RadmanCT\Content\gallery_ids( $post_id );
	$featured = (int) get_post_thumbnail_id( $post_id );
	if ( $featured ) {
		$featured = (int) apply_filters( 'wpml_object_id', $featured, 'attachment', true );
		if ( $featured && wp_attachment_is_image( $featured ) ) { array_unshift( $ids, $featured ); }
	}
	return array_values( array_unique( $ids ) );
}

function render( int $post_id ): void {
	if ( ! \RadmanCT\Content\Access\allowed( $post_id ) ) { return; }
	$class = 'radman_product' === get_post_type( $post_id ) ? 'rdct-prd-media' : 'rdct-prj-media';
	$links = array();
	foreach ( ids( $post_id ) as $id ) {
		$primary = ! $links;
		$link = \RadmanCT\Content\gallery_link( $id, $primary ? 'large' : 'thumbnail', $primary ? $class . '__image' : '' );
		if ( '' !== $link ) { $links[] = $link; }
	}
	if ( ! $links ) {
		echo '<div class="' . $class . '"><div class="' . $class . '__image rdct-image-placeholder" aria-hidden="true"></div></div>';
		return;
	}
	echo '<section class="' . $class . '" ' . \RadmanCT\Content\gallery_attributes() . '>' . array_shift( $links );
	if ( $links ) { echo '<div class="rdct-gal">' . implode( '', $links ) . '</div>'; }
	echo '</section>';
}
