<?php

namespace RadmanCTComponents\Components\Box;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/image-loading.php';
require_once __DIR__ . '/image-media.php';

function styles(): array {
	return array(
		'cover' => __( 'Tall image cover', 'radmanct-components' ),
		's01' => __( 'Image overlay — 1', 'radmanct-components' ),
		's01a' => __( 'Image overlay — 1A', 'radmanct-components' ),
		's01b' => __( 'Image overlay with label — 1B', 'radmanct-components' ),
		's02' => __( 'Image with caption bar — 2', 'radmanct-components' ),
		's03' => __( 'Image with dark content — 3', 'radmanct-components' ),
		's04' => __( 'Centered card with divider — 4', 'radmanct-components' ),
		's05' => __( 'Side image card — 5', 'radmanct-components' ),
		's06' => __( 'Image with tilt effect — 6', 'radmanct-components' ),
		's07' => __( 'Raised image card — 7', 'radmanct-components' ),
		's08' => __( 'Overlapping content card — 8', 'radmanct-components' ),
		's09' => __( 'Centered image card — 9', 'radmanct-components' ),
		's10' => __( 'Text with arrow — 10', 'radmanct-components' ),
		's11' => __( 'Circular image card — 11', 'radmanct-components' ),
	);
}

function fields(): array {
	$fields = array(
		'template' => array( 'label' => __( 'Style', 'radmanct-components' ), 'default' => 's01', 'options' => styles() ),
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => __( 'Title', 'radmanct-components' ) ),
		'title_tag' => array( 'label' => __( 'Title HTML tag', 'radmanct-components' ), 'default' => 'h2', 'options' => array_combine( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ) ) ),
		'inherit_font' => array( 'label' => __( 'Inherit font styles', 'radmanct-components' ), 'default' => false ),
		'inherited_tag' => array( 'label' => __( 'Inherited heading size', 'radmanct-components' ), 'default' => 'h2', 'options' => array_combine( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ),
		'subtitle' => array( 'label' => __( 'Subtitle', 'radmanct-components' ), 'default' => '', 'styles' => array( 's06' ) ),
		'label' => array( 'label' => __( 'Label', 'radmanct-components' ), 'default' => '', 'styles' => array( 's01', 's01b', 's03' ) ),
		'image_loading' => \RadmanCTComponents\Components\ImageLoading\field() + array( 'styles' => array_values( array_diff( array_keys( styles() ), array( 's10' ) ) ) ),
		'text' => array( 'label' => __( 'Text', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true ),
		'url' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '', 'url' => true ),
		'new_tab' => array( 'label' => __( 'Open in new window', 'radmanct-components' ), 'default' => false ),
		'nofollow' => array( 'label' => __( 'Add nofollow', 'radmanct-components' ), 'default' => false ),
		'show_button' => array( 'label' => __( 'Show button', 'radmanct-components' ), 'default' => false ),
		'button_text' => array( 'label' => __( 'Button text', 'radmanct-components' ), 'default' => __( 'View details', 'radmanct-components' ) ),
		'button_url' => array( 'label' => __( 'Button link', 'radmanct-components' ), 'default' => '', 'url' => true ),
		'icon' => array( 'label' => __( 'Icon', 'radmanct-components' ), 'default' => '', 'styles' => array( 's06', 's08', 'cover' ) ),
		'content_alignment' => array( 'label' => __( 'Content alignment', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Reading start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'Reading end', 'radmanct-components' ), 'middle' => __( 'Middle', 'radmanct-components' ) ) ),
	);
	foreach ( array(
		'height_percent' => array( __( 'Box height (%)', 'radmanct-components' ), 100, 50, 125 ),
		'height' => array( __( 'Custom height (px)', 'radmanct-components' ), 0, 0, 1600 ),
		'content_width' => array( __( 'Content width (%)', 'radmanct-components' ), 80, 50, 100 ),
		'padding' => array( __( 'Content padding', 'radmanct-components' ), 32, 0, 120 ),
		'label_padding' => array( __( 'Label padding', 'radmanct-components' ), 8, 0, 64 ),
		'title_size' => array( __( 'Title size', 'radmanct-components' ), 0, 0, 120 ),
		'label_size' => array( __( 'Label size', 'radmanct-components' ), 0, 0, 72 ),
		'text_size' => array( __( 'Text size', 'radmanct-components' ), 0, 0, 72 ),
		'overlay_opacity' => array( __( 'Overlay opacity', 'radmanct-components' ), 0.7, 0, 1 ),
		'hover_overlay_opacity' => array( __( 'Hover overlay opacity', 'radmanct-components' ), 0.85, 0, 1 ),
		'overlay_angle' => array( __( 'Gradient angle', 'radmanct-components' ), 0, 0, 360 ),
		'radius' => array( __( 'Border radius', 'radmanct-components' ), 6, 0, 160 ),
	) as $key => $field ) { $fields[ $key ] = array( 'label' => $field[0], 'default' => $field[1], 'min' => $field[2], 'max' => $field[3] ); }
	foreach ( array(
		'overlay_color' => array( __( 'Overlay color', 'radmanct-components' ), '#000000' ),
		'overlay_second_color' => array( __( 'Gradient second color', 'radmanct-components' ), '#000000' ),
		'hover_overlay_color' => array( __( 'Hover overlay color', 'radmanct-components' ), '#000000' ),
		'content_background' => array( __( 'Content background', 'radmanct-components' ), '#ffffff' ),
		'revealer_color' => array( __( 'Revealer color', 'radmanct-components' ), '#000000' ),
		'hover_revealer_color' => array( __( 'Hover revealer color', 'radmanct-components' ), '#235a50' ),
		'heading_color' => array( __( 'Heading color', 'radmanct-components' ), '' ),
		'heading_hover_color' => array( __( 'Hover heading color', 'radmanct-components' ), '' ),
		'text_color' => array( __( 'Content color', 'radmanct-components' ), '' ),
		'subtitle_color' => array( __( 'Subtitle color', 'radmanct-components' ), '' ),
		'icon_color' => array( __( 'Icon color', 'radmanct-components' ), '' ),
		'icon_hover_color' => array( __( 'Hover icon color', 'radmanct-components' ), '' ),
		'label_color' => array( __( 'Label color', 'radmanct-components' ), '' ),
		'label_background' => array( __( 'Label background', 'radmanct-components' ), '#235a50' ),
	) as $key => $field ) { $fields[ $key ] = array( 'label' => $field[0], 'default' => $field[1], 'color' => true ); }
	return $fields;
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 'yes', 'true' ), true ); }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? min( $field['max'], max( $field['min'], (float) $value ) ) : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ? $value : $field['default']; }
		elseif ( ! empty( $field['url'] ) ) { $value = is_string( $value ) && preg_match( '~^(https?://|/(?!/)|#|mailto:|tel:)~i', $value ) ? esc_url_raw( substr( $value, 0, 2048 ), array( 'http', 'https', 'mailto', 'tel' ) ) : ''; }
		elseif ( 'text' === $key ) { $value = is_string( $value ) ? wp_kses_post( substr( $value, 0, 8000 ) ) : ''; }
		else { $value = is_string( $value ) ? sanitize_text_field( substr( $value, 0, 500 ) ) : ''; }
		$result[ $key ] = $value;
	}
	$result['image_id'] = is_numeric( $input['image_id'] ?? null ) ? max( 0, (int) $input['image_id'] ) : 0;
	return $result;
}

function css( array $settings ): string {
	$css = '';
	foreach ( array( 'padding', 'label_padding', 'radius', 'overlay_opacity', 'hover_overlay_opacity', 'overlay_angle', 'content_width' ) as $key ) { $css .= '--rdct-box-' . str_replace( '_', '-', $key ) . ':' . $settings[ $key ] . ( in_array( $key, array( 'padding', 'label_padding', 'radius' ), true ) ? 'px' : ( 'content_width' === $key ? '%' : ( 'overlay_angle' === $key ? 'deg' : '' ) ) ) . ';'; }
	$css .= '--rdct-box-ratio:100 / ' . $settings['height_percent'] . ';';
	if ( $settings['height'] > 0 ) { $css .= '--rdct-box-height:' . $settings['height'] . 'px;'; }
	foreach ( array( 'title_size', 'label_size', 'text_size' ) as $key ) { if ( $settings[ $key ] > 0 ) { $css .= '--rdct-box-' . str_replace( '_', '-', $key ) . ':' . $settings[ $key ] . 'px;'; } }
	foreach ( fields() as $key => $field ) { if ( ! empty( $field['color'] ) && $settings[ $key ] ) { $css .= '--rdct-box-' . str_replace( '_', '-', $key ) . ':' . $settings[ $key ] . ';'; } }
	return $css;
}

function render( array $input ): string {
	$s = settings( $input );
	if ( '' === $s['title'] ) { return ''; }
	$image = '';
	if ( $s['image_id'] && 's10' !== $s['template'] ) {
		$id = (int) apply_filters( 'wpml_object_id', $s['image_id'], 'attachment', true );
		if ( $id && wp_attachment_is_image( $id ) ) { $image = wp_get_attachment_image( $id, \RadmanCTComponents\Components\ImageMedia\size( $input['image'] ?? null ), false, array( 'class' => 'rdct-box__image', 'decoding' => 'async', 'alt' => '' ) + \RadmanCTComponents\Components\ImageLoading\attributes( $s['image_loading'] ) ) ?: ''; }
	}
	$icon = '';
	if ( $s['icon'] ) { require_once dirname( __DIR__ ) . '/icons.php'; $icon = \RadmanCTComponents\Icons\render( $s['icon'], array( 'size' => 32 ) ); }
	$motion = 's06' === $s['template'] ? ' data-rdct-box-motion="tilt"' : '';
	$html = '<article class="rdct-box rdct-box--' . $s['template'] . ' rdct-box--align-' . $s['content_alignment'] . ( $s['inherit_font'] ? ' rdct-box--inherit rdct-box--' . $s['inherited_tag'] : '' ) . '"' . $motion . ' style="' . esc_attr( css( $s ) ) . '">';
	if ( $image ) { $html .= '<div class="rdct-box__media">' . $image . '<span class="rdct-box__overlay" aria-hidden="true"></span></div>'; }
	$html .= '<div class="rdct-box__content">';
	if ( $s['label'] ) { $html .= '<span class="rdct-box__label">' . esc_html( $s['label'] ) . '</span>'; }
	if ( $icon ) { $html .= '<span class="rdct-box__icon">' . $icon . '</span>'; }
	if ( $s['subtitle'] ) { $html .= '<p class="rdct-box__subtitle">' . esc_html( $s['subtitle'] ) . '</p>'; }
	$html .= '<' . $s['title_tag'] . ' class="rdct-box__title">' . esc_html( $s['title'] ) . '</' . $s['title_tag'] . '>';
	if ( $s['text'] ) { $html .= '<div class="rdct-box__text">' . ( str_contains( $s['text'], '<' ) ? $s['text'] : nl2br( esc_html( $s['text'] ) ) ) . '</div>'; }
	if ( $s['show_button'] && $s['button_url'] && $s['button_text'] ) { $html .= '<a class="rdct-box__button" href="' . esc_url( $s['button_url'] ) . '">' . esc_html( $s['button_text'] ) . '</a>'; }
	$html .= '</div>';
	if ( $s['url'] ) { $html .= '<a class="rdct-box__link" href="' . esc_url( $s['url'] ) . '" aria-label="' . esc_attr( $s['title'] ) . '"' . ( $s['new_tab'] ? ' target="_blank"' : '' ) . ' rel="' . esc_attr( implode( ' ', array_filter( array( $s['new_tab'] ? 'noopener' : '', $s['nofollow'] ? 'nofollow' : '' ) ) ) ) . '"></a>'; }
	return \RadmanCTComponents\Components\Presentation\render( $html . '</article>', $input, 'box' );
}
