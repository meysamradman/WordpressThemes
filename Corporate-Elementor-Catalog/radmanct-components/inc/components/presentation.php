<?php

namespace RadmanCTComponents\Components\Presentation;

defined( 'ABSPATH' ) || exit;

function breakpoints( array $fallback = array( 'tablet' => 1024, 'mobile' => 767 ) ): array {
	$result = $fallback;
	if ( class_exists( '\\Elementor\\Plugin', false ) && isset( \Elementor\Plugin::$instance->breakpoints ) ) {
		foreach ( \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints() as $name => $breakpoint ) {
			if ( ! isset( $result[ $name ] ) ) { continue; }
			$value = $breakpoint->get_value();
			if ( is_numeric( $value ) && is_finite( (float) $value ) && $value >= 1 && $value <= 10000 ) { $result[ $name ] = (int) $value; }
		}
	}
	return $result['mobile'] < $result['tablet'] ? $result : $fallback;
}

function controls( string $type ): array {
	if ( 'button' === $type ) { require_once __DIR__ . '/button/fields.php'; return \RadmanCTComponents\Components\Button\presentation(); }
	$align = array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) );
	$maps = array(
		'text-segment-shadow' => array( 'shadow' => array( 'shadow' => true, 'selector' => '& .rdct-text__part', 'css' => '--rdct-segment-shadow:%s;' ) ),
		'text-segment-image' => array(
			'font_size' => array( 'label' => __( 'Font size', 'radmanct-components' ), 'selector' => '& .rdct-text__part', 'css' => 'font-size:%s;', 'group' => 'text', 'units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), 'min' => 0, 'max' => 1000 ),
			'word_spacing' => array( 'label' => __( 'Word spacing', 'radmanct-components' ), 'selector' => '& .rdct-text__part', 'css' => 'word-spacing:%s;', 'group' => 'text', 'units' => array( 'px', 'em', 'rem' ), 'min' => -20, 'max' => 100 ),
			'mask_width' => array( 'label' => __( 'Fill image width', 'radmanct-components' ), 'selector' => '& .rdct-text__part[data-paint=image][data-mask-size=custom]', 'css' => '--rdct-segment-mask-width:%s;', 'group' => 'text', 'condition' => array( 'paint' => 'image', 'mask_size' => 'custom' ), 'units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), 'min' => 0, 'max' => 4000 ),
			'mask_height' => array( 'label' => __( 'Fill image height', 'radmanct-components' ), 'selector' => '& .rdct-text__part[data-paint=image][data-mask-size=custom]', 'css' => '--rdct-segment-mask-height:%s;', 'group' => 'text', 'condition' => array( 'paint' => 'image', 'mask_size' => 'custom' ), 'units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), 'min' => 0, 'max' => 4000 ),
			'mask_x' => array( 'label' => __( 'Fill image horizontal position', 'radmanct-components' ), 'selector' => '& .rdct-text__part[data-paint=image][data-mask-position=custom]', 'css' => '--rdct-segment-mask-x:%s;', 'group' => 'text', 'condition' => array( 'paint' => 'image', 'mask_position' => 'custom' ), 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => -4000, 'max' => 4000 ),
			'mask_y' => array( 'label' => __( 'Fill image vertical position', 'radmanct-components' ), 'selector' => '& .rdct-text__part[data-paint=image][data-mask-position=custom]', 'css' => '--rdct-segment-mask-y:%s;', 'group' => 'text', 'condition' => array( 'paint' => 'image', 'mask_position' => 'custom' ), 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => -4000, 'max' => 4000 ),
			'gradient_position' => array( 'direct' => true, 'default' => 'center center', 'condition' => array( 'paint' => 'gradient', 'gradient_type' => 'radial' ), 'label' => __( 'Radial gradient position', 'radmanct-components' ), 'group' => 'text', 'selector' => '& .rdct-text__part[data-gradient-type=radial]', 'css' => '--rdct-segment-gradient-position:%s;', 'options' => array( 'center center' => __( 'Center', 'radmanct-components' ), 'center left' => __( 'Center left', 'radmanct-components' ), 'center right' => __( 'Center right', 'radmanct-components' ), 'top center' => __( 'Top center', 'radmanct-components' ), 'top left' => __( 'Top left', 'radmanct-components' ), 'top right' => __( 'Top right', 'radmanct-components' ), 'bottom center' => __( 'Bottom center', 'radmanct-components' ), 'bottom left' => __( 'Bottom left', 'radmanct-components' ), 'bottom right' => __( 'Bottom right', 'radmanct-components' ) ) ),
			'line_height' => array( 'selector' => '& .rdct-text__part', 'css' => 'line-height:%s;', 'group' => 'text', 'units' => array( 'px', 'em', 'rem', 'lh', 'rlh' ), 'default_unit' => 'em', 'min' => 0, 'max' => 500, 'range' => array( 'px' => array( 'min' => 1, 'max' => 500 ), 'em' => array( 'min' => 0.1, 'max' => 10 ), 'rem' => array( 'min' => 0.1, 'max' => 20 ), 'lh' => array( 'min' => 0.1, 'max' => 10 ), 'rlh' => array( 'min' => 0.1, 'max' => 10 ) ) ),
			'letter_spacing' => array( 'selector' => '& .rdct-text__part', 'css' => 'letter-spacing:%s;', 'group' => 'text', 'units' => array( 'px', 'em', 'rem' ), 'min' => -5, 'max' => 10, 'range' => array( 'px' => array( 'min' => -5, 'max' => 10 ), 'em' => array( 'min' => -1, 'max' => 1 ), 'rem' => array( 'min' => -1, 'max' => 1 ) ) ),
			'image_width' => array( 'selector' => '& .rdct-text__figure', 'css' => 'inline-size:%s;', 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => 0, 'max' => 2000 ),
			'image_radius' => array( 'selector' => '& .rdct-text__figure > .rdct-text__image', 'css' => '--rdct-text-image-radius:%s;', 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => 0, 'max' => 200 ),
		),
		'text-reveal' => array(
			'font_size' => array( 'label' => __( 'Font size', 'radmanct-components' ), 'selector' => '&', 'css' => 'font-size:%s;', 'units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), 'min' => 0, 'max' => 1000 ),
			'word_spacing' => array( 'label' => __( 'Word spacing', 'radmanct-components' ), 'selector' => '&', 'css' => 'word-spacing:%s;', 'units' => array( 'px', 'em', 'rem' ), 'min' => -20, 'max' => 100 ),
			'line_height' => array( 'label' => __( 'Line height', 'radmanct-components' ), 'selector' => '&', 'css' => 'line-height:%s;', 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => 0, 'max' => 1000 ),
			'letter_spacing' => array( 'label' => __( 'Letter spacing', 'radmanct-components' ), 'selector' => '&', 'css' => 'letter-spacing:%s;', 'units' => array( 'px', 'em', 'rem' ), 'min' => -20, 'max' => 100 ),
			'inline_image_width' => array( 'label' => __( 'Inline image width', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-image-width:%spx;', 'min' => 0, 'max' => 1000, 'default' => 96, 'condition' => array( 'mode' => array( 'rich' ) ) ),
			'inline_image_radius' => array( 'label' => __( 'Inline image radius', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-image-radius:%spx;', 'min' => 0, 'max' => 200, 'default' => 12, 'condition' => array( 'mode' => array( 'rich' ) ) ),
			'text_size' => array( 'label' => __( 'Text size', 'radmanct-components' ), 'selector' => '&', 'css' => 'font-size:%spx;', 'min' => 8, 'max' => 240, 'default' => 24 ),
			'word_size' => array( 'label' => __( 'Rotating word size', 'radmanct-components' ), 'selector' => '& .rdct-text__word', 'css' => 'font-size:%spx;', 'min' => 8, 'max' => 240, 'default' => 24 ),
			'highlight_width' => array( 'label' => __( 'Highlight width (%)', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-highlight-width:%s%;', 'min' => 0, 'max' => 200, 'unit' => '%' ),
			'highlight_height' => array( 'label' => __( 'Highlight height (px)', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-highlight-height:%spx;', 'min' => 0, 'max' => 240 ),
			'highlight_offset' => array( 'label' => __( 'Highlight bottom offset (px)', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-highlight-offset:%spx;', 'min' => -200, 'max' => 200 ),
			'highlight_inline_offset' => array( 'label' => __( 'Highlight reading-start offset (px)', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-highlight-inline-offset:%spx;', 'min' => -200, 'max' => 200 ),
			'highlight_radius' => array( 'label' => __( 'Highlight corner radius (px)', 'radmanct-components' ), 'selector' => '&', 'css' => '--rdct-text-highlight-radius:%spx;', 'min' => 0, 'max' => 200 ),
			'visible_words' => array( 'selector' => '&', 'css' => '--rdct-text-visible:%s !important;', 'min' => 1, 'max' => 9 ),
			'align' => array( 'label' => __( 'Text alignment', 'radmanct-components' ), 'default' => 'start', 'selector' => '&', 'css' => 'text-align:%s;', 'options' => $align ),
		),
		'curtain' => array( 'gap' => array( 'selector' => '&', 'css' => 'gap:%spx;', 'min' => 0, 'max' => 64 ), 'padding' => array( 'selector' => '& .rdct-curtain__body', 'css' => 'padding:%spx;', 'min' => 0, 'max' => 120 ) ),
		'icon' => array( 'size' => array( 'selector' => '&', 'css' => 'inline-size:%spx;block-size:%spx;', 'min' => 8, 'max' => 128 ), 'stroke' => array( 'selector' => '&', 'css' => 'stroke-width:%s;', 'min' => 1, 'max' => 3, 'step' => .25 ), 'icon_align' => array( 'base' => true, 'selector' => '&', 'options' => $align, 'default' => 'start' ) ),
		'icon-box' => array(
			'icon_size' => array( 'selector' => '& .rdct-ib__icon > :is(svg,img)', 'css' => 'inline-size:%spx;block-size:%spx;', 'min' => 8, 'max' => 128 ),
			'icon_padding' => array( 'selector' => '& .rdct-ib__icon', 'css' => 'padding:%spx;', 'min' => 0, 'max' => 80 ),
			'border_width' => array( 'selector' => '& .rdct-ib__icon', 'css' => 'border-width:%spx;', 'min' => 0, 'max' => 8 ),
			'gap' => array( 'selector' => '&', 'css' => '--rdct-ib-space:%spx;column-gap:%spx;', 'min' => 0, 'max' => 120 ),
			'title_gap' => array( 'selector' => '&', 'css' => 'row-gap:%spx;', 'min' => 0, 'max' => 100 ),
			'title_size' => array( 'label' => __( 'Title size', 'radmanct-components' ), 'default' => 24, 'selector' => '& .rdct-ib__title', 'css' => 'font-size:%spx;', 'min' => 8, 'max' => 120 ),
			'text_size' => array( 'label' => __( 'Description size', 'radmanct-components' ), 'default' => 16, 'selector' => '& .rdct-ib__content', 'css' => 'font-size:%spx;', 'min' => 8, 'max' => 72 ),
			'align' => array( 'selector' => '&', 'css' => 'text-align:%s;', 'options' => $align + array( 'justify' => __( 'Justify', 'radmanct-components' ) ), 'icon_box' => true ),
			'icon_side' => array( 'base' => true, 'selector' => '&', 'css' => '', 'options' => array( 'start' => $align['start'], 'end' => $align['end'] ) ),
			'vertical_align' => array( 'base' => true, 'selector' => '&', 'css' => '--rdct-ib-valign:%s;', 'options' => array( 'auto' => __( 'Default', 'radmanct-components' ), 'start' => __( 'Top', 'radmanct-components' ), 'center' => __( 'Middle', 'radmanct-components' ), 'end' => __( 'Bottom', 'radmanct-components' ) ) ),
			'layout' => array( 'options' => array( 'top' => __( 'Icon on top', 'radmanct-components' ), 'inline' => __( 'Icon and heading inline', 'radmanct-components' ), 'side' => __( 'Icon and content inline', 'radmanct-components' ) ) ),
		),
		'box' => array(),
		'flipbox' => array( 'height' => array( 'selector' => '&', 'css' => '--rdct-fb-height:%spx;', 'min' => 100, 'max' => 1600 ) ),
		'table' => array( 'padding' => array( 'selector' => '& :is(th,td)', 'css' => 'padding:%spx;', 'min' => 0, 'max' => 64 ), 'min_width' => array( 'selector' => '& table', 'css' => 'min-inline-size:%spx;', 'min' => 0, 'max' => 2400 ), 'align' => array( 'selector' => '& :is(th,td)', 'css' => 'text-align:%s;', 'options' => $align ) ),
	);
	foreach ( array( 'title_size' => '.rdct-box__title', 'label_size' => '.rdct-box__label', 'text_size' => '.rdct-box__text' ) as $key => $selector ) { $maps['box'][ $key ] = array( 'selector' => '& ' . $selector, 'css' => 'font-size:%spx;', 'min' => 8, 'max' => 200 ); }
	foreach ( array( 'start-start' => __( 'Image top start corner', 'radmanct-components' ), 'start-end' => __( 'Image top end corner', 'radmanct-components' ), 'end-start' => __( 'Image bottom start corner', 'radmanct-components' ), 'end-end' => __( 'Image bottom end corner', 'radmanct-components' ) ) as $corner => $label ) {
		$maps['text-segment-image'][ 'image_radius_' . str_replace( '-', '_', $corner ) ] = array( 'label' => $label, 'selector' => '& .rdct-text__figure > .rdct-text__image', 'css' => '--rdct-text-image-radius-' . $corner . ':%s;', 'units' => array( 'px', '%', 'em', 'rem' ), 'min' => 0, 'max' => 200 );
	}
	foreach ( array( 'width' => __( 'Image border width', 'radmanct-components' ), 'top' => __( 'Image top border', 'radmanct-components' ), 'bottom' => __( 'Image bottom border', 'radmanct-components' ), 'start' => __( 'Image start border', 'radmanct-components' ), 'end' => __( 'Image end border', 'radmanct-components' ) ) as $edge => $label ) {
		$maps['text-segment-image'][ 'image_border_' . $edge ] = array( 'label' => $label, 'direct' => true, 'unit_field' => 'image_border_unit', 'selector' => '& .rdct-text__figure > .rdct-text__image', 'css' => '--rdct-text-image-border-' . $edge . ':%s;', 'min' => 0, 'max' => 20, 'default' => 'width' === $edge ? 0 : -1 );
	}
	foreach ( $maps['text-reveal'] as $key => &$spec ) {
		if ( str_starts_with( $key, 'highlight_' ) ) { $spec['condition'] = array( 'highlight' => array( 'underline', 'fill', 'double', 'circle', 'brush' ), 'mode' => array( 'static', 'hover', 'rotate', 'marquee', 'rich' ) ); }
	}
	unset( $spec );
	foreach ( array( 'width' => __( 'Highlight width', 'radmanct-components' ), 'height' => __( 'Highlight height', 'radmanct-components' ), 'offset' => __( 'Highlight bottom offset', 'radmanct-components' ), 'inline_offset' => __( 'Highlight reading-start offset', 'radmanct-components' ), 'radius' => __( 'Highlight corner radius', 'radmanct-components' ) ) as $dimension => $label ) {
		$key = 'highlight_' . $dimension;
		$spec = &$maps['text-reveal'][ $key ];
		$spec['label'] = $label;
		$spec['unit_field'] = $key . '_unit';
		$spec['allowed_units'] = array( 'px', '%', 'em', 'rem', 'vw', 'vh' );
		$spec['default_unit'] = 'width' === $dimension ? '%' : 'px';
		$spec['default'] = null;
		$spec['css'] = '--rdct-text-highlight-' . str_replace( '_', '-', $dimension ) . ':%s;';
		$maps['text-reveal'][ $key . '_unit' ] = array( 'label' => $spec['label'] . ' — ' . __( 'Unit', 'radmanct-components' ), 'selector' => '&', 'css' => '', 'options_ui' => 'select', 'options' => array_combine( $spec['allowed_units'], $spec['allowed_units'] ), 'condition' => $spec['condition'] );
		unset( $spec );
	}
	if ( in_array( $type, array( 'testimonial', 'carousel-testimonials' ), true ) ) { require_once __DIR__ . '/testimonial/appearance.php'; return \RadmanCTComponents\Components\Testimonial\presentation(); }
	return $maps[ $type ] ?? array();
}

function unwrap( $value ) {
	return is_array( $value ) && isset( $value['$$type'] ) ? ( ! empty( $value['disabled'] ) ? null : ( $value['value'] ?? null ) ) : $value;
}

function value( array $input, string $key, string $device, array $spec ) {
	if ( ! empty( $spec['shadow'] ) ) { return 'desktop' === $device ? null : \RadmanCTComponents\Components\TextReveal\segment_shadow_value( $input, $device ); }
	if ( isset( $spec['unit_field'] ) ) { return number_with_unit( $input, $key, $device, $spec ); }
	if ( isset( $spec['units'] ) ) {
		$raw = unwrap( $input[ $key . '_responsive' ] ?? null );
		$value = is_array( $raw ) && array_key_exists( $device, $raw ) ? unwrap( $raw[ $device ] ) : unwrap( $input[ $key . ( 'desktop' === $device ? '' : '_' . $device ) ] ?? null );
		if ( 'desktop' === $device && is_array( $raw ) && array_key_exists( 'size', $raw ) ) { $value = $raw; }
		if ( 'desktop' !== $device && array_key_exists( $key . '_responsive_' . $device, $input ) ) { $value = unwrap( $input[ $key . '_responsive_' . $device ] ); }
		if ( is_numeric( $value ) ) { $value = array( 'size' => $value, 'unit' => $spec['default_unit'] ?? 'px' ); }
		if ( ! is_array( $value ) ) { return null; }
		$size = unwrap( $value['size'] ?? null );
		$unit = unwrap( $value['unit'] ?? null );
		if ( ! is_string( $unit ) || ! in_array( $unit, $spec['units'], true ) || ! is_numeric( $size ) || ! is_finite( (float) $size ) ) { return null; }
		$range = $spec['range'][ $unit ] ?? $spec;
		return max( $range['min'], min( $range['max'], (float) $size ) ) . $unit;
	}
	$raw = unwrap( $input[ $key . '_responsive' ] ?? null );
	if ( ! empty( $spec['direct'] ) ) {
		$value = is_array( $raw ) && array_key_exists( $device, $raw ) ? unwrap( $raw[ $device ] ) : unwrap( $input[ $key . ( 'desktop' === $device ? '' : '_' . $device ) ] ?? null );
		if ( 'desktop' === $device && is_array( $raw ) && array_key_exists( 'size', $raw ) ) { $value = unwrap( $raw['size'] ); }
		elseif ( 'desktop' === $device && ( is_string( $raw ) || is_numeric( $raw ) ) ) { $value = $raw; }
		elseif ( 'desktop' !== $device && array_key_exists( $key . '_responsive_' . $device, $input ) ) { $classic = unwrap( $input[ $key . '_responsive_' . $device ] ); $value = is_array( $classic ) ? unwrap( $classic['size'] ?? null ) : $classic; }
		if ( isset( $spec['options'] ) ) { return is_string( $value ) && '' !== $value && isset( $spec['options'][ $value ] ) ? $value : null; }
		return is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 0 ? max( $spec['min'], min( $spec['max'], (float) $value ) ) : null;
	}
	$value = is_array( $raw ) && array_key_exists( $device, $raw ) ? unwrap( $raw[ $device ] ) : null;
	if ( is_array( $raw ) && array_key_exists( 'size', $raw ) && 'desktop' === $device ) { $value = $raw['size']; }
	if ( 'desktop' !== $device ) { $classic = $input[ $key . '_responsive_' . $device ] ?? null; if ( null !== $classic ) { $value = is_array( $classic ) ? ( $classic['size'] ?? null ) : $classic; } }
	if ( 'desktop' === $device && ( null === $value || '' === $value ) && ! empty( $spec['base'] ) ) { $value = unwrap( $input[ $key ] ?? null ); }
	if ( null === $value || '' === $value ) { return null; }
	if ( isset( $spec['options'] ) ) { return is_string( $value ) && isset( $spec['options'][ $value ] ) ? $value : null; }
	return is_numeric( $value ) && is_finite( (float) $value ) ? max( $spec['min'], min( $spec['max'], (float) $value ) ) : null;
}

function number_with_unit( array $input, string $key, string $device, array $spec ) {
	$number = $spec['default']; $unit = $spec['default_unit'] ?? 'px'; $changed = false;
	$minimum = min( array_merge( array( $spec['min'] ), array_column( $spec['range'] ?? array(), 'min' ) ) );
	foreach ( 'button' === ( $spec['component'] ?? '' ) || 'laptop' === $device ? array( 'desktop', 'laptop', 'tablet', 'mobile' ) : array( 'desktop', 'tablet', 'mobile' ) as $current ) {
		$changed = false;
		foreach ( array( $key, $spec['unit_field'] ) as $field ) {
			$responsive = unwrap( $input[ $field . '_responsive' ] ?? null );
			$raw = is_array( $responsive ) && array_key_exists( $current, $responsive ) ? unwrap( $responsive[ $current ] ) : unwrap( $input[ $field . ( 'desktop' === $current ? '' : '_' . $current ) ] ?? null );
			$slider = null;
			if ( 'desktop' === $current && is_array( $responsive ) && array_key_exists( 'size', $responsive ) ) { $slider = $responsive; $raw = unwrap( $responsive['size'] ); }
			elseif ( 'desktop' === $current && ( is_string( $responsive ) || is_numeric( $responsive ) ) ) { $raw = $responsive; }
			elseif ( 'desktop' !== $current && array_key_exists( $field . '_responsive_' . $current, $input ) ) { $raw = unwrap( $input[ $field . '_responsive_' . $current ] ); if ( is_array( $raw ) ) { $slider = $raw; $raw = unwrap( $raw['size'] ?? null ); } }
			if ( $field === $key && is_array( $raw ) && array_key_exists( 'size', $raw ) ) { $slider = $raw; $raw = unwrap( $raw['size'] ); }
			if ( $field === $key && is_array( $slider ) && is_numeric( $raw ) && $raw >= $minimum && in_array( $slider['unit'] ?? null, $spec['allowed_units'] ?? array( 'px', 'em', 'rem' ), true ) ) { $unit = $slider['unit']; }
			if ( $field === $key && is_numeric( $raw ) && is_finite( (float) $raw ) && (float) $raw >= $minimum ) { $number = (float) $raw; $changed = true; }
			elseif ( $field !== $key && is_string( $raw ) && in_array( $raw, $spec['allowed_units'] ?? array( 'px', 'em', 'rem' ), true ) ) { $unit = $raw; $changed = true; }
		}
		if ( $current === $device ) { break; }
	}
	$range = $spec['range'][ $unit ] ?? $spec;
	return $changed && null !== $number && $number >= $minimum ? max( $range['min'], min( $range['max'], $number ) ) . $unit : null;
}

function declarations( string $key, $value, array $spec ): array {
	if ( isset( $spec['zero_css'] ) && ( is_numeric( $value ) || preg_match( '/^0(?:\.0+)?(?:px|em|rem)$/D', (string) $value ) ) && 0.0 === (float) $value ) { return array( $spec['selector'] => $spec['zero_css'] ); }
	if ( 'icon_align' === $key ) {
		return array( '&' => 'display:block;margin-inline-start:' . ( 'start' === $value ? '0' : 'auto' ) . ';margin-inline-end:' . ( 'end' === $value ? '0' : 'auto' ) . ';' );
	}
	if ( 'icon_side' === $key ) {
		return array( '&' => '--rdct-ib-columns:' . ( 'end' === $value ? 'minmax(0,1fr) auto' : 'auto minmax(0,1fr)' ) . ';--rdct-ib-icon-col:' . ( 'end' === $value ? '2' : '1' ) . ';--rdct-ib-text-col:' . ( 'end' === $value ? '1' : '2' ) . ';' );
	}
	if ( 'layout' === $key ) {
		$top = 'top' === $value; $inline = 'inline' === $value;
		$scope = '&:has(> .rdct-ib__icon)';
		return array( $scope => 'grid-template-columns:' . ( $top ? 'minmax(0,1fr)' : 'var(--rdct-ib-columns,auto minmax(0,1fr))' ) . ';align-items:var(--rdct-ib-valign,' . ( $inline ? 'center' : 'start' ) . ');', $scope . ' .rdct-ib__icon' => 'grid-column:' . ( $top ? '1' : 'var(--rdct-ib-icon-col,1)' ) . ';grid-row:' . ( 'side' === $value ? '1 / span 2' : '1' ) . ';align-self:var(--rdct-ib-valign,' . ( $inline ? 'center' : 'start' ) . ');margin-block-end:' . ( $top ? 'var(--rdct-ib-space,var(--rdct-ib-gap,20px))' : '0' ) . ';', $scope . ' .rdct-ib__title' => 'grid-column:' . ( $top ? '1' : 'var(--rdct-ib-text-col,2)' ) . ';grid-row:' . ( $top ? '2' : '1' ) . ';', $scope . ' .rdct-ib__content' => 'grid-column:' . ( $top ? '1' : ( $inline ? '1 / -1' : 'var(--rdct-ib-text-col,2)' ) ) . ';grid-row:' . ( $top ? '3' : '2' ) . ';' );
	}
	$css = str_replace( '%s', (string) $value, $spec['css'] );
	if ( 'vertical_align' === $key && 'auto' === $value ) { $css = '--rdct-ib-valign:initial;'; }
	if ( 'align' === $key ) { $css .= ! empty( $spec['icon_box'] ) ? '--rdct-ib-align:' . ( 'justify' === $value ? 'start' : $value ) . ';' : 'justify-items:' . $value . ';'; }
	return array( $spec['selector'] => $css );
}

function inline_defaults( string $html, array &$rules ): string {
	$properties = array();
	foreach ( $rules as $selectors ) {
		preg_match_all( '/(?:^|;)\s*([\w-]+)\s*:/', $selectors['&'] ?? '', $matches );
		foreach ( $matches[1] as $property ) { $properties[ $property ] = true; }
	}
	if ( ! $properties ) { return $html; }
	$defaults = '';
	$html = preg_replace_callback( '/\A(<[a-z][^>]*\sstyle=")([^"]*)(")/i', static function ( $match ) use ( $properties, &$defaults ): string {
		$value = preg_replace_callback( '/(^|;)\s*([\w-]+)\s*:\s*([^;]*)(?=;|$)/', static function ( $declaration ) use ( $properties, &$defaults ): string {
			if ( ! isset( $properties[ $declaration[2] ] ) || str_contains( $declaration[3], '&' ) ) { return $declaration[0]; }
			$defaults .= $declaration[2] . ':' . $declaration[3] . ';';
			return $declaration[1];
		}, $match[2] );
		return $match[1] . $value . $match[3];
	}, $html, 1 );
	if ( $defaults ) { $rules['desktop']['&'] = $defaults . ( $rules['desktop']['&'] ?? '' ); }
	return $html;
}

function render( string $html, array $input, string $type, ?array &$styles = null ): string {
	$rules = array();
	foreach ( 'button' === $type ? array( 'desktop', 'laptop', 'tablet', 'mobile' ) : array( 'desktop', 'tablet', 'mobile' ) as $device ) {
		$rules[ $device ] = array();
		foreach ( controls( $type ) as $key => $spec ) {
			$value = value( $input, $key, $device, $spec );
			if ( null === $value ) { continue; }
			foreach ( declarations( $key, $value, $spec ) as $selector => $css ) { $rules[ $device ][ $selector ] = ( $rules[ $device ][ $selector ] ?? '' ) . $css; }
		}
	}
	if ( ! array_filter( $rules ) ) { return $html; }
	$html = inline_defaults( $html, $rules );
	$breakpoints = 'button' === $type ? breakpoints( array( 'laptop' => 1439, 'tablet' => 1024, 'mobile' => 767 ) ) : breakpoints();
	$class = 'rdct-r-' . substr( md5( serialize( array( $type, $rules, $breakpoints ) ) ), 0, 12 );
	$css = '';
	foreach ( $rules as $device => $selectors ) {
		$block = '';
		foreach ( $selectors as $selector => $declaration ) { $block .= str_replace( '&', '.' . $class . '.' . $class, $selector ) . '{' . $declaration . '}'; }
		$css .= $block && 'desktop' !== $device ? '@media(max-width:' . $breakpoints[ $device ] . 'px){' . $block . '}' : $block;
	}
	$html = preg_replace( '/class="/', 'class="' . $class . ' ', $html, 1 );
	if ( null !== $styles ) { $styles[ $class ] = '<style data-rdct-responsive>' . $css . '</style>'; return $html; }
	return '<style data-rdct-responsive>' . $css . '</style>' . $html;
}
