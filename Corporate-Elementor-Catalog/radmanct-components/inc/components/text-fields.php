<?php

namespace RadmanCTComponents\Components\TextReveal;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/marquee.php';

function fields(): array {
	$tags = array( 'p', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	$fonts = array( '' => __( 'Inherit', 'radmanct-components' ) ) + array_combine( array_keys( fonts() ), array_keys( fonts() ) );
	$fields = array(
		'header_colors' => array( 'label' => __( 'Header state colors', 'radmanct-components' ), 'default' => false ),
		'header_sticky_color' => array( 'label' => __( 'Sticky header text color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'header_colors' => true ) ),
		'header_light_color' => array( 'label' => __( 'Text over light sections', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'header_colors' => true ) ),
		'header_dark_color' => array( 'label' => __( 'Text over dark sections', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'header_colors' => true ) ),
		'text' => array( 'label' => __( 'Text', 'radmanct-components' ), 'default' => __( 'Text becomes highlighted as you scroll.', 'radmanct-components' ), 'multiline' => true ),
		'inline_image_width' => array( 'label' => __( 'Inline image width', 'radmanct-components' ), 'default' => 96, 'min' => 0, 'max' => 1000, 'condition' => array( 'mode' => array( 'rich' ) ) ),
		'inline_image_radius' => array( 'label' => __( 'Inline image radius', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 200, 'condition' => array( 'mode' => array( 'rich' ) ) ),
		'suffix' => array( 'label' => __( 'Text after rotating words', 'radmanct-components' ), 'default' => '' ),
		'mode' => array( 'label' => __( 'Text effect', 'radmanct-components' ), 'default' => 'scroll', 'options' => array( 'rich' => __( 'Text and inline images', 'radmanct-components' ), 'static' => __( 'None', 'radmanct-components' ), 'scroll' => __( 'Scroll', 'radmanct-components' ), 'hover' => __( 'Hover and focus', 'radmanct-components' ), 'fill' => __( 'Scroll text fill', 'radmanct-components' ), 'rotate' => __( 'Rotating words', 'radmanct-components' ), 'marquee' => __( 'Marquee', 'radmanct-components' ) ) ),
		'tag' => array( 'label' => __( 'HTML tag', 'radmanct-components' ), 'default' => 'p', 'options' => array_combine( $tags, $tags ) ),
		'direction' => array( 'label' => __( 'Text direction', 'radmanct-components' ), 'default' => 'inherit', 'options' => array( 'inherit' => __( 'Inherit', 'radmanct-components' ), 'auto' => __( 'Automatic', 'radmanct-components' ), 'rtl' => 'RTL', 'ltr' => 'LTR' ) ),
		'opacity' => array( 'label' => __( 'Initial opacity', 'radmanct-components' ), 'default' => .25, 'min' => .1, 'max' => 1, 'step' => .05, 'condition' => array( 'mode' => array( 'scroll' ) ) ),
		'split_type' => array( 'label' => __( 'Reveal units', 'radmanct-components' ), 'default' => 'words', 'options' => array( 'words' => __( 'Words', 'radmanct-components' ), 'lines' => __( 'Lines', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'scroll' ) ) ),
		'line_mask' => array( 'label' => __( 'Mask revealed lines', 'radmanct-components' ), 'default' => false, 'condition' => array( 'mode' => array( 'scroll' ), 'split_type' => array( 'lines' ) ) ),
		'final_opacity' => array( 'label' => __( 'Final opacity', 'radmanct-components' ), 'default' => 1, 'min' => .1, 'max' => 1, 'step' => .05, 'condition' => array( 'mode' => array( 'scroll' ) ) ),
		'scrub' => array( 'label' => __( 'Scroll smoothing', 'radmanct-components' ), 'default' => .5, 'min' => 0, 'max' => 5, 'step' => .1, 'condition' => array( 'mode' => array( 'scroll', 'fill' ) ) ),
		'stagger' => array( 'label' => __( 'Reveal stagger', 'radmanct-components' ), 'default' => .12, 'min' => 0, 'max' => 1, 'step' => .01, 'condition' => array( 'mode' => array( 'scroll' ) ) ),
		'start' => array( 'label' => __( 'Scroll start', 'radmanct-components' ), 'default' => 'top 80%', 'condition' => array( 'mode' => array( 'scroll', 'fill' ) ) ),
		'end' => array( 'label' => __( 'Scroll end', 'radmanct-components' ), 'default' => 'bottom 35%', 'condition' => array( 'mode' => array( 'scroll', 'fill' ) ) ),
		'from' => array( 'label' => __( 'Reveal order', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Reading start', 'radmanct-components' ), 'end' => __( 'Reading end', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'edges' => __( 'Edges', 'radmanct-components' ), 'random' => __( 'Random', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'scroll' ) ) ),
		'rotate_style' => array( 'label' => __( 'Word transition', 'radmanct-components' ), 'default' => 'slide', 'options' => array( 'slide' => __( 'Slide', 'radmanct-components' ), 'basic' => __( 'Fade', 'radmanct-components' ), 'list' => __( 'Word list', 'radmanct-components' ) ), 'condition' => array( 'mode' => array( 'rotate' ) ) ),
		'word_delay' => array( 'label' => __( 'Word hold time (seconds)', 'radmanct-components' ), 'default' => 3.5, 'min' => 1, 'max' => 30, 'step' => .1, 'condition' => array( 'mode' => array( 'rotate' ) ) ),
		'word_duration' => array( 'label' => __( 'Word transition time (seconds)', 'radmanct-components' ), 'default' => .8, 'min' => .1, 'max' => 3, 'step' => .1, 'condition' => array( 'mode' => array( 'rotate' ) ) ),
		'visible_words' => array( 'label' => __( 'Visible words', 'radmanct-components' ), 'default' => 3, 'min' => 1, 'max' => 9, 'condition' => array( 'mode' => array( 'rotate' ), 'rotate_style' => array( 'list' ) ) ),
		'highlight' => array( 'label' => __( 'Highlight shape', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'underline' => __( 'Underline', 'radmanct-components' ), 'fill' => __( 'Background fill', 'radmanct-components' ), 'wave' => __( 'Wave', 'radmanct-components' ), 'double' => __( 'Double underline', 'radmanct-components' ), 'circle' => __( 'Circle', 'radmanct-components' ), 'brush' => __( 'Brush', 'radmanct-components' ) ) ),
		'highlight_animation' => array( 'label' => __( 'Highlight animation', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Reading start', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ), 'fade' => __( 'Fade', 'radmanct-components' ) ) ),
		'highlight_hover' => array( 'label' => __( 'Fill highlight on hover', 'radmanct-components' ), 'default' => false ),
		'highlight_duration' => array( 'label' => __( 'Highlight duration (seconds)', 'radmanct-components' ), 'default' => .7, 'min' => .1, 'max' => 10, 'step' => .1, 'condition' => array( 'highlight' => array( 'underline', 'fill', 'double', 'circle', 'brush' ), 'mode' => array( 'static', 'hover', 'rotate', 'marquee', 'rich' ) ) ),
		'highlight_delay' => array( 'label' => __( 'Highlight delay (seconds)', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 10, 'step' => .1, 'condition' => array( 'highlight' => array( 'underline', 'fill', 'double', 'circle', 'brush' ), 'mode' => array( 'static', 'hover', 'rotate', 'marquee', 'rich' ) ) ),
		'outline' => array( 'label' => __( 'Text outline', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'static' => __( 'Always', 'radmanct-components' ), 'hover' => __( 'On hover', 'radmanct-components' ) ) ),
		'outline_width' => array( 'label' => __( 'Outline width (px)', 'radmanct-components' ), 'default' => 1, 'min' => .1, 'max' => 8, 'step' => .1 ),
		'nowrap' => array( 'label' => __( 'Keep text on one line', 'radmanct-components' ), 'default' => false ),
		'writing_mode' => array( 'label' => __( 'Writing mode', 'radmanct-components' ), 'default' => 'horizontal-tb', 'options' => array( 'horizontal-tb' => __( 'Horizontal', 'radmanct-components' ), 'vertical-rl' => __( 'Vertical right to left', 'radmanct-components' ), 'vertical-lr' => __( 'Vertical left to right', 'radmanct-components' ) ) ),
		'gradient' => array( 'label' => __( 'Gradient text', 'radmanct-components' ), 'default' => false ),
		'gradient_end' => array( 'label' => __( 'Text gradient end', 'radmanct-components' ), 'default' => '#5667df', 'color' => true ),
		'gradient_angle' => array( 'label' => __( 'Gradient angle', 'radmanct-components' ), 'default' => 90, 'min' => 0, 'max' => 360 ),
		'outline_color' => array( 'label' => __( 'Outline color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true ),
		'url' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '' ),
		'external' => array( 'label' => __( 'Open in new tab', 'radmanct-components' ), 'default' => false ),
		'nofollow' => array( 'label' => __( 'Nofollow', 'radmanct-components' ), 'default' => false ),
		'font_family' => array( 'label' => __( 'Text font family', 'radmanct-components' ), 'default' => '', 'options' => $fonts ),
		'word_font_family' => array( 'label' => __( 'Rotating word font family', 'radmanct-components' ), 'default' => '', 'options' => $fonts ),
		'idle' => array( 'label' => __( 'Initial text color', 'radmanct-components' ), 'default' => '#667085', 'color' => true ),
		'active' => array( 'label' => __( 'Highlighted text color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true ),
		'highlight_color' => array( 'label' => __( 'Highlight color', 'radmanct-components' ), 'default' => '#f5ca76', 'color' => true ),
		'word_color' => array( 'label' => __( 'Rotating word color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true ),
		'word_active_end' => array( 'label' => __( 'Active word gradient end', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true ),
	);
	foreach ( \RadmanCTComponents\Components\Marquee\fields() as $key => $field ) { $fields[ $key ] = $field + array( 'condition' => array( 'mode' => array( 'marquee' ) ) ); }
	$typography = segment_fields();
	foreach ( array( 'font_weight', 'font_style', 'text_transform', 'text_decoration' ) as $key ) { $fields[ $key ] = $typography[ $key ]; }
	return $fields;
}

function word_fields(): array {
	return array( 'title' => array( 'label' => __( 'Word or phrase', 'radmanct-components' ), 'default' => '' ), 'color' => array( 'label' => __( 'Word color', 'radmanct-components' ), 'default' => '', 'color' => true ) );
}

function groups(): array {
	$groups = array(
		'typography' => array( 'label' => __( 'Typography', 'radmanct-components' ), 'fields' => array( 'font_weight', 'font_style', 'text_transform', 'text_decoration' ) ),
		'header' => array( 'label' => __( 'Header state colors', 'radmanct-components' ), 'fields' => array( 'header_colors', 'header_sticky_color', 'header_light_color', 'header_dark_color' ) ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'fields' => array( 'text', 'mode', 'tag', 'direction', 'nowrap', 'writing_mode', 'suffix', 'url', 'external', 'nofollow' ) ),
		'segments' => array( 'label' => __( 'Text segments', 'radmanct-components' ), 'fields' => array( 'inline_image_width', 'inline_image_radius' ) ),
		'scroll' => array( 'label' => __( 'Scroll', 'radmanct-components' ), 'fields' => array( 'split_type', 'line_mask', 'opacity', 'final_opacity', 'start', 'end', 'scrub', 'stagger', 'from' ) ),
		'words' => array( 'label' => __( 'Rotating words', 'radmanct-components' ), 'fields' => array( 'rotate_style', 'word_delay', 'word_duration', 'visible_words', 'word_color', 'word_active_end' ) ),
		'marquee' => array( 'label' => __( 'Marquee', 'radmanct-components' ), 'fields' => array_keys( \RadmanCTComponents\Components\Marquee\fields() ) ),
		'appearance' => array( 'label' => __( 'Text style', 'radmanct-components' ), 'fields' => array( 'font_family', 'word_font_family', 'idle', 'active', 'gradient', 'gradient_end', 'gradient_angle', 'outline', 'outline_width', 'outline_color' ) ),
		'highlight' => array( 'label' => __( 'Text highlight', 'radmanct-components' ), 'fields' => array( 'highlight', 'highlight_color', 'highlight_animation', 'highlight_hover', 'highlight_duration', 'highlight_delay', 'highlight_width', 'highlight_height', 'highlight_offset', 'highlight_inline_offset', 'highlight_radius' ) ),
	);
	$fields = array();
	foreach ( $groups['highlight']['fields'] as $key ) {
		$fields[] = $key;
		if ( in_array( $key, array( 'highlight_width', 'highlight_height', 'highlight_offset', 'highlight_inline_offset', 'highlight_radius' ), true ) ) { $fields[] = $key . '_unit'; }
	}
	$groups['highlight']['fields'] = $fields;
	return $groups;
}

function fonts(): array {
	$fonts = apply_filters( 'radmanct_components/text_fonts', array() );
	return is_array( $fonts ) ? array_filter( $fonts, static fn( $family ) => is_string( $family ) && preg_match( '/^[a-zA-Z0-9 _-]{1,80}$/D', $family ) ) : array();
}

function uses_effects( array $options ): bool {
    foreach ( array( 'font_weight', 'font_style', 'text_transform', 'text_decoration' ) as $key ) { if ( ! empty( $options[ $key ] ) ) { return true; } }
    if ( ! empty( $options['header_colors'] ) ) { return true; }
    return in_array( $options['mode'] ?? '', array( 'static', 'fill', 'rotate', 'rich' ), true ) || ( isset( $options['highlight'] ) && 'none' !== $options['highlight'] ) || ( isset( $options['outline'] ) && 'none' !== $options['outline'] ) || ! empty( $options['nowrap'] ) || ! empty( $options['font_family'] ) || ! empty( $options['word_font_family'] ) || ! empty( $options['url'] ) || ! empty( $options['gradient'] ) || ( isset( $options['writing_mode'] ) && 'horizontal-tb' !== $options['writing_mode'] );
}

function assets( array $options ): array {
    $s = settings( $options );
    $assets = array( 'text-reveal' );
    if ( $s['header_colors'] ) { $assets[] = 'text-header-state'; }
    if ( uses_effects( $options ) || 'marquee' === ( $options['mode'] ?? '' ) ) { $assets[] = 'text-effects'; }
    if ( 'marquee' === ( $options['mode'] ?? '' ) ) { $assets[] = 'marquee'; }
    if ( 'rich' === ( $options['mode'] ?? '' ) ) {
        $assets[] = 'text-segments';
        foreach ( array_slice( is_array( $options['segments'] ?? null ) ? $options['segments'] : array(), 0, 30 ) as $row ) {
            if ( ! is_array( $row ) ) { continue; }
            if ( ! empty( $row['image']['id'] ) ) { $assets[] = 'text-inline-image'; }
            if ( 'gradient' === ( $row['paint'] ?? '' ) ) { $assets[] = 'text-segment-gradient'; }
            if ( 'image' === ( $row['paint'] ?? '' ) ) { $assets[] = 'text-segment-mask'; }
        }
    }
    if ( 'none' !== $s['highlight'] ) {
        $assets[] = 'text-highlight';
        if ( 'underline' !== $s['highlight'] ) { $assets[] = 'text-highlight-' . $s['highlight']; }
    }
    if ( 'fill' === $s['mode'] ) { $assets[] = 'text-fill'; }
    if ( 'rotate' === $s['mode'] ) { $assets[] = 'text-rotate'; if ( 'list' === $s['rotate_style'] ) { $assets[] = 'text-rotate-list'; } }
    return array_values( array_unique( $assets ) );
}
