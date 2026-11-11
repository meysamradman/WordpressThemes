<?php

namespace RadmanCTChild\Blog;

defined( 'ABSPATH' ) || exit;

function card_assets(): void {
	\RadmanCT\Assets\style( 'rdct-client-blog-cards', 'src/css/blog/cards.css', 'child', array( 'radmanct-child' ) );
}

function fragment_styles( array $keys ): void {
	if ( 'blog' !== ( $_GET['_rdct_fragment'] ?? null ) ) { return; }
	foreach ( $keys as $key ) {
		$asset = \RadmanCT\Assets\entry( $key, 'child' );
		if ( $asset ) { echo '<link rel="stylesheet" href="' . esc_url( \RadmanCT\Assets\url( $asset['file'], 'child' ) ) . '">'; }
	}
}
