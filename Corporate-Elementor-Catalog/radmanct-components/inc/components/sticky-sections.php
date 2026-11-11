<?php

namespace RadmanCTComponents\Components\StickySections;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/values.php';
require_once __DIR__ . '/custom-content.php';

function fields(): array {
	return array(
		'mode' => array( 'label' => __( 'Sticky effect', 'radmanct-components' ), 'default' => 'stack', 'options' => array( 'stack' => __( 'Stacked cards', 'radmanct-components' ), 'overlap' => __( 'Overlapping sections', 'radmanct-components' ), 'services' => __( 'Pinned service panels', 'radmanct-components' ), 'zoom' => __( 'Zooming cards', 'radmanct-components' ), 'split' => __( 'Fixed column and scrolling content', 'radmanct-components' ) ) ),
		'transition' => array( 'label' => __( 'Panel transition', 'radmanct-components' ), 'default' => 'pin', 'options' => array( 'pin' => __( 'Pin only', 'radmanct-components' ), 'slide-scale' => __( 'Slide and scale', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'services' ) ) ),
		'aside_position' => array( 'label' => __( 'Fixed column position', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'split' ) ) ),
		'aside_width' => array( 'label' => __( 'Fixed column width (%)', 'radmanct-components' ), 'default' => 35, 'min' => 20, 'max' => 60, 'condition' => array( 'mode' => array( 'split' ) ) ),
		'aside_type' => array( 'label' => __( 'Fixed column content', 'radmanct-components' ), 'default' => 'text', 'options' => array( 'text' => __( 'Text editor', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'split' ) ) ),
		'aside_content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true, 'condition' => array( 'mode' => array( 'split' ), 'aside_type' => array( 'text' ) ) ),
		'aside_template_id' => array( 'label' => __( 'Select template', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'template' => true, 'condition' => array( 'mode' => array( 'split' ), 'aside_type' => array( 'template' ) ) ),
		'offset' => array( 'label' => __( 'Top offset', 'radmanct-components' ), 'default' => 30, 'min' => 0, 'max' => 300 ),
		'spacer' => array( 'label' => __( 'Layer spacing', 'radmanct-components' ), 'default' => 30, 'min' => 0, 'max' => 120, 'condition' => array( 'mode' => array( 'stack' ) ) ),
		'min_scale' => array( 'label' => __( 'Minimum scale', 'radmanct-components' ), 'default' => 0.8, 'min' => 0.5, 'max' => 1, 'condition' => array( 'mode' => array( 'stack', 'zoom' ) ) ),
		'min_width' => array( 'label' => __( 'Minimum screen width', 'radmanct-components' ), 'default' => 992, 'min' => 0, 'max' => 1920 ),
		'gap' => array( 'label' => __( 'Items gap', 'radmanct-components' ), 'default' => 40, 'min' => 0, 'max' => 200 ),
		'height' => array( 'label' => __( 'Minimum height', 'radmanct-components' ), 'default' => 420, 'min' => 120, 'max' => 1200 ),
		'padding' => array( 'label' => __( 'Content padding', 'radmanct-components' ), 'default' => 40, 'min' => 0, 'max' => 120 ),
		'radius' => array( 'label' => __( 'Border radius', 'radmanct-components' ), 'default' => 20, 'min' => 0, 'max' => 160 ),
	);
}

function item_fields(): array {
	return array(
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => __( 'Section title', 'radmanct-components' ) ),
		'content_type' => array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'text', 'options' => array( 'text' => __( 'Text editor', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ) ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true, 'condition' => array( 'content_type' => 'text' ) ),
		'template_id' => array( 'label' => __( 'Select template', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'template' => true, 'condition' => array( 'content_type' => 'template' ) ),
		'image_id' => array( 'label' => __( 'Image', 'radmanct-components' ), 'default' => 0, 'media' => true ),
		'layout' => array( 'label' => __( 'Layout', 'radmanct-components' ), 'default' => 'split', 'options' => array( 'split' => __( 'Text and image', 'radmanct-components' ), 'content' => __( 'Content only', 'radmanct-components' ), 'image' => __( 'Image background', 'radmanct-components' ) ) ),
		'image_position' => array( 'label' => __( 'Image position', 'radmanct-components' ), 'default' => 'end', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'background' => array( 'label' => __( 'Background color', 'radmanct-components' ), 'default' => '#e9f1fc', 'color' => true ),
		'color' => array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true ),
	);
}

function settings( array $input ): array { return \RadmanCTComponents\Components\Values\fields( $input, fields() ); }

function items( array $input ): array {
	$result = array();
	foreach ( array_slice( $input, 0, 12 ) as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		if ( is_array( $row['image_id'] ?? null ) ) { $row['image_id'] = (int) ( $row['image_id']['id'] ?? 0 ); }
		$item = \RadmanCTComponents\Components\Values\fields( $row, item_fields() );
		$item['custom_content'] = \RadmanCTComponents\Components\CustomContent\key( $row );
		if ( '' !== trim( $item['title'] ) || $item['content'] || $item['image_id'] || $item['template_id'] || $item['custom_content'] ) { $result[] = $item; }
	}
	return $result;
}

function render( array $input, array $options = array(), ?callable $aside_renderer = null, ?callable $item_renderer = null ): string {
	$rows = items( $input ); if ( ! $rows ) { return ''; }
	$custom_item = $item_renderer && true === ( $options['custom_item'] ?? false );
	$s = settings( $options ); $style = '';
	$style .= '--rdct-ss-aside-width:' . $s['aside_width'] . '%;';
	foreach ( array( 'offset', 'spacer', 'gap', 'height', 'padding', 'radius' ) as $key ) { $style .= '--rdct-ss-' . $key . ':' . $s[ $key ] . 'px;'; }
	$html = '<div class="rdct-ss" data-rdct-sticky-sections data-rdct-native-scroll data-mode="' . $s['mode'] . '" data-transition="' . $s['transition'] . '" data-offset="' . $s['offset'] . '" data-spacer="' . $s['spacer'] . '" data-min-scale="' . $s['min_scale'] . '" data-min-width="' . $s['min_width'] . '" style="' . esc_attr( $style ) . '">';
	if ( 'split' === $s['mode'] ) {
		$aside = $s['aside_content'];
		if ( $aside_renderer ) { ob_start(); try { $aside_renderer(); $aside = (string) ob_get_contents(); } finally { ob_end_clean(); } }
		elseif ( \RadmanCTComponents\Components\CustomContent\key( array( 'custom_content' => $options['aside_custom_content'] ?? '' ) ) ) { $aside = \RadmanCTComponents\Components\CustomContent\render( $options['aside_custom_content'], array( 'slot' => 'sticky-aside' ) ); }
		elseif ( 'template' === $s['aside_type'] ) { require_once __DIR__ . '/template-library.php'; $aside = \RadmanCTComponents\Components\TemplateLibrary\render( $s['aside_template_id'] ) ?: $aside; }
		$html .= '<div class="rdct-ss__aside rdct-ss__aside--' . $s['aside_position'] . '"><div class="rdct-ss__fixed">' . $aside . '</div></div><div class="rdct-ss__list">';
	}
	foreach ( $rows as $index => $row ) {
		$content = $row['content'];
		if ( $item_renderer ) { ob_start(); try { $item_renderer( $index, $row ); $content = (string) ob_get_contents(); } finally { ob_end_clean(); } }
		elseif ( $row['custom_content'] ) { $content = \RadmanCTComponents\Components\CustomContent\render( $row['custom_content'], array( 'slot' => 'sticky-item', 'index' => $index ) ); }
		elseif ( 'template' === $row['content_type'] ) { require_once __DIR__ . '/template-library.php'; $content = \RadmanCTComponents\Components\TemplateLibrary\render( $row['template_id'] ) ?: $content; }
		$html .= '<section class="rdct-ss__item" style="--rdct-ss-index:' . $index . ';--rdct-ss-bg:' . $row['background'] . ';--rdct-ss-color:' . $row['color'] . '">';
		if ( $custom_item ) { $html .= $content . '</section>'; continue; }
		$image = ''; $image_id = (int) apply_filters( 'wpml_object_id', $row['image_id'], 'attachment', true );
		if ( $image_id && wp_attachment_is_image( $image_id ) ) { $image = wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'rdct-ss__image', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); }
		$html .= '<div class="rdct-ss__inner rdct-ss__inner--' . $row['layout'] . ' rdct-ss__inner--image-' . $row['image_position'] . '">';
		$html .= '<div class="rdct-ss__content">' . ( $row['title'] ? '<h2 class="rdct-ss__title">' . esc_html( $row['title'] ) . '</h2>' : '' ) . $content . '</div>';
		if ( $image ) { $html .= '<div class="rdct-ss__media">' . $image . '</div>'; }
		$html .= '</div></section>';
	}
	return $html . ( 'split' === $s['mode'] ? '</div>' : '' ) . '</div>';
}
