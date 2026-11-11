<?php

namespace RadmanCT\Content\ProductMedia;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-media.php';

function ids( int $post_id ): array {
	if ( ! \RadmanCT\Content\Access\allowed( $post_id, true ) ) { return array(); }
	return \RadmanCT\Content\Media\ids( $post_id );
}

function render( int $post_id ): void {
	if ( ! \RadmanCT\Content\Access\allowed( $post_id, true ) ) {
		echo '<div class="rdct-prd-media"><div class="rdct-prd-media__image rdct-image-placeholder" aria-hidden="true"></div></div>';
		return;
	}
	\RadmanCT\Content\Media\render( $post_id );
}
