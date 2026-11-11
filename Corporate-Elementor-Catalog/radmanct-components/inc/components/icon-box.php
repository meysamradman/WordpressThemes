<?php

namespace RadmanCTComponents\Components\IconBox;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/image-media.php';

function fields(): array {
	return array(
		'layout' => array( 'label' => __( 'Layout', 'radmanct-components' ), 'default' => 'top', 'options' => array( 'top' => __( 'Icon on top', 'radmanct-components' ), 'inline' => __( 'Icon and heading inline', 'radmanct-components' ), 'side' => __( 'Icon and content inline', 'radmanct-components' ) ) ),
		'icon_type' => array( 'label' => __( 'Icon type', 'radmanct-components' ), 'default' => 'icon', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'icon' => __( 'Icon', 'radmanct-components' ), 'image' => __( 'Image', 'radmanct-components' ) ) ),
		'icon' => array( 'label' => __( 'Icon', 'radmanct-components' ), 'default' => 'check' ),
		'image_id' => array( 'label' => __( 'Image', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX ),
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => __( 'Service title', 'radmanct-components' ) ),
		'title_tag' => array( 'label' => __( 'Title HTML tag', 'radmanct-components' ), 'default' => 'h3', 'options' => array_combine( array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' ), array( 'H2', 'H3', 'H4', 'H5', 'H6', 'P', 'DIV' ) ) ),
		'text' => array( 'label' => __( 'Description', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true ),
		'url' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '' ),
		'link_text' => array( 'label' => __( 'Link text', 'radmanct-components' ), 'default' => '' ),
		'new_tab' => array( 'label' => __( 'Open in new tab', 'radmanct-components' ), 'default' => false ),
		'nofollow' => array( 'label' => __( 'Nofollow', 'radmanct-components' ), 'default' => false ),
		'align' => array( 'label' => __( 'Text alignment', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ), 'justify' => __( 'Justify', 'radmanct-components' ) ) ),
		'icon_side' => array( 'label' => __( 'Icon side', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'vertical_align' => array( 'label' => __( 'Vertical alignment', 'radmanct-components' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Default', 'radmanct-components' ), 'start' => __( 'Top', 'radmanct-components' ), 'center' => __( 'Middle', 'radmanct-components' ), 'end' => __( 'Bottom', 'radmanct-components' ) ) ),
		'shape' => array( 'label' => __( 'Icon shape', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'square' => __( 'Square', 'radmanct-components' ), 'circle' => __( 'Circle', 'radmanct-components' ), 'lozenge' => __( 'Lozenge', 'radmanct-components' ) ) ),
		'animation' => array( 'label' => __( 'SVG animation', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'spin' => __( 'Rotate', 'radmanct-components' ), 'float' => __( 'Float', 'radmanct-components' ), 'pulse' => __( 'Pulse', 'radmanct-components' ), 'draw' => __( 'Draw strokes', 'radmanct-components' ) ) ),
		'animation_target' => array( 'label' => __( 'Animation target', 'radmanct-components' ), 'default' => 'whole', 'options' => array( 'whole' => __( 'Whole icon', 'radmanct-components' ), 'parts' => __( 'SVG parts', 'radmanct-components' ) ) ),
		'animation_trigger' => array( 'label' => __( 'Animation trigger', 'radmanct-components' ), 'default' => 'hover', 'options' => array( 'hover' => __( 'Hover and focus', 'radmanct-components' ), 'always' => __( 'Always', 'radmanct-components' ) ) ),
		'animation_duration' => array( 'label' => __( 'Animation duration', 'radmanct-components' ), 'default' => 2, 'min' => 0.2, 'max' => 20 ),
		'icon_size' => array( 'label' => __( 'Icon size', 'radmanct-components' ), 'default' => 40, 'min' => 8, 'max' => 128 ),
		'icon_padding' => array( 'label' => __( 'Icon padding', 'radmanct-components' ), 'default' => 16, 'min' => 0, 'max' => 80 ),
		'border_width' => array( 'label' => __( 'Border width', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 8 ),
		'gap' => array( 'label' => __( 'Gap', 'radmanct-components' ), 'default' => 20, 'min' => 0, 'max' => 120 ),
		'title_gap' => array( 'label' => __( 'Title spacing', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 100 ),
		'icon_color' => array( 'label' => __( 'Icon color', 'radmanct-components' ), 'default' => '#176a6b', 'color' => true ),
		'icon_background' => array( 'label' => __( 'Icon background', 'radmanct-components' ), 'default' => '#f2f4f7', 'color' => true ),
		'border_color' => array( 'label' => __( 'Border color', 'radmanct-components' ), 'default' => '#d7e0e7', 'color' => true ),
		'hover_color' => array( 'label' => __( 'Hover icon color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true ),
		'hover_background' => array( 'label' => __( 'Hover icon background', 'radmanct-components' ), 'default' => '#176a6b', 'color' => true ),
	);
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ? $value : $field['default']; }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( 'url' === $key ) { $value = is_string( $value ) ? esc_url_raw( substr( $value, 0, 2048 ), array( 'http', 'https', 'mailto', 'tel' ) ) : ''; }
		elseif ( 'text' === $key ) { $value = is_string( $value ) ? wp_kses_post( substr( $value, 0, 16000 ) ) : ''; }
		else { $value = is_string( $value ) ? sanitize_text_field( substr( $value, 0, 500 ) ) : $field['default']; }
		$result[ $key ] = $value;
	}
	return $result;
}

function has_media( array $s ): bool {
	if ( 'image' === $s['icon_type'] ) {
		$id = (int) apply_filters( 'wpml_object_id', (int) $s['image_id'], 'attachment', true );
		return $id > 0 && wp_attachment_is_image( $id );
	}
	if ( 'icon' !== $s['icon_type'] || '' === $s['icon'] ) { return false; }
	require_once dirname( __DIR__ ) . '/icons.php';
	return isset( \RadmanCTComponents\Icons\catalog()[ $s['icon'] ] );
}

function css( array $s ): string {
	$css = '';
	foreach ( fields() as $key => $field ) {
		if ( ! empty( $field['color'] ) || ( isset( $field['min'] ) && 'image_id' !== $key ) ) {
			$css .= '--rdct-ib-' . str_replace( '_', '-', $key ) . ':' . $s[ $key ] . ( 'animation_duration' === $key ? 's' : ( isset( $field['min'] ) ? 'px' : '' ) ) . ';';
		}
	}
	return $css;
}

function render( array $input ): string {
	$s = settings( $input );
	if ( '' === trim( $s['title'] ) ) { return ''; }
	$icon = '';
	if ( 'image' === $s['icon_type'] ) {
		$id = (int) apply_filters( 'wpml_object_id', (int) $s['image_id'], 'attachment', true );
		if ( $id && 'none' !== $s['animation'] ) { require_once __DIR__ . '/svg-image.php'; $icon = \RadmanCTComponents\Components\SvgImage\render( $id ); }
		if ( ! $icon && $id && wp_attachment_is_image( $id ) ) { $icon = wp_get_attachment_image( $id, \RadmanCTComponents\Components\ImageMedia\size( $input['image'] ?? null, 'thumbnail' ), false, array( 'class' => 'rdct-ib__image', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ); }
	} elseif ( 'icon' === $s['icon_type'] && '' !== $s['icon'] ) {
		require_once dirname( __DIR__ ) . '/icons.php';
		$icon = \RadmanCTComponents\Icons\render( $s['icon'], array( 'size' => $s['icon_size'] ) );
	}
	if ( 'none' !== $s['animation'] ) { $icon = preg_replace( '/<(path|circle|rect|line|polyline|polygon|ellipse)\b([^>]*)>/i', '<$1 pathLength="100"$2>', preg_replace( '/\s+pathLength="[^"]*"/i', '', $icon ) ); }
	if ( ! $icon ) { $s['animation'] = 'none'; }
	$rel = implode( ' ', array_filter( array( $s['new_tab'] ? 'noopener' : '', $s['nofollow'] ? 'nofollow' : '' ) ) );
	$attributes = ' href="' . esc_url( $s['url'] ) . '"' . ( $s['new_tab'] ? ' target="_blank"' : '' ) . ( $rel ? ' rel="' . esc_attr( $rel ) . '"' : '' );
	$title = esc_html( $s['title'] );
	if ( $s['url'] ) { $title = '<a' . $attributes . '>' . $title . '</a>'; }
	$html = '<div class="rdct-ib rdct-ib--' . $s['layout'] . ' rdct-ib--' . $s['align'] . ' rdct-ib--' . $s['shape'] . ' rdct-ib--motion-' . $s['animation'] . ' rdct-ib--target-' . $s['animation_target'] . ' rdct-ib--trigger-' . $s['animation_trigger'] . '" style="' . esc_attr( css( $s ) ) . '">';
	if ( $icon ) { $html .= '<span class="rdct-ib__icon" aria-hidden="true">' . $icon . '</span>'; }
	$html .= '<' . $s['title_tag'] . ' class="rdct-ib__title">' . $title . '</' . $s['title_tag'] . '>';
	$content = $s['text'];
	if ( $s['url'] && $s['link_text'] ) { $content .= '<a class="rdct-ib__link"' . $attributes . '>' . esc_html( $s['link_text'] ) . '</a>'; }
	if ( '' !== trim( $content ) ) { $html .= '<div class="rdct-ib__content">' . $content . '</div>'; }
	return \RadmanCTComponents\Components\Presentation\render( $html . '</div>', $input, 'icon-box' );
}
