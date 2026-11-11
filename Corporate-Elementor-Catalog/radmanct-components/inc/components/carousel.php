<?php

namespace RadmanCTComponents\Components\Carousel;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/template-library.php';
require_once __DIR__ . '/custom-content.php';
require_once __DIR__ . '/carousel-controls.php';
require_once __DIR__ . '/carousel-gallery.php';
require_once __DIR__ . '/marquee.php';
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/image-loading.php';
require_once __DIR__ . '/image-media.php';
require_once dirname( __DIR__ ) . '/icons.php';

function fields(): array {
	$fields = array(
		'image_loading' => \RadmanCTComponents\Components\ImageLoading\field(),
		'label' => array( 'label' => __( 'Accessible label', 'radmanct-components' ), 'default' => __( 'Carousel', 'radmanct-components' ) ),
		'controller_id' => array( 'label' => __( 'Carousel ID', 'radmanct-components' ), 'default' => '', 'description' => __( 'Use a unique name starting with a letter, without #.', 'radmanct-components' ), 'condition' => array( 'mode' => array( 'carousel', 'slider', 'stack' ) ) ),
		'controlled_ids' => array( 'label' => __( 'Controlled carousel IDs', 'radmanct-components' ), 'default' => '', 'description' => __( 'Add up to 16 carousel IDs, separated by commas. Use autoplay on the controlling carousel only.', 'radmanct-components' ), 'condition' => array( 'mode' => array( 'carousel', 'slider', 'stack' ) ) ),
		'sync_mode' => array( 'label' => __( 'Synchronization method', 'radmanct-components' ), 'default' => 'index', 'options' => array( 'index' => __( 'Slide index', 'radmanct-components' ), 'progress' => __( 'Continuous progress', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'carousel', 'slider', 'stack' ) ) ),
		'external_previous_class' => array( 'label' => __( 'Previous button CSS class', 'radmanct-components' ), 'default' => '', 'description' => __( 'Enter one unique CSS class without a dot. Add it to a button or its widget wrapper on this page.', 'radmanct-components' ), 'condition' => array( 'mode' => array( 'carousel', 'slider', 'stack' ) ) ),
		'external_next_class' => array( 'label' => __( 'Next button CSS class', 'radmanct-components' ), 'default' => '', 'description' => __( 'Enter one unique CSS class without a dot. Add it to a button or its widget wrapper on this page.', 'radmanct-components' ), 'condition' => array( 'mode' => array( 'carousel', 'slider', 'stack' ) ) ),
		'variant' => array( 'label' => __( 'Appearance', 'radmanct-components' ), 'default' => 'cards', 'options' => array( 'content' => __( 'Existing components', 'radmanct-components' ), 'cards' => __( 'Cards', 'radmanct-components' ), 'images' => __( 'Images', 'radmanct-components' ), 'brands' => __( 'Brand logos', 'radmanct-components' ) ) ),
		'mode' => array( 'label' => __( 'Display mode', 'radmanct-components' ), 'default' => 'carousel', 'options' => array( 'carousel' => __( 'Carousel', 'radmanct-components' ), 'slider' => __( 'Single-slide slider', 'radmanct-components' ), 'stack' => __( 'Stacked cards', 'radmanct-components' ), 'marquee' => __( 'Marquee', 'radmanct-components' ) ) ),
		'transition' => array( 'label' => __( 'Slide transition', 'radmanct-components' ), 'default' => 'slide', 'options' => array( 'slide' => __( 'Slide', 'radmanct-components' ), 'fade' => __( 'Crossfade', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'slider' ) ) ),
		'alignment' => array( 'label' => __( 'Slide alignment', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ) ) ),
		'height_mode' => array( 'label' => __( 'Automatic height', 'radmanct-components' ), 'default' => 'equal', 'options' => array( 'equal' => __( 'Equal height', 'radmanct-components' ), 'natural' => __( 'Natural height', 'radmanct-components' ), 'adaptive' => __( 'Active-slide height', 'radmanct-components' ) ) ),
		'draggable' => array( 'label' => __( 'Drag and swipe', 'radmanct-components' ), 'default' => true ),
		'rewind' => array( 'label' => __( 'Return to the first slide', 'radmanct-components' ), 'default' => true ),
		'group_slides' => array( 'label' => __( 'Move visible slides as a group', 'radmanct-components' ), 'default' => false ),
		'variable_width' => array( 'label' => __( 'Variable item widths', 'radmanct-components' ), 'default' => false, 'condition' => array( 'mode' => array( 'carousel' ) ) ),
		'slides' => array( 'label' => __( 'Desktop slides', 'radmanct-components' ), 'default' => 3, 'min' => 1, 'max' => 8, 'step' => 0.1 ),
		'slides_tablet' => array( 'label' => __( 'Tablet slides', 'radmanct-components' ), 'default' => 2, 'min' => 1, 'max' => 6, 'step' => 0.1 ),
		'slides_mobile' => array( 'label' => __( 'Mobile slides', 'radmanct-components' ), 'default' => 1, 'min' => 1, 'max' => 3, 'step' => 0.1 ),
		'gap' => array( 'label' => __( 'Slide gap (px)', 'radmanct-components' ), 'default' => 24, 'min' => 0, 'max' => 80 ),
		'height' => array( 'label' => __( 'Item height', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 2000, 'step' => 0.1 ),
		'height_tablet' => array( 'label' => __( 'Tablet item height', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 2000, 'step' => 0.1 ),
		'height_mobile' => array( 'label' => __( 'Mobile item height', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 2000, 'step' => 0.1 ),
		'height_unit' => array( 'label' => __( 'Height unit', 'radmanct-components' ), 'default' => 'px', 'options' => array( 'px' => 'px', 'em' => 'em', '%' => '%', 'vh' => 'vh' ) ),
		'height_unit_tablet' => array( 'label' => __( 'Tablet height unit', 'radmanct-components' ), 'default' => 'px', 'options' => array( 'px' => 'px', 'em' => 'em', '%' => '%', 'vh' => 'vh' ) ),
		'height_unit_mobile' => array( 'label' => __( 'Mobile height unit', 'radmanct-components' ), 'default' => 'px', 'options' => array( 'px' => 'px', 'em' => 'em', '%' => '%', 'vh' => 'vh' ) ),
		'duration' => array( 'label' => __( 'Transition duration (ms)', 'radmanct-components' ), 'default' => 500, 'min' => 0, 'max' => 2000, 'step' => 0.1 ),
		'delay' => array( 'label' => __( 'Autoplay delay (ms)', 'radmanct-components' ), 'default' => 4000, 'min' => 1500, 'max' => 30000 ),
		'autoplay' => array( 'label' => __( 'Autoplay', 'radmanct-components' ), 'default' => false ),
		'media_motion' => array( 'label' => __( 'Ken Burns image motion', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'in' => __( 'Zoom in', 'radmanct-components' ), 'out' => __( 'Zoom out', 'radmanct-components' ), 'start' => __( 'Zoom toward reading start', 'radmanct-components' ), 'end' => __( 'Zoom toward reading end', 'radmanct-components' ) ) ),
		'motion_duration' => array( 'label' => __( 'Image motion duration (seconds)', 'radmanct-components' ), 'default' => 8, 'min' => 2, 'max' => 60, 'step' => .1 ),
		'motion_scale' => array( 'label' => __( 'Image zoom scale', 'radmanct-components' ), 'default' => 1.15, 'min' => 1.01, 'max' => 1.5, 'step' => .01 ),
		'pause_hover' => array( 'label' => __( 'Pause autoplay on hover', 'radmanct-components' ), 'default' => true ),
		'resume_interaction' => array( 'label' => __( 'Resume autoplay after interaction', 'radmanct-components' ), 'default' => false ),
		'stop_last' => array( 'label' => __( 'Stop autoplay at the last slide', 'radmanct-components' ), 'default' => false ),
		'autoplay_direction' => array( 'label' => __( 'Autoplay direction', 'radmanct-components' ), 'default' => 'forward', 'options' => array( 'forward' => __( 'Forward', 'radmanct-components' ), 'reverse' => __( 'Reverse', 'radmanct-components' ) ) ),
		'arrows' => array( 'label' => __( 'Navigation arrows', 'radmanct-components' ), 'default' => true ),
		'arrow_style' => array( 'label' => __( 'Arrow appearance', 'radmanct-components' ), 'default' => 'circle', 'options' => array( 'circle' => __( 'Circle', 'radmanct-components' ), 'square' => __( 'Square', 'radmanct-components' ), 'minimal' => __( 'Minimal', 'radmanct-components' ) ) ),
		'arrow_fill' => array( 'label' => __( 'Arrow fill', 'radmanct-components' ), 'default' => 'default', 'options' => array( 'default' => __( 'Default', 'radmanct-components' ), 'outline' => __( 'Outlined', 'radmanct-components' ), 'solid' => __( 'Solid', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ) ) ),
		'arrow_shadow' => array( 'label' => __( 'Arrow shadow', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'normal' => __( 'Always', 'radmanct-components' ), 'hover' => __( 'On hover and focus', 'radmanct-components' ) ) ),
		'arrow_background' => array( 'label' => __( 'Arrow background', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'arrow_hover_background' => array( 'label' => __( 'Arrow hover background', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'arrow_border_color' => array( 'label' => __( 'Arrow border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'arrow_hover_border_color' => array( 'label' => __( 'Arrow hover border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'arrow_size' => array( 'label' => __( 'Arrow button size (px)', 'radmanct-components' ), 'default' => 44, 'min' => 44, 'max' => 120 ),
		'arrow_icon_size' => array( 'label' => __( 'Arrow icon size (px)', 'radmanct-components' ), 'default' => 20, 'min' => 8, 'max' => 64 ),
		'arrow_gap' => array( 'label' => __( 'Arrow gap (px)', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 64 ),
		'arrow_color' => array( 'label' => __( 'Arrow color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'arrow_hover_color' => array( 'label' => __( 'Arrow hover color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'pagination' => array( 'label' => __( 'Pagination', 'radmanct-components' ), 'default' => 'dots', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'dots' => __( 'Dots', 'radmanct-components' ), 'numbers' => __( 'Numbers', 'radmanct-components' ), 'fraction' => __( 'Current / total', 'radmanct-components' ) ) ),
		'position' => array( 'label' => __( 'Arrow position', 'radmanct-components' ), 'default' => 'bottom', 'options' => array( 'top' => __( 'Top', 'radmanct-components' ), 'middle' => __( 'Middle', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ) ) ),
		'pagination_position' => array( 'label' => __( 'Pagination position', 'radmanct-components' ), 'default' => 'follow', 'options' => array( 'follow' => __( 'Same as arrows', 'radmanct-components' ), 'top' => __( 'Top', 'radmanct-components' ), 'middle' => __( 'Middle', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ) ) ),
		'arrow_alignment' => array( 'label' => __( 'Arrow alignment', 'radmanct-components' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Automatic', 'radmanct-components' ), 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ), 'spread' => __( 'Opposite edges', 'radmanct-components' ) ) ),
		'pagination_alignment' => array( 'label' => __( 'Pagination alignment', 'radmanct-components' ), 'default' => 'center', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'controls_overlay' => array( 'label' => __( 'Overlay controls on slides', 'radmanct-components' ), 'default' => false ),
		'previous_icon' => array( 'label' => __( 'Previous arrow icon (LTR)', 'radmanct-components' ), 'default' => 'arrow-left', 'options' => \RadmanCTComponents\Icons\labels() ),
		'next_icon' => array( 'label' => __( 'Next arrow icon (LTR)', 'radmanct-components' ), 'default' => 'arrow-right', 'options' => \RadmanCTComponents\Icons\labels() ),
	);
    foreach ( array( 'delay', 'pause_hover', 'resume_interaction', 'stop_last', 'autoplay_direction' ) as $key ) { $fields[ $key ]['condition'] = array( 'autoplay' => array( true ) ); }
    foreach ( array( 'alignment', 'gap', 'group_slides' ) as $key ) { $fields[ $key ]['condition'] = array( 'mode' => array( 'carousel' ) ); }
    foreach ( array( 'position', 'arrow_alignment', 'previous_icon', 'next_icon' ) as $key ) { $fields[ $key ]['condition'] = array( 'arrows' => array( true ) ); }
    foreach ( array( 'arrow_style', 'arrow_size', 'arrow_icon_size', 'arrow_gap', 'arrow_color', 'arrow_hover_color', 'arrow_fill', 'arrow_shadow', 'arrow_background', 'arrow_hover_background', 'arrow_border_color', 'arrow_hover_border_color' ) as $key ) { $fields[ $key ]['condition'] = array( 'arrows' => array( true ), 'mode' => array( 'carousel', 'slider', 'stack' ) ); }
    foreach ( array( 'pagination_position', 'pagination_alignment' ) as $key ) { $fields[ $key ]['condition'] = array( 'pagination' => array( 'dots', 'numbers', 'fraction' ) ); }
    foreach ( array( 'motion_duration', 'motion_scale' ) as $key ) { $fields[ $key ]['condition'] = array( 'media_motion' => array( 'in', 'out', 'start', 'end' ) ); }
    foreach ( array( 'slides', 'slides_tablet', 'slides_mobile' ) as $key ) { $fields[ $key ]['condition'] = array( 'mode' => array( 'carousel', 'marquee' ) ); }
    foreach ( array( 'slides', 'slides_tablet', 'slides_mobile' ) as $key ) { $fields[ $key ]['condition']['variable_width'] = array( false ); }
    foreach ( array( 'duration', 'autoplay', 'delay', 'pause_hover', 'resume_interaction', 'stop_last', 'autoplay_direction', 'arrows', 'pagination', 'position', 'arrow_alignment', 'pagination_position', 'pagination_alignment', 'controls_overlay', 'previous_icon', 'next_icon', 'media_motion', 'motion_duration', 'motion_scale' ) as $key ) { $fields[ $key ]['condition']['mode'] = array( 'carousel', 'slider', 'stack' ); }
    foreach ( \RadmanCTComponents\Components\Marquee\fields() as $key => $field ) { $fields[ $key ] = $field + array( 'condition' => array( 'mode' => array( 'marquee' ) ) ); }
    return $fields;

}

function item_fields(): array {
	return array(
		'content_type' => array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'content', 'options' => array( 'content' => __( 'Content', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ) ),
		'width' => array( 'label' => __( 'Item width (px, 0 = content)', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 2000 ),
		'width_tablet' => array( 'label' => __( 'Tablet item width (px, -1 = inherit)', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 2000 ),
		'width_mobile' => array( 'label' => __( 'Mobile item width (px, -1 = inherit)', 'radmanct-components' ), 'default' => -1, 'min' => -1, 'max' => 2000 ),
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => '' ),
		'image_id' => array( 'label' => __( 'Image or logo', 'radmanct-components' ), 'default' => 0, 'media' => true, 'min' => 0, 'max' => PHP_INT_MAX ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true ),
		'link' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '' ),
		'template_id' => array( 'label' => __( 'Elementor template', 'radmanct-components' ), 'default' => 0, 'template' => true, 'min' => 0, 'max' => PHP_INT_MAX, 'condition' => array( 'content_type' => 'template' ) ),
	);
}

function groups(): array {
	return array(
		'layout' => array( 'label' => __( 'Layout', 'radmanct-components' ), 'fields' => array( 'label', 'variant', 'mode', 'image_loading', 'transition', 'alignment', 'variable_width', 'slides', 'slides_tablet', 'slides_mobile', 'gap', 'draggable', 'rewind', 'group_slides' ) ),
		'height' => array( 'label' => __( 'Items height', 'radmanct-components' ), 'fields' => array( 'height_mode', 'height', 'height_tablet', 'height_mobile', 'height_unit', 'height_unit_tablet', 'height_unit_mobile' ) ),
		'navigation' => array( 'label' => __( 'Navigation', 'radmanct-components' ), 'fields' => array( 'arrows', 'position', 'arrow_alignment', 'previous_icon', 'next_icon', 'pagination', 'pagination_position', 'pagination_alignment', 'controls_overlay' ) ),
		'navigation_style' => array( 'label' => __( 'Arrow appearance', 'radmanct-components' ), 'tab' => 'style', 'fields' => array( 'arrow_style', 'arrow_size', 'arrow_icon_size', 'arrow_gap', 'arrow_color', 'arrow_hover_color', 'arrow_fill', 'arrow_shadow', 'arrow_background', 'arrow_hover_background', 'arrow_border_color', 'arrow_hover_border_color' ) ),
		'synchronization' => array( 'label' => __( 'Synchronization', 'radmanct-components' ), 'fields' => array( 'controller_id', 'controlled_ids', 'sync_mode' ) ),
		'external_controls' => array( 'label' => __( 'External buttons', 'radmanct-components' ), 'fields' => array( 'external_previous_class', 'external_next_class' ) ),
		'marquee' => array( 'label' => __( 'Marquee', 'radmanct-components' ), 'fields' => array_keys( \RadmanCTComponents\Components\Marquee\fields() ) ),
		'timing' => array( 'label' => __( 'Timing', 'radmanct-components' ), 'fields' => array( 'duration', 'autoplay', 'delay', 'pause_hover', 'resume_interaction', 'stop_last', 'autoplay_direction', 'media_motion', 'motion_duration', 'motion_scale' ) ),
	);
}

function controller_id( $value ): string {
	return is_string( $value ) && preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,63}$/D', trim( $value ) ) ? trim( $value ) : '';
}

function controlled_ids( $value ): string {
	if ( ! is_string( $value ) || strlen( $value ) > 1040 ) { return ''; }
	$ids = array();
	foreach ( explode( ',', $value ) as $id ) {
		$id = trim( $id );
		$id = controller_id( str_starts_with( $id, '#' ) ? substr( $id, 1 ) : $id );
		if ( '' !== $id ) { $ids[ $id ] = $id; }
		if ( count( $ids ) >= 16 ) { break; }
	}
	return implode( ',', $ids );
}

function settings( array $input ): array {
	foreach ( array( '' => 'height_unit', '_tablet' => 'height_unit_tablet', '_mobile' => 'height_unit_mobile' ) as $suffix => $unit_key ) {
		$key = 'height' . $suffix;
		if ( 'auto' === ( $input[ $key ] ?? null ) ) { $input[ $key ] = array( 'size' => 0, 'unit' => 'px' ); }
		elseif ( is_string( $input[ $key ] ?? null ) && preg_match( '/^([0-9]+(?:\.[0-9]+)?)(px|em|%|vh)$/D', $input[ $key ], $parts ) ) { $input[ $key ] = array( 'size' => (float) $parts[1], 'unit' => $parts[2] ); }
		if ( is_array( $input[ $key ] ?? null ) ) { $size = $input[ $key ]; $input[ $unit_key ] = 'auto' === ( $size['unit'] ?? '' ) ? 'px' : ( $size['unit'] ?? 'px' ); $input[ $key ] = 'auto' === ( $size['unit'] ?? '' ) ? 0 : ( $size['size'] ?? ( '' === $suffix ? 0 : -1 ) ); }
	}
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( 'controller_id' === $key ) { $value = controller_id( $value ); }
		elseif ( in_array( $key, array( 'external_previous_class', 'external_next_class' ), true ) ) { $value = controller_id( is_string( $value ) && str_starts_with( trim( $value ), '.' ) ? substr( trim( $value ), 1 ) : $value ); }
		elseif ( 'controlled_ids' === $key ) { $value = controlled_ids( $value ); }
		elseif ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], ( isset( $field['step'] ) ? (float) $value : (int) $value ) ) ) : $field['default']; }
		elseif ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) ? ( sanitize_hex_color( $value ) ?: '' ) : ''; }
		else { $value = is_string( $value ) ? sanitize_text_field( strlen( $value ) <= 640 ? $value : $field['default'] ) : $field['default']; }
		$result[ $key ] = $value;
	}
	if ( 'follow' === $result['pagination_position'] ) { $result['pagination_position'] = 'middle' === $result['position'] ? 'bottom' : $result['position']; }
	if ( 'auto' === $result['arrow_alignment'] ) { $result['arrow_alignment'] = 'middle' === $result['position'] ? 'spread' : 'center'; }
	if ( 'slider' === $result['mode'] ) { $result['slides'] = 1; $result['slides_tablet'] = 1; $result['slides_mobile'] = 1; $result['gap'] = 0; $result['controls_overlay'] = true; }
	if ( 'stack' === $result['mode'] ) { $result['slides'] = 1; $result['slides_tablet'] = 1; $result['slides_mobile'] = 1; $result['gap'] = 0; $result['group_slides'] = false; $result['alignment'] = 'start'; }
	if ( 'slider' !== $result['mode'] ) { $result['transition'] = 'slide'; }
	if ( 'carousel' !== $result['mode'] ) { $result['variable_width'] = false; }
	return $result;
}

function height_css( array $settings ): string {
	$css = '';
	foreach ( array( '' => 'desktop', '_tablet' => 'tablet', '_mobile' => 'mobile' ) as $suffix => $device ) {
		$value = $settings[ 'height' . $suffix ];
		if ( $value < 0 ) { continue; }
		$unit = $settings[ 'height_unit' . $suffix ];
		$value = min( $value, 'px' === $unit ? 2000 : 100 );
		$css .= '--rdct-carousel-height-' . $device . ':' . ( 0.0 === (float) $value ? 'auto' : $value . $unit ) . ';';
	}
	return $css;
}

function item_width_css( array $item ): string {
	$css = '';
	foreach ( array( 'width' => 'd', 'width_tablet' => 't', 'width_mobile' => 'm' ) as $key => $device ) {
		$value = $item[ $key ] ?? ( 'd' === $device ? 0 : -1 );
		if ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || $value < 0 ) { continue; }
		$css .= '--rdct-crs-w-' . $device . ':' . ( 0.0 === (float) $value ? 'max-content' : min( 2000, (float) $value ) . 'px' ) . ';';
	}
	return $css;
}

function render( array $items, array $options = array() ): string {
	$s = settings( $options ); $rows = array();
	foreach ( array_slice( $items, 0, 50 ) as $item ) {
		if ( ! is_array( $item ) ) { continue; }
		$html = ''; $media_id = 0; $link = '';
		$image_size = \RadmanCTComponents\Components\ImageMedia\size( $item['image_id'] ?? null, 'brands' === $s['variant'] ? 'medium' : 'large' );
		if ( ( $item['render'] ?? null ) instanceof \Closure ) {
			ob_start();
			try { $result = $item['render'](); $html = ob_get_contents() . ( is_string( $result ) ? $result : '' ); }
			finally { ob_end_clean(); }
		}
		elseif ( \RadmanCTComponents\Components\CustomContent\key( $item ) ) { $html = \RadmanCTComponents\Components\CustomContent\render( \RadmanCTComponents\Components\CustomContent\key( $item ), array( 'slot' => 'carousel' ) ); }
		elseif ( 'template' === ( $item['content_type'] ?? '' ) || ( ! isset( $item['content_type'] ) && ! empty( $item['template_id'] ) ) ) { $html = \RadmanCTComponents\Components\TemplateLibrary\render( is_numeric( $item['template_id'] ?? null ) ? (int) $item['template_id'] : 0 ); }
		else {
			$image_id = is_array( $item['image_id'] ?? null ) ? ( $item['image_id']['id'] ?? 0 ) : ( $item['image_id'] ?? 0 );
			$id = is_numeric( $image_id ) ? max( 0, (int) $image_id ) : 0;
			$id = $id ? (int) apply_filters( 'wpml_object_id', $id, 'attachment', true ) : 0;
			if ( $id && wp_attachment_is_image( $id ) ) { $media_id = $id; }
			$title = is_string( $item['title'] ?? null ) ? $item['title'] : '';
			if ( '' !== $title ) { $html .= '<h3>' . esc_html( $title ) . '</h3>'; }
			if ( is_string( $item['content'] ?? null ) ) { $html .= wp_kses_post( $item['content'] ); }
			$link = is_string( $item['link'] ?? null ) ? esc_url( $item['link'] ) : '';
			if ( '' !== $html ) { $html = '<div class="rdct-crs__caption">' . $html . '</div>'; }
		}
		if ( $media_id || '' !== trim( $html ) ) { $rows[] = array( 'html' => $html, 'image' => $media_id, 'image_size' => $image_size, 'link' => $link, 'width' => $s['variable_width'] ? item_width_css( $item ) : '' ); }
	}
	$slides = array();
	$initial = $s['autoplay'] && 'reverse' === $s['autoplay_direction'] && 'marquee' !== $s['mode'] ? count( $rows ) - 1 : 0;
	foreach ( $rows as $index => $row ) {
		$html = $row['html'];
		if ( $row['image'] ) { $attrs = array( 'decoding' => 'async' ) + \RadmanCTComponents\Components\ImageLoading\attributes( $s['image_loading'], $index === $initial ); $html = '<div class="rdct-crs__media">' . wp_get_attachment_image( $row['image'], $row['image_size'], false, $attrs ) . '</div>' . $html; }
		if ( '' !== $row['link'] ) { $html = '<a class="rdct-crs__link" href="' . $row['link'] . '">' . $html . '</a>'; }
		$slides[] = '<div class="swiper-slide rdct-crs__slide"' . ( '' !== $row['width'] ? ' style="' . esc_attr( $row['width'] ) . '"' : '' ) . '><div class="rdct-crs__content">' . $html . '</div></div>';
	}
	if ( ! $slides ) { return ''; }
	if ( 'marquee' === $s['mode'] ) { return \RadmanCTComponents\Components\Marquee\render( $slides, $s, 'rdct-crs--' . $s['variant'], height_css( $s ) ); }
	$breakpoints = \RadmanCTComponents\Components\Presentation\breakpoints( array( 'tablet' => 1023, 'mobile' => 767 ) );
	$data = $s + array( 'breakpoints' => array( 'tablet' => $breakpoints['mobile'] + 1, 'desktop' => $breakpoints['tablet'] + 1 ), 'slide_count' => count( $slides ), 'messages' => array( 'slide' => __( 'Slide {{index}} of {{slidesLength}}', 'radmanct-components' ), 'bullet' => __( 'Go to slide {{index}}', 'radmanct-components' ) ) );
	$html = '<section class="rdct-crs rdct-crs--' . esc_attr( $s['variant'] ) . '" data-rdct-carousel data-options="' . esc_attr( wp_json_encode( $data ) ) . '" data-mode="' . esc_attr( $s['mode'] ) . '" data-variable-width="' . ( $s['variable_width'] ? 'true' : 'false' ) . '" data-transition="' . $s['transition'] . '" data-media-motion="' . esc_attr( $s['media_motion'] ) . '" data-height-mode="' . esc_attr( $s['height_mode'] ) . '" style="' . height_css( $s ) . '--rdct-carousel-motion-duration:' . $s['motion_duration'] . 's;--rdct-carousel-motion-scale:' . $s['motion_scale'] . ';--rdct-carousel-gap:' . $s['gap'] . 'px;--rdct-carousel-desktop:' . $s['slides'] . ';--rdct-carousel-tablet:' . $s['slides_tablet'] . ';--rdct-carousel-mobile:' . $s['slides_mobile'] . '" aria-label="' . esc_attr( $s['label'] ) . '">';
    $slots = control_slots( $s );
    if ( ! $s['controls_overlay'] ) { $html .= control_slot( $slots['top'], 'top' ); }
    $html .= '<div class="rdct-crs__frame"><div class="rdct-crs__viewport"><div class="swiper-wrapper">' . implode( '', $slides ) . '</div></div>';
    foreach ( $slots as $position => $slot ) {
        if ( $s['controls_overlay'] || 'middle' === $position ) { $html .= control_slot( $slot, $position, true ); }
    }
    $html .= '</div>';
    if ( ! $s['controls_overlay'] ) { $html .= control_slot( $slots['bottom'], 'bottom' ); }
    return responsive_markup( $html . '</section>', $breakpoints, $s['variable_width'] );
}

function responsive_markup( string $html, array $breakpoints, bool $variable_width ): string {
	if ( array( 'tablet' => 1023, 'mobile' => 767 ) === $breakpoints ) { return $html; }
	$class = 'rdct-crs-r-' . substr( md5( serialize( array( $breakpoints, $variable_width ) ) ), 0, 12 );
	$selector = '.' . $class . '.' . $class;
	$css = '';
	foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
		$columns = '--rdct-carousel-columns:var(--rdct-carousel-' . $device . ',1);';
		$height = 'var(--rdct-carousel-height-desktop,auto)';
		$width = 'var(--rdct-crs-w-d,max-content)';
		if ( 'desktop' !== $device ) { $height = 'var(--rdct-carousel-height-tablet,' . $height . ')'; $width = 'var(--rdct-crs-w-t,' . $width . ')'; }
		if ( 'mobile' === $device ) { $height = 'var(--rdct-carousel-height-mobile,' . $height . ')'; $width = 'var(--rdct-crs-w-m,' . $width . ')'; }
		$block = $selector . '{' . $columns . '--rdct-carousel-height:' . $height . ';}';
		if ( $variable_width ) { $block .= $selector . '[data-variable-width=true] .swiper-slide{--rdct-crs-w:' . $width . ';}'; }
		$css .= 'desktop' === $device ? $block : '@media(max-width:' . $breakpoints[ $device ] . 'px){' . $block . '}';
	}
	return '<style data-rdct-responsive>' . $css . '</style>' . preg_replace( '/class="/', 'class="' . $class . ' ', $html, 1 );
}

function assets( array $options ): array {
    $s = settings( $options );
    $assets = array( 'marquee' === $s['mode'] ? 'marquee' : 'carousel' );
    if ( 'fade' === $s['transition'] ) { $assets[] = 'carousel-fade'; }
    if ( 'stack' === $s['mode'] ) { $assets[] = 'carousel-stack'; }
    if ( $s['variable_width'] ) { $assets[] = 'carousel-width'; }
    if ( 'marquee' !== $s['mode'] && $s['arrows'] && ( 'circle' !== $s['arrow_style'] || 44 !== $s['arrow_size'] || 12 !== $s['arrow_gap'] || '' !== $s['arrow_color'] || '' !== $s['arrow_hover_color'] ) ) { $assets[] = 'carousel-navigation'; }
    if ( 'marquee' !== $s['mode'] && $s['arrows'] ) {
        if ( 'default' !== $s['arrow_fill'] || '' !== $s['arrow_background'] || '' !== $s['arrow_hover_background'] || '' !== $s['arrow_border_color'] || '' !== $s['arrow_hover_border_color'] ) { $assets[] = 'carousel-navigation-surface'; }
        if ( 'none' !== $s['arrow_shadow'] ) { $assets[] = 'carousel-navigation-shadow'; }
    }
    if ( 'content' !== $s['variant'] ) { $assets[] = 'carousel-' . $s['variant']; }
    if ( 'marquee' !== $s['mode'] && 'none' !== $s['media_motion'] ) { $assets[] = 'carousel-motion'; }
    return $assets;
}
