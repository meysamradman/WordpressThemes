<?php

namespace RadmanCTComponents\Components\TextReveal;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/text/shadows.php';

function segment_groups(): array {
	return array( 'content' => __( 'Content', 'radmanct-components' ), 'text' => __( 'Text', 'radmanct-components' ), 'shadow' => __( 'Text shadow', 'radmanct-components' ), 'image' => __( 'Image', 'radmanct-components' ) );
}

function segment_color( $value, string $fallback = '' ): string {
	if ( ! is_string( $value ) || strlen( $value ) > 100 ) { return $fallback; }
	$value = trim( $value );
	if ( preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{4}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ) { return $value; }
	if ( 'transparent' === strtolower( $value ) ) { return '#00000000'; }
	if ( preg_match( '/^(rgb|rgba)\(\s*(\d{1,3}(?:\.\d+)?)\s*,\s*(\d{1,3}(?:\.\d+)?)\s*,\s*(\d{1,3}(?:\.\d+)?)(?:\s*,\s*(0(?:\.\d+)?|1(?:\.0+)?|\.\d+))?\s*\)$/iD', $value, $parts ) && max( (float) $parts[2], (float) $parts[3], (float) $parts[4] ) <= 255 ) {
		$alpha = $parts[5] ?? '1';
		return 'rgba(' . $parts[2] . ',' . $parts[3] . ',' . $parts[4] . ',' . $alpha . ')';
	}
	return $fallback;
}

function segment_fields(): array {
	$fields = array(
		'title' => array( 'label' => __( 'Text segment', 'radmanct-components' ), 'default' => '' ),
		'image' => array( 'label' => __( 'Inline image', 'radmanct-components' ), 'default' => '', 'image' => true ),
		'image_position' => array( 'label' => __( 'Image placement', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Before text', 'radmanct-components' ), 'end' => __( 'After text', 'radmanct-components' ) ) ),
		'vertical_align' => array( 'label' => __( 'Vertical alignment', 'radmanct-components' ), 'default' => 'middle', 'options' => array( 'baseline' => __( 'Baseline', 'radmanct-components' ), 'middle' => __( 'Middle', 'radmanct-components' ), 'text-top' => __( 'Text top', 'radmanct-components' ), 'text-bottom' => __( 'Text bottom', 'radmanct-components' ) ) ),
		'color' => array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'shadow_mode' => array( 'label' => __( 'Text shadow', 'radmanct-components' ), 'default' => 'inherit', 'options' => array( 'inherit' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'custom' => __( 'Custom', 'radmanct-components' ) ) ),
		'paint' => array( 'label' => __( 'Text color type', 'radmanct-components' ), 'default' => 'solid', 'options' => array( 'solid' => __( 'Solid', 'radmanct-components' ), 'gradient' => __( 'Gradient', 'radmanct-components' ), 'image' => __( 'Image fill', 'radmanct-components' ) ) ),
		'mask_image' => array( 'label' => __( 'Text fill image', 'radmanct-components' ), 'default' => '', 'image' => true, 'condition' => array( 'paint' => 'image' ) ),
		'mask_resolution' => array( 'label' => __( 'Fill image resolution', 'radmanct-components' ), 'default' => 'large', 'options' => segment_image_sizes(), 'condition' => array( 'paint' => 'image' ) ),
		'mask_size' => array( 'label' => __( 'Fill image size', 'radmanct-components' ), 'default' => 'cover', 'options' => array( 'cover' => __( 'Cover', 'radmanct-components' ), 'contain' => __( 'Contain', 'radmanct-components' ), 'auto' => __( 'Original size', 'radmanct-components' ) ), 'condition' => array( 'paint' => 'image' ) ),
		'mask_position' => array( 'label' => __( 'Fill image position', 'radmanct-components' ), 'default' => 'center', 'options' => array( 'center' => __( 'Center', 'radmanct-components' ), 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ), 'top' => __( 'Top', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ) ), 'condition' => array( 'paint' => 'image' ) ),
		'mask_repeat' => array( 'label' => __( 'Repeat fill image', 'radmanct-components' ), 'default' => 'no-repeat', 'options' => array( 'no-repeat' => __( 'No repeat', 'radmanct-components' ), 'repeat' => __( 'Repeat', 'radmanct-components' ), 'repeat-x' => __( 'Repeat horizontally', 'radmanct-components' ), 'repeat-y' => __( 'Repeat vertically', 'radmanct-components' ) ), 'condition' => array( 'paint' => 'image' ) ),
		'gradient_type' => array( 'label' => __( 'Gradient type', 'radmanct-components' ), 'default' => 'linear', 'options' => array( 'linear' => __( 'Linear', 'radmanct-components' ), 'radial' => __( 'Radial', 'radmanct-components' ) ), 'condition' => array( 'paint' => 'gradient' ) ),
		'gradient_start' => array( 'label' => __( 'Gradient start color', 'radmanct-components' ), 'default' => '#172b3a', 'color' => true, 'condition' => array( 'paint' => 'gradient' ) ),
		'gradient_start_position' => array( 'label' => __( 'Start color position (%)', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 100, 'step' => 0.1, 'float' => true, 'condition' => array( 'paint' => 'gradient' ) ),
		'gradient_end' => array( 'label' => __( 'Gradient end color', 'radmanct-components' ), 'default' => '#5667df', 'color' => true, 'condition' => array( 'paint' => 'gradient' ) ),
		'gradient_end_position' => array( 'label' => __( 'End color position (%)', 'radmanct-components' ), 'default' => 100, 'min' => 0, 'max' => 100, 'step' => 0.1, 'float' => true, 'condition' => array( 'paint' => 'gradient' ) ),
		'gradient_angle' => array( 'label' => __( 'Gradient angle', 'radmanct-components' ), 'default' => 90, 'min' => 0, 'max' => 360, 'step' => 0.1, 'float' => true, 'condition' => array( 'paint' => 'gradient', 'gradient_type' => 'linear' ) ),
		'image_border_width' => array( 'label' => __( 'Image border width', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 20 ),
		'image_border_color' => array( 'label' => __( 'Image border color', 'radmanct-components' ), 'default' => '#e6ecef', 'color' => true ),
		'font_family' => array( 'label' => __( 'Text font family', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ) ) + array_combine( array_keys( fonts() ), array_keys( fonts() ) ) ),
		'font_scale' => array( 'label' => __( 'Relative text size (%)', 'radmanct-components' ), 'default' => 100, 'min' => 25, 'max' => 400 ),
		'font_weight' => array( 'label' => __( 'Font weight', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), '100' => '100', '200' => '200', '300' => '300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900' ) ),
		'font_style' => array( 'label' => __( 'Font style', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'normal' => __( 'Normal', 'radmanct-components' ), 'italic' => __( 'Italic', 'radmanct-components' ), 'oblique' => __( 'Oblique', 'radmanct-components' ) ) ),
		'text_transform' => array( 'label' => __( 'Text transform', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'uppercase' => __( 'Uppercase', 'radmanct-components' ), 'lowercase' => __( 'Lowercase', 'radmanct-components' ), 'capitalize' => __( 'Capitalize', 'radmanct-components' ) ) ),
		'text_decoration' => array( 'label' => __( 'Text decoration', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'underline' => __( 'Underline', 'radmanct-components' ), 'overline' => __( 'Overline', 'radmanct-components' ), 'line-through' => __( 'Line through', 'radmanct-components' ) ) ),
	);
	$fields = array_replace( $fields, segment_shadow_metadata() );
	$fields['mask_size']['options']['custom'] = __( 'Custom', 'radmanct-components' );
	$fields['mask_position']['options']['custom'] = __( 'Custom', 'radmanct-components' );
	$fields['mask_attachment'] = array( 'label' => __( 'Fill image attachment', 'radmanct-components' ), 'default' => 'scroll', 'options' => array( 'scroll' => __( 'Scroll', 'radmanct-components' ), 'fixed' => __( 'Fixed', 'radmanct-components' ) ), 'condition' => array( 'paint' => 'image' ) );
	foreach ( array( '', '_tablet', '_mobile' ) as $suffix ) {
		$fields[ 'image_border_unit' . $suffix ] = array( 'label' => __( 'Image border unit', 'radmanct-components' ), 'default' => '' === $suffix ? 'px' : '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'px' => 'px', 'em' => 'em', 'rem' => 'rem' ), 'responsive' => true, 'device' => $suffix, 'group' => 'image', 'tablet_default' => '', 'mobile_default' => '' );
	}
	$size_labels = array(
		'line_height' => array( __( 'Line height', 'radmanct-components' ), __( 'Line height (tablet)', 'radmanct-components' ), __( 'Line height (mobile)', 'radmanct-components' ) ),
		'letter_spacing' => array( __( 'Letter spacing', 'radmanct-components' ), __( 'Letter spacing (tablet)', 'radmanct-components' ), __( 'Letter spacing (mobile)', 'radmanct-components' ) ),
		'image_width' => array( __( 'Image width', 'radmanct-components' ), __( 'Image width (tablet)', 'radmanct-components' ), __( 'Image width (mobile)', 'radmanct-components' ) ),
		'image_radius' => array( __( 'Image corner radius', 'radmanct-components' ), __( 'Image corner radius (tablet)', 'radmanct-components' ), __( 'Image corner radius (mobile)', 'radmanct-components' ) ),
	);
	foreach ( \RadmanCTComponents\Components\Presentation\controls( 'text-segment-image' ) as $key => $spec ) {
		$labels = $size_labels[ $key ] ?? array( $spec['label'], sprintf( __( '%s (tablet)', 'radmanct-components' ), $spec['label'] ), sprintf( __( '%s (mobile)', 'radmanct-components' ), $spec['label'] ) );
		foreach ( array( '', '_tablet', '_mobile' ) as $index => $suffix ) {
			if ( ! isset( $spec['units'] ) && ! isset( $spec['options'] ) ) {
				$fields[ $key . $suffix ] = array( 'label' => $labels[ $index ], 'default' => '' === $suffix ? $spec['default'] : -1, 'min' => -1, 'max' => $spec['max'], 'step' => 0.1, 'float' => true, 'responsive' => true, 'device' => $suffix, 'group' => 'image', 'tablet_default' => -1, 'mobile_default' => -1, 'description' => __( 'Use -1 to inherit; zero removes the border.', 'radmanct-components' ) );
				continue;
			}
			if ( isset( $spec['options'] ) ) {
				$fields[ $key . $suffix ] = array( 'label' => $labels[ $index ], 'default' => '' === $suffix ? ( $spec['default'] ?? '' ) : '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ) ) + $spec['options'], 'responsive' => true, 'device' => $suffix, 'group' => $spec['group'] ?? 'image', 'condition' => $spec['condition'] ?? array() );
				continue;
			}
			$fields[ $key . $suffix ] = array( 'label' => $labels[ $index ], 'default' => '', 'size' => true, 'device' => $suffix, 'units' => $spec['units'], 'default_unit' => $spec['default_unit'] ?? 'px', 'min' => $spec['min'], 'max' => $spec['max'], 'range' => $spec['range'] ?? array(), 'group' => $spec['group'] ?? 'image', 'condition' => $spec['condition'] ?? array() );
		}
	}
	foreach ( $fields as $key => &$field ) {
		$field['group'] ??= in_array( $key, array( 'title', 'image', 'image_position', 'vertical_align' ), true ) ? 'content' : ( str_starts_with( $key, 'image_border_' ) || ! empty( $field['size'] ) ? 'image' : 'text' );
	}
	unset( $field );
	$fields['color']['condition'] = array( 'paint' => 'solid' );
	return $fields;
}

function segment_image_sizes(): array {
	static $sizes = null;
	if ( null !== $sizes ) { return $sizes; }
	$sizes = array( 'large' => 'large' );
	foreach ( get_intermediate_image_sizes() as $size ) {
		if ( is_string( $size ) && preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', $size ) ) { $sizes[ $size ] = $size; }
	}
	$sizes['full'] = __( 'Full size', 'radmanct-components' );
	return $sizes;
}

function segment_typography( array $row ): string {
	static $fields = null;
	$fields ??= segment_fields();
	$style = '';
	foreach ( array( 'font_style', 'text_transform', 'text_decoration' ) as $key ) {
		$value = $row[ $key ] ?? '';
		if ( is_string( $value ) && '' !== $value && isset( $fields[ $key ]['options'][ $value ] ) ) { $style .= str_replace( '_', '-', $key ) . ':' . $value . ';'; }
	}
	return $style;
}

function segment_image_id( array $image ): int {
	$id = $image['id'] ?? 0;
	$id = is_numeric( $id ) && ctype_digit( (string) $id ) && (float) $id <= PHP_INT_MAX ? (int) $id : 0;
	return $id > 0 ? (int) apply_filters( 'wpml_object_id', $id, 'attachment', true ) : 0;
}

function segment_mask( array $row ): array {
	if ( 'image' !== ( $row['paint'] ?? '' ) ) { return array( '', '' ); }
	$id = segment_image_id( is_array( $row['mask_image'] ?? null ) ? $row['mask_image'] : array() );
	$sizes = segment_image_sizes();
	$size = is_string( $row['mask_resolution'] ?? null ) && isset( $sizes[ $row['mask_resolution'] ] ) ? $row['mask_resolution'] : 'large';
	$url = $id > 0 && wp_attachment_is_image( $id ) ? wp_get_attachment_image_url( $id, $size ) : false;
	$url = is_string( $url ) ? esc_url_raw( $url, array( 'http', 'https' ) ) : '';
	if ( '' === $url || preg_match( '/[\x00-\x20\x7f]/', $url ) ) { return array( '', '' ); }
	$style = '--rdct-segment-mask-image:url("' . addcslashes( $url, '"\\' ) . '");';
	$fields = segment_fields();
	foreach ( array( 'mask_size', 'mask_repeat', 'mask_attachment' ) as $key ) {
		$value = is_string( $row[ $key ] ?? null ) && isset( $fields[ $key ]['options'][ $row[ $key ] ] ) ? $row[ $key ] : $fields[ $key ]['default'];
		$style .= '--rdct-segment-' . str_replace( '_', '-', $key ) . ':' . ( 'custom' === $value ? 'auto' : $value ) . ';';
	}
	$position = is_string( $row['mask_position'] ?? null ) && isset( $fields['mask_position']['options'][ $row['mask_position'] ] ) ? $row['mask_position'] : 'center';
	return array( $style, $position );
}

function segments( $rows ): string {
	if ( ! is_array( $rows ) ) { return ''; }
	$html = ''; $styles = array();
	$fonts = fonts();
	$presentation_fields = \RadmanCTComponents\Components\Presentation\controls( 'text-segment-image' );
	foreach ( array_slice( $rows, 0, 30 ) as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		$text = is_string( $row['title'] ?? null ) && strlen( $row['title'] ) <= 2000 ? $row['title'] : '';
		$image = is_array( $row['image'] ?? null ) ? $row['image'] : array();
		$id = segment_image_id( $image );
		$size = 'medium';
		if ( is_numeric( $image['width'] ?? null ) && is_numeric( $image['height'] ?? null ) && $image['width'] > 0 && $image['height'] > 0 ) { $size = array( (int) number( $image['width'], 300, 1, 8192 ), (int) number( $image['height'], 300, 1, 8192 ) ); }
		$media = $id > 0 && wp_attachment_is_image( $id ) ? wp_get_attachment_image( $id, $size, false, array( 'class' => 'rdct-text__image', 'decoding' => 'async' ) ) : '';
		if ( '' === $text && '' === $media ) { continue; }
		$style = '--rdct-segment-scale:' . number( $row['font_scale'] ?? null, 100, 25, 400 ) . '%;';
		$color = segment_color( $row['color'] ?? null );
		if ( '' !== $color ) { $style .= 'color:' . $color . ';-webkit-text-fill-color:currentColor;'; }
		$family = $row['font_family'] ?? '';
		if ( is_string( $family ) && isset( $fonts[ $family ] ) ) { $style .= '--rdct-text-font:"' . $fonts[ $family ] . '",sans-serif;'; }
		if ( in_array( $row['font_weight'] ?? '', array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ), true ) ) { $style .= 'font-weight:' . $row['font_weight'] . ';'; }
		$gradient = 'gradient' === ( $row['paint'] ?? '' );
		$radial = $gradient && 'radial' === ( $row['gradient_type'] ?? '' );
		if ( $gradient ) {
			foreach ( array( 'gradient_start' => '#172b3a', 'gradient_end' => '#5667df' ) as $key => $fallback ) {
				$color = segment_color( $row[ $key ] ?? null, $fallback );
				$style .= '--rdct-segment-' . str_replace( '_', '-', $key ) . ':' . $color . ';';
			}
			$style .= '--rdct-segment-gradient-angle:' . number( $row['gradient_angle'] ?? null, 90, 0, 360 ) . 'deg;';
			foreach ( array( 'start' => 0, 'end' => 100 ) as $stop => $fallback ) {
				$key = 'gradient_' . $stop . '_position';
				if ( array_key_exists( $key, $row ) ) { $style .= '--rdct-segment-gradient-' . $stop . '-position:' . number( $row[ $key ], $fallback, 0, 100 ) . '%;'; }
			}
		}
		list( $mask_style, $mask_position ) = '' !== $text ? segment_mask( $row ) : array( '', '' );
		$style .= $mask_style;
		$border = number( $row['image_border_width'] ?? null, 0, 0, 20 );
		$edges = '';
		foreach ( array( 'top', 'bottom', 'start', 'end' ) as $edge ) {
			$value = number( $row[ 'image_border_' . $edge ] ?? null, -1, -1, 20 );
			if ( $value >= 0 ) { $edges .= '--rdct-text-image-border-' . $edge . ':' . $value . 'px;'; }
		}
		$responsive_border = false;
		foreach ( $presentation_fields as $key => $spec ) {
			if ( ! str_starts_with( $key, 'image_border_' ) ) { continue; }
			foreach ( array( 'tablet', 'mobile' ) as $device ) {
				if ( null !== \RadmanCTComponents\Components\Presentation\value( $row, $key, $device, $spec ) ) { $responsive_border = true; }
			}
			if ( isset( $row[ $key . '_responsive' ] ) && null !== \RadmanCTComponents\Components\Presentation\value( $row, $key, 'desktop', $spec ) ) { $responsive_border = true; }
		}
		if ( $border > 0 || '' !== $edges || $responsive_border ) {
			$color = segment_color( $row['image_border_color'] ?? null, '#e6ecef' );
			$style .= '--rdct-text-image-border-width:' . $border . 'px;--rdct-text-image-border-color:' . $color . ';';
			$style .= $edges;
		}
		$align = in_array( $row['vertical_align'] ?? '', array( 'baseline', 'middle', 'text-top', 'text-bottom' ), true ) ? $row['vertical_align'] : 'middle';
		$media = $media ? '<span class="rdct-text__figure" style="vertical-align:' . $align . '">' . $media . '</span>' : '';
		$shadow = '' !== $text ? segment_shadow( $row ) . segment_typography( $row ) : '';
		$body = '' === $text ? '' : '<span class="rdct-text__part" data-paint="' . ( $mask_style ? 'image' : ( $gradient ? 'gradient' : 'solid' ) ) . '"' . ( $radial && ! $mask_style ? ' data-gradient-type="radial"' : '' ) . ( $mask_style ? ' data-mask-position="' . $mask_position . '" data-mask-size="' . ( 'custom' === ( $row['mask_size'] ?? '' ) ? 'custom' : 'preset' ) . '"' : '' ) . ( $shadow ? ' style="' . esc_attr( $shadow ) . '"' : '' ) . '>' . highlighted( $text ) . '</span>';
		$segment = '<span class="rdct-text__segment" style="' . esc_attr( $style ) . '">' . ( 'end' === ( $row['image_position'] ?? '' ) ? $body . $media : $media . $body ) . '</span>';
		$presentation = array();
		foreach ( array( '', '_tablet', '_mobile', '_responsive' ) as $suffix ) { if ( array_key_exists( 'image_border_unit' . $suffix, $row ) ) { $presentation[ 'image_border_unit' . $suffix ] = $row[ 'image_border_unit' . $suffix ]; } }
		foreach ( $presentation_fields as $key => $spec ) {
			if ( str_starts_with( $key, 'mask_' ) && ( ! $mask_style || ( in_array( $key, array( 'mask_width', 'mask_height' ), true ) ? 'custom' !== ( $row['mask_size'] ?? '' ) : 'custom' !== $mask_position ) ) ) { continue; }
			if ( 'gradient_position' === $key && ! $radial ) { continue; }
			$is_text = 'text' === ( $spec['group'] ?? 'image' );
			if ( ( $is_text && '' === $text ) || ( ! $is_text && ! $media ) ) { continue; }
			foreach ( array( '', '_tablet', '_mobile', '_responsive' ) as $suffix ) {
				if ( '' === $suffix && str_starts_with( $key, 'image_border_' ) && ! array_key_exists( 'image_border_unit', $row ) ) { continue; }
				if ( array_key_exists( $key . $suffix, $row ) ) { $presentation[ $key . $suffix ] = $row[ $key . $suffix ]; }
			}
		}
		$segment = \RadmanCTComponents\Components\Presentation\render( $segment, $presentation, 'text-segment-image', $styles );
		$html .= '' !== $text ? \RadmanCTComponents\Components\Presentation\render( $segment, $row, 'text-segment-shadow', $styles ) : $segment;
	}
	return implode( '', $styles ) . $html;
}
