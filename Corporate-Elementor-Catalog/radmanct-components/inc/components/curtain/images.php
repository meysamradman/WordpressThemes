<?php

namespace RadmanCTComponents\Components\Curtain;

defined( 'ABSPATH' ) || exit;

function render_images( array $items, array $settings ): string {
	$id = wp_unique_id( 'rdct-image-panels-' );
	$style = '';
	$style .= '--rdct-curtain-ratio:' . $settings['expanded_ratio'] . ';';
	foreach ( array( 'gap', 'height', 'image_radius' ) as $key ) { $style .= '--rdct-curtain-' . str_replace( '_', '-', $key ) . ':' . $settings[ $key ] . 'px;'; }
	$html = '<div class="rdct-curtain rdct-curtain--images" data-rdct-curtain data-layout="images" data-active-item="' . min( count( $items ), $settings['active_item'] ) . '" data-trigger="' . esc_attr( $settings['trigger'] ) . '" data-ratio="' . $settings['expanded_ratio'] . '" data-duration="' . $settings['duration'] . '" style="' . esc_attr( $style ) . '">';
	foreach ( $items as $index => $item ) {
		$tag = $settings['heading_tag'];
		$label = $id . '-' . $index;
		$content = $item['content'];
		if ( $item['custom_content'] ) { $content = \RadmanCTComponents\Components\CustomContent\render( $item['custom_content'], array( 'slot' => 'curtain-images' ) ); }
		elseif ( 'template' === $item['content_type'] ) { require_once dirname( __DIR__ ) . '/template-library.php'; $content = \RadmanCTComponents\Components\TemplateLibrary\render( $item['template_id'] ) ?: $content; }
		$image_key = $item['background_id'] ? 'background_id' : 'image_id';
		$html .= '<' . $settings['item_tag'] . ' class="rdct-curtain__item" style="' . esc_attr( item_css( $item ) ) . '"><div class="rdct-curtain__visual">' . image( $item[ $image_key ], 'rdct-curtain__background', $item[ $image_key . '_size' ] ) . '<button type="button" class="rdct-curtain__trigger" aria-pressed="false" aria-labelledby="' . esc_attr( $label ) . '"></button></div><div class="rdct-curtain__panel"><' . $tag . ' class="rdct-curtain__title" id="' . esc_attr( $label ) . '">' . esc_html( $item['title'] ) . '</' . $tag . '><div class="rdct-curtain__text">' . $content . '</div></div></' . $settings['item_tag'] . '>';
	}
	return $html . '</div>';
}
