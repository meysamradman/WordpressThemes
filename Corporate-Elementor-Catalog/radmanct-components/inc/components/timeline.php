<?php

namespace RadmanCTComponents\Components\Timeline;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/image-media.php';

function fields(): array {
	return array(
		'style' => array( 'label' => __( 'Timeline style', 'radmanct-components' ), 'default' => 'alternating', 'options' => array( 'alternating' => __( 'Alternating image and text', 'radmanct-components' ) ) ),
		'first_image' => array( 'label' => __( 'First image position', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'heading_tag' => array( 'label' => __( 'Title HTML tag', 'radmanct-components' ), 'default' => 'h3', 'options' => array( 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6' ) ),
		'show_line' => array( 'label' => __( 'Show timeline line', 'radmanct-components' ), 'default' => true ),
		'gap' => array( 'label' => __( 'Items gap', 'radmanct-components' ), 'default' => 56, 'min' => 0, 'max' => 200 ),
		'column_gap' => array( 'label' => __( 'Column gap', 'radmanct-components' ), 'default' => 64, 'min' => 24, 'max' => 200 ),
		'image_radius' => array( 'label' => __( 'Image corner radius', 'radmanct-components' ), 'default' => 16, 'min' => 0, 'max' => 120 ),
		'image_ratio' => array( 'label' => __( 'Image aspect ratio', 'radmanct-components' ), 'default' => '4/3', 'options' => array( '4/3' => '4:3', '16/9' => '16:9', '1/1' => '1:1', 'auto' => __( 'Original', 'radmanct-components' ) ) ),
		'label' => array( 'label' => __( 'Accessible label', 'radmanct-components' ), 'default' => __( 'Timeline', 'radmanct-components' ) ),
	);
}

function item_fields(): array {
	return array(
		'date' => array( 'label' => __( 'Date or milestone', 'radmanct-components' ), 'default' => '' ),
		'datetime' => array( 'label' => __( 'Machine date (YYYY-MM-DD, optional)', 'radmanct-components' ), 'default' => '' ),
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => '' ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true ),
		'image_id' => array( 'label' => __( 'Image', 'radmanct-components' ), 'default' => 0, 'media' => true ),
	);
}

function settings( array $options ): array {
	$s = array();
	foreach ( fields() as $key => $field ) {
		$value = $options[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $s[ $key ] = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( isset( $field['min'] ) ) { $s[ $key ] = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $s[ $key ] = in_array( $value, array( true, 1, '1', 'yes', 'true' ), true ); }
		else { $s[ $key ] = is_string( $value ) ? sanitize_text_field( $value ) : $field['default']; }
	}
	return $s;
}

function assets( array $options ): array { return array( 'timeline', 'timeline-' . settings( $options )['style'] ); }

function render( array $items, array $options = array() ): string {
	$s = settings( $options ); $rows = '';
	foreach ( array_slice( $items, 0, 100 ) as $item ) {
		if ( ! is_array( $item ) ) { continue; }
		$title = is_string( $item['title'] ?? null ) ? sanitize_text_field( $item['title'] ) : '';
		$content = is_string( $item['content'] ?? null ) ? wp_kses_post( $item['content'] ) : '';
		$date = is_string( $item['date'] ?? null ) ? sanitize_text_field( $item['date'] ) : '';
		$image = $item['image_id'] ?? 0;
		$id = \RadmanCTComponents\Components\ImageMedia\attachment_id( $image );
		$image = $id > 0 ? wp_get_attachment_image( (int) apply_filters( 'wpml_object_id', $id, 'attachment', true ), \RadmanCTComponents\Components\ImageMedia\size( $image ), false, array( 'class' => 'rdct-timeline__image', 'loading' => 'lazy', 'decoding' => 'async' ) ) : '';
		if ( '' === $title && '' === trim( strip_tags( $content ) ) && '' === $date && ! $image ) { continue; }
		$rows .= '<li class="rdct-timeline__item">';
		if ( $image ) { $rows .= '<figure class="rdct-timeline__media">' . $image . '</figure>'; }
		$has_text = '' !== $title || '' !== $content || '' !== $date;
		if ( $has_text ) { $rows .= '<div class="rdct-timeline__content">'; }
		if ( '' !== $date ) {
			$datetime = is_string( $item['datetime'] ?? null ) ? $item['datetime'] : '';
			$valid = preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/D', $datetime, $parts ) && checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] );
			$rows .= $valid ? '<time class="rdct-timeline__date" datetime="' . esc_attr( $datetime ) . '">' . esc_html( $date ) . '</time>' : '<span class="rdct-timeline__date">' . esc_html( $date ) . '</span>';
		}
		if ( '' !== $title ) { $rows .= '<' . $s['heading_tag'] . ' class="rdct-timeline__title">' . esc_html( $title ) . '</' . $s['heading_tag'] . '>'; }
		if ( '' !== $content ) { $rows .= '<div class="rdct-timeline__text">' . $content . '</div>'; }
		if ( $has_text ) { $rows .= '</div>'; }
		$rows .= '</li>';
	}
	if ( '' === $rows ) { return ''; }
	$css = '--rdct-timeline-gap:' . $s['gap'] . 'px;--rdct-timeline-columns-gap:' . $s['column_gap'] . 'px;--rdct-timeline-radius:' . $s['image_radius'] . 'px;--rdct-timeline-ratio:' . $s['image_ratio'] . ';';
	return '<ol role="list" class="rdct-timeline rdct-timeline--' . esc_attr( $s['style'] ) . '" aria-label="' . esc_attr( $s['label'] ) . '" data-first-image="' . esc_attr( $s['first_image'] ) . '" data-line="' . ( $s['show_line'] ? 'true' : 'false' ) . '" style="' . esc_attr( $css ) . '">' . $rows . '</ol>';
}
