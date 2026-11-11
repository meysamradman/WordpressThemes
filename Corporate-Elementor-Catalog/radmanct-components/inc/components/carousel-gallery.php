<?php

namespace RadmanCTComponents\Components\Carousel;

defined( 'ABSPATH' ) || exit;

function gallery_items( $images ): array {
	$items = array();
	foreach ( is_array( $images ) ? array_slice( $images, 0, 50 ) : array() as $image ) {
		$id = is_array( $image ) ? ( $image['id'] ?? null ) : $image;
		if ( ! is_int( $id ) && ! ( is_string( $id ) && ctype_digit( $id ) ) ) { continue; }
		$id = (int) $id;
		if ( $id > 0 && ! isset( $items[ $id ] ) ) { $items[ $id ] = array( 'image_id' => $id, 'content_type' => 'content' ); }
	}
	return array_values( $items );
}
