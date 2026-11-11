<?php

namespace RadmanCTComponents\Components\Curtain;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/custom-content.php';
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/image-media.php';

function fields(): array {
	return array(
		'layout' => array( 'label' => __( 'Panel layout', 'radmanct-components' ), 'default' => 'panels', 'options' => array( 'panels' => __( 'Expandable Panels', 'radmanct-components' ), 'images' => __( 'Image accordion', 'radmanct-components' ) ) ),
		'expanded_ratio' => array( 'label' => __( 'Expanded image ratio', 'radmanct-components' ), 'default' => 3, 'min' => 1, 'max' => 8, 'condition' => array( 'layout' => 'images' ) ),
		'image_radius' => array( 'label' => __( 'Image corner radius', 'radmanct-components' ), 'default' => 26, 'min' => 0, 'max' => 120, 'condition' => array( 'layout' => 'images' ) ),
		'active_item' => array( 'label' => __( 'Active item', 'radmanct-components' ), 'default' => 1, 'min' => 1, 'max' => 12 ),
		'trigger' => array( 'label' => __( 'Trigger type', 'radmanct-components' ), 'default' => 'click', 'options' => array( 'click' => __( 'Click', 'radmanct-components' ), 'hover' => __( 'Hover and focus', 'radmanct-components' ) ) ),
		'heading_tag' => array( 'label' => __( 'Title HTML tag', 'radmanct-components' ), 'default' => 'h3', 'options' => array( 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6' ) ),
		'item_tag' => array( 'label' => __( 'Items HTML tag', 'radmanct-components' ), 'default' => 'article', 'options' => array( 'div' => 'DIV', 'section' => 'SECTION', 'article' => 'ARTICLE', 'aside' => 'ASIDE' ) ),
		'gap' => array( 'label' => __( 'Items gap', 'radmanct-components' ), 'default' => 20, 'min' => 0, 'max' => 64 ),
		'height' => array( 'label' => __( 'Minimum height', 'radmanct-components' ), 'default' => 480, 'min' => 160, 'max' => 1000 ),
		'rail' => array( 'label' => __( 'Collapsed width', 'radmanct-components' ), 'default' => 88, 'min' => 48, 'max' => 160 ),
		'padding' => array( 'label' => __( 'Content padding', 'radmanct-components' ), 'default' => 32, 'min' => 0, 'max' => 120 ),
		'duration' => array( 'label' => __( 'Animation duration', 'radmanct-components' ), 'default' => 650, 'min' => 100, 'max' => 1500 ),
	);
}

function item_fields(): array {
	$fields = array(
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => __( 'Title', 'radmanct-components' ) ),
		'content_type' => array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'text', 'options' => array( 'text' => __( 'Text editor', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ) ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true, 'condition' => array( 'content_type' => 'text' ) ),
		'template_id' => array( 'label' => __( 'Select template', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'template' => true, 'condition' => array( 'content_type' => 'template' ) ),
		'icon' => array( 'label' => __( 'Icon', 'radmanct-components' ), 'default' => '' ),
		'icon_size' => array( 'label' => __( 'Icon size', 'radmanct-components' ), 'default' => 28, 'min' => 8, 'max' => 128 ),
		'icon_position' => array( 'label' => __( 'Icon position', 'radmanct-components' ), 'default' => 'before', 'options' => array( 'before' => __( 'Before', 'radmanct-components' ), 'after' => __( 'After', 'radmanct-components' ) ) ),
		'image_id' => array( 'label' => __( 'Image or logo', 'radmanct-components' ), 'default' => 0, 'media' => true ),
		'background_id' => array( 'label' => __( 'Background image', 'radmanct-components' ), 'default' => 0, 'media' => true ),
		'background' => array( 'label' => __( 'Background color', 'radmanct-components' ), 'default' => '#f3f6fa', 'color' => true, 'group' => 'normal' ),
		'active_background' => array( 'label' => __( 'Background color', 'radmanct-components' ), 'default' => '#e9f1fc', 'color' => true, 'group' => 'active' ),
		'color' => array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true, 'group' => 'normal' ),
	);
	foreach ( array( 'normal', 'active' ) as $state ) {
		$prefix = 'normal' === $state ? '' : 'active_';
		foreach ( array( 'title_color' => __( 'Title color', 'radmanct-components' ), 'content_color' => __( 'Content color', 'radmanct-components' ), 'icon_color' => __( 'Icon color', 'radmanct-components' ) ) as $key => $label ) { $fields[ $prefix . $key ] = array( 'label' => $label, 'default' => '', 'color' => true, 'group' => $state ); }
		$fields[ $prefix . 'gradient' ] = array( 'label' => __( 'Gradient type', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'linear' => __( 'Linear', 'radmanct-components' ), 'radial' => __( 'Radial', 'radmanct-components' ) ), 'group' => $state );
		$fields[ $prefix . 'second_color' ] = array( 'label' => __( 'Second color', 'radmanct-components' ), 'default' => '#ffffff', 'color' => true, 'group' => $state );
		$fields[ $prefix . 'angle' ] = array( 'label' => __( 'Angle', 'radmanct-components' ), 'default' => 180, 'min' => 0, 'max' => 360, 'group' => $state );
		$fields[ $prefix . 'border_style' ] = array( 'label' => __( 'Border style', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'solid' => __( 'Solid', 'radmanct-components' ), 'dashed' => __( 'Dashed', 'radmanct-components' ), 'dotted' => __( 'Dotted', 'radmanct-components' ) ), 'group' => $state );
		$fields[ $prefix . 'border_width' ] = array( 'label' => __( 'Border width', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 12, 'group' => $state );
		$fields[ $prefix . 'border_color' ] = array( 'label' => __( 'Border color', 'radmanct-components' ), 'default' => '#d7e0e7', 'color' => true, 'group' => $state );
	}
	return $fields;
}

function number( $value, int $default, int $min, int $max ): int {
	return is_numeric( $value ) && is_finite( (float) $value ) ? max( $min, min( $max, (int) $value ) ) : $default;
}

function settings( array $options ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $options[ $key ] ?? $field['default'];
		$result[ $key ] = isset( $field['options'] ) ? ( is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default'] ) : number( $value, $field['default'], $field['min'], $field['max'] );
	}
	return $result;
}

function items( array $input ): array {
	$result = array();
	foreach ( array_slice( $input, 0, 12 ) as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		$item = array();
		foreach ( item_fields() as $key => $field ) {
			$value = $row[ $key ] ?? $field['default'];
			if ( ! empty( $field['media'] ) ) {
				$item[ $key . '_size' ] = \RadmanCTComponents\Components\ImageMedia\size( $value, 'background_id' === $key ? 'large' : 'medium_large' );
				$value = number( is_array( $value ) ? ( $value['id'] ?? 0 ) : $value, 0, 0, PHP_INT_MAX );
			}
			elseif ( isset( $field['min'] ) ) { $value = number( $value, $field['default'], $field['min'], $field['max'] ); }
			elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
			elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ? $value : $field['default']; }
			else { $value = is_string( $value ) ? $value : ''; $value = 'content' === $key ? wp_kses_post( substr( $value, 0, 16000 ) ) : sanitize_text_field( substr( $value, 0, 500 ) ); }
			$item[ $key ] = $value;
		}
		if ( '' === trim( $item['title'] ) ) { continue; }
		$item['custom_content'] = \RadmanCTComponents\Components\CustomContent\key( $row );
		$result[] = $item;
	}
	return $result;
}

function image( int $id, string $class, array|string $size = 'medium_large' ): string {
	if ( ! $id ) { return ''; }
	$id = (int) apply_filters( 'wpml_object_id', $id, 'attachment', true );
	return $id && wp_attachment_is_image( $id ) ? ( wp_get_attachment_image( $id, $size, false, array( 'class' => $class, 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ) ?: '' ) : '';
}

function render( array $input, array $options = array() ): string {
	$items = items( $input );
	if ( ! $items ) { return ''; }
	$settings = settings( $options );
	if ( 'images' === $settings['layout'] ) {
		require_once __DIR__ . '/curtain/images.php';
		return \RadmanCTComponents\Components\Presentation\render( render_images( $items, $settings ), $options, 'curtain' );
	}
	$id = wp_unique_id( 'rdct-curtain-' );
	$style = '';
	foreach ( array( 'gap', 'height', 'rail', 'padding', 'duration' ) as $key ) { $style .= '--rdct-curtain-' . $key . ':' . $settings[ $key ] . ( 'duration' === $key ? 'ms' : 'px' ) . ';'; }
	$tag = $settings['heading_tag'];
	$html = '<div class="rdct-curtain" data-rdct-curtain data-active-item="' . min( count( $items ), $settings['active_item'] ) . '" data-trigger="' . esc_attr( $settings['trigger'] ) . '" style="' . esc_attr( $style ) . '">';
	foreach ( $items as $index => $item ) {
		$panel = $id . '-' . $index;
		$icon = '';
		if ( $item['icon'] ) { require_once dirname( __DIR__ ) . '/icons.php'; $icon = \RadmanCTComponents\Icons\render( $item['icon'], array( 'size' => $item['icon_size'] ) ); }
		$content = $item['content'];
		if ( $item['custom_content'] ) { $content = \RadmanCTComponents\Components\CustomContent\render( $item['custom_content'], array( 'slot' => 'curtain' ) ); }
		elseif ( 'template' === $item['content_type'] ) { require_once __DIR__ . '/template-library.php'; $content = \RadmanCTComponents\Components\TemplateLibrary\render( $item['template_id'] ) ?: $content; }
		$before = 'before' === $item['icon_position'] ? $icon : ''; $after = 'after' === $item['icon_position'] ? $icon : '';
		$html .= '<' . $settings['item_tag'] . ' class="rdct-curtain__item" style="' . esc_attr( item_css( $item ) ) . '">' . image( $item['background_id'], 'rdct-curtain__background', $item['background_id_size'] );
		$html .= '<' . $tag . ' class="rdct-curtain__heading"><button type="button" class="rdct-curtain__trigger" id="' . esc_attr( $panel . '-label' ) . '" aria-expanded="true" aria-controls="' . esc_attr( $panel ) . '">' . $before . '<span>' . esc_html( $item['title'] ) . '</span>' . $after . '</button></' . $tag . '>';
		$html .= '<div class="rdct-curtain__panel" id="' . esc_attr( $panel ) . '" role="region" aria-labelledby="' . esc_attr( $panel . '-label' ) . '"><div class="rdct-curtain__body">' . image( $item['image_id'], 'rdct-curtain__image', $item['image_id_size'] ) . '<' . $tag . ' class="rdct-curtain__title">' . $before . '<span>' . esc_html( $item['title'] ) . '</span>' . $after . '</' . $tag . '><div class="rdct-curtain__text">' . $content . '</div></div></div></' . $settings['item_tag'] . '>';
	}
	return \RadmanCTComponents\Components\Presentation\render( $html . '</div>', $options, 'curtain' );
}

function item_css( array $item ): string {
	$css = '--rdct-curtain-color:' . $item['color'] . ';';
	foreach ( array( '', 'active_' ) as $prefix ) {
		$background = $item[ $prefix . 'background' ];
		if ( 'none' !== $item[ $prefix . 'gradient' ] ) { $background = ( 'radial' === $item[ $prefix . 'gradient' ] ? 'radial-gradient(circle,' : 'linear-gradient(' . $item[ $prefix . 'angle' ] . 'deg,' ) . $background . ',' . $item[ $prefix . 'second_color' ] . ')'; }
		$css .= '--rdct-curtain-' . ( $prefix ? 'active-bg' : 'bg' ) . ':' . $background . ';';
		foreach ( array( 'title_color', 'content_color', 'icon_color' ) as $key ) {
			$value = $item[ $prefix . $key ];
			if ( '' === $value ) { $value = 'var(--rdct-curtain-' . ( $prefix ? str_replace( '_', '-', $key ) : 'color' ) . ')'; }
			$css .= '--rdct-curtain-' . str_replace( '_', '-', $prefix . $key ) . ':' . $value . ';';
		}
		$css .= '--rdct-curtain-' . ( $prefix ? 'active-border' : 'border' ) . ':' . $item[ $prefix . 'border_width' ] . 'px ' . $item[ $prefix . 'border_style' ] . ' ' . $item[ $prefix . 'border_color' ] . ';';
	}
	return $css;
}
