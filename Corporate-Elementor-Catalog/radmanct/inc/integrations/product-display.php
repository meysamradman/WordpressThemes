<?php

namespace RadmanCT\Content\ProductDisplay;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/content-access.php';

function html( int $post_id ): string {
	if ( ! \RadmanCT\Content\Access\allowed( $post_id, true ) ) { return ''; }
	$pricing = \RadmanCT\Content\data( $post_id )['pricing'] ?? array( 'mode' => 'contact', 'text' => '' );
	if ( 'price' === $pricing['mode'] ) {
		$text = trim( $pricing['text'] );
		return '' === $text ? '' : '<p class="rdct-prd-price"><span>' . esc_html__( 'Price', 'radmanct' ) . '</span> <bdi>' . esc_html( $text ) . '</bdi></p>';
	}
	$url = function_exists( '\\RadmanCTContent\\Catalog\\contact_url' ) ? \RadmanCTContent\Catalog\contact_url() : ( function_exists( '\\RadmanCTContent\\Catalog\\inquiry_url' ) ? \RadmanCTContent\Catalog\inquiry_url( $post_id ) : '' );
	return $url ? '<p class="rdct-prd-contact"><a class="rdct-button" href="' . esc_url( $url ) . '">' . esc_html__( 'Consultation and price inquiry', 'radmanct' ) . '</a></p>' : '';
}
