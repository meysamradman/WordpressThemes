<?php

namespace RadmanCTComponents\Components\Button;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/button/fields.php';
require_once __DIR__ . '/presentation.php';

function settings( array $input ): array {
	foreach ( array( 'font_weight', 'font_style', 'text_transform', 'text_decoration' ) as $key ) {
		if ( ! array_key_exists( $key, $input ) && isset( $input[ 'button_typography_' . $key ] ) ) { $input[ $key ] = $input[ 'button_typography_' . $key ]; }
	}
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = valid_color( $value ) ? $value : ''; }
		elseif ( ! empty( $field['media'] ) ) { $value = is_array( $value ) ? ( $value['id'] ?? 0 ) : $value; $value = is_numeric( $value ) ? max( 0, (int) $value ) : 0; }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (float) $value ) ) : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $value = true === $value || 'yes' === $value; }
		elseif ( 'url' === $key ) { $value = is_string( $value ) ? esc_url_raw( substr( $value, 0, 2048 ), array( 'http', 'https', 'mailto', 'tel' ) ) : ''; }
		else { $value = is_string( $value ) ? sanitize_text_field( substr( $value, 0, 500 ) ) : $field['default']; }
		$result[ $key ] = $value;
	}
	if ( ! isset( $input['duration'] ) || '' === $input['duration'] ) { $result['duration'] = fields()['duration']['effect_defaults'][ $result['text_effect'] ] ?? fields()['duration']['default']; }
	if ( is_array( $input['link'] ?? null ) ) {
		$url = $input['link']['url'] ?? $input['link']['href'] ?? '';
		$result['url'] = is_string( $url ) ? esc_url_raw( substr( $url, 0, 2048 ), array( 'http', 'https', 'mailto', 'tel' ) ) : '';
		$result['new_tab'] = ! empty( $input['link']['is_external'] ) || '_blank' === ( $input['link']['target'] ?? '' ); $result['nofollow'] = ! empty( $input['link']['nofollow'] );
		if ( in_array( $input['link']['tag'] ?? '', array( 'a', 'button' ), true ) ) { $result['tag'] = $input['link']['tag']; }
	}
	if ( 'custom' !== $result['size'] ) {
		$presets = array( 'xs' => array( 12, 12, 6 ), 'sm' => array( 14, 18, 10 ), 'md' => array( 16, 24, 14 ), 'lg' => array( 18, 32, 18 ), 'xl' => array( 20, 40, 22 ) );
		list( $result['font_size'], $result['padding_x'], $result['padding_y'] ) = $presets[ $result['size'] ];
	}
	if ( 'custom' !== $result['icon_box_preset'] ) { $result['icon_box_size'] = array( 'xs' => 24, 'sm' => 32, 'md' => 40, 'lg' => 48, 'xl' => 64 )[ $result['icon_box_preset'] ]; }
	$result['anchor_id'] = preg_match( '/^#?[a-zA-Z][a-zA-Z0-9_:-]{0,99}$/D', $result['anchor_id'] ) ? ltrim( $result['anchor_id'], '#' ) : '';
	return $result;
}

function display_settings( array $input ): array {
	$s = settings( $input );
	if ( '' !== $s['icon'] ) {
		require_once dirname( __DIR__ ) . '/icons.php';
		if ( ! isset( \RadmanCTComponents\Icons\catalog()[ $s['icon'] ] ) ) { $s['icon'] = ''; }
	}
	if ( '' === $s['icon'] ) { $s['icon_effect'] = 'none'; $s['icon_magnetic'] = false; $s['icon_ripple'] = false; $s['icon_separator'] = false; $s['icon_position'] = 'start'; }
	return $s;
}

function assets( array $input ): array {
	$s = display_settings( $input ); $styles = array( 'button', 'button-' . $s['style'] );
	if ( 'custom' === $s['sticky_mode'] ) { $styles[] = 'button-sticky'; }
	if ( 'lightbox' === $s['action'] ) { $styles[] = 'image-lightbox'; $styles[] = 'button-dialog'; }
	if ( 'modal' === $s['action'] ) { $styles[] = 'button-dialog'; }
	if ( 'none' !== $s['text_effect'] ) { $styles[] = 'button-' . ( 'change-y' === $s['text_effect'] ? 'switch' : explode( '-', $s['text_effect'] )[0] ); }
	if ( '' !== $s['icon'] ) {
		$styles[] = 'button-icon';
		if ( 'none' !== $s['icon_effect'] ) { $styles[] = 'button-icon-motion'; }
		if ( $s['icon_ripple'] ) { $styles[] = 'button-ripple'; }
	}
	return $styles;
}

function render( array $input ): string {
	foreach ( array( 'line_height', 'letter_spacing', 'word_spacing' ) as $key ) {
		foreach ( array( 'desktop', 'laptop', 'tablet', 'mobile' ) as $device ) {
			$native = $input[ 'button_typography_' . $key . ( 'desktop' === $device ? '' : '_' . $device ) ] ?? null;
			if ( ! is_array( $native ) || ! is_numeric( $native['size'] ?? null ) ) { continue; }
			if ( ! array_key_exists( $key, $input ) && ! isset( $input[ $key . '_responsive' ][ $device ] ) ) {
				$input[ $key . '_responsive' ][ $device ] = $native['size'];
				$input[ $key . '_unit_responsive' ][ $device ] = $native['unit'] ?? ( 'line_height' === $key ? '' : 'px' );
			}
		}
	}
	$s = display_settings( $input );
	if ( '' === trim( $s['label'] ) ) { return ''; }
	$css = '';
	foreach ( fields() as $key => $field ) {
		if ( ! empty( $field['color'] ) && '' !== $s[ $key ] ) { $css .= '--rdct-button-' . str_replace( '_', '-', $key ) . ':' . $s[ $key ] . ';'; }
		elseif ( isset( $field['min'] ) && in_array( $key, array( 'gap', 'icon_size', 'icon_box_size', 'icon_border_width', 'icon_rotation', 'padding_x', 'padding_y', 'radius', 'border_width', 'font_size', 'custom_width', 'custom_height', 'duration', 'scroll_speed' ), true ) && ( ! in_array( $key, array( 'custom_width', 'custom_height', 'icon_box_size' ), true ) || $s[ $key ] > 0 ) ) { $css .= '--rdct-button-' . str_replace( '_', '-', $key ) . ':' . $s[ $key ] . ( in_array( $key, array( 'duration', 'scroll_speed' ), true ) ? 's' : ( str_contains( $key, 'rotation' ) || str_contains( $key, 'angle' ) ? 'deg' : 'px' ) ) . ';'; }
	}
	$css .= decoration( $s );
	foreach ( array( 'font_weight', 'font_style', 'text_transform', 'text_decoration' ) as $key ) {
		if ( '' !== $s[ $key ] ) { $css .= str_replace( '_', '-', $key ) . ':' . $s[ $key ] . ';'; }
	}
	$motion = ! $s['disabled'] && ( 'link' !== $s['action'] || $s['magnetic'] || $s['icon_magnetic'] || str_starts_with( $s['text_effect'], 'liquid-' ) );
	$html = '<' . $s['tag'] . ' class="rdct-button rdct-button--' . $s['style'] . '" style="' . esc_attr( $css ) . '" data-width="' . esc_attr( $s['width'] ) . '" data-text-effect="' . esc_attr( $s['text_effect'] ) . '"';
	if ( $motion ) { $html .= ' data-rdct-button data-action="' . $s['action'] . '" data-anchor="' . esc_attr( $s['anchor_id'] ) . '" data-action-url="' . esc_attr( $s['url'] ) . '" data-caption="' . esc_attr( $s['image_caption'] ) . '" data-magnetic="' . ( $s['magnetic'] ? 'true' : 'false' ) . '" data-icon-magnetic="' . ( $s['icon_magnetic'] ? 'true' : 'false' ) . '"'; }
	if ( 'a' === $s['tag'] ) {
		if ( ! $s['disabled'] ) { $html .= ' href="' . esc_url( $s['url'] ) . '"'; }
		else { $html .= ' aria-disabled="true" role="link" tabindex="-1"'; }
		if ( $s['new_tab'] && ! $s['disabled'] ) { $html .= ' target="_blank"'; }
		$rel = implode( ' ', array_filter( array( $s['new_tab'] ? 'noopener noreferrer' : '', $s['nofollow'] ? 'nofollow' : '' ) ) );
		if ( $rel ) { $html .= ' rel="' . esc_attr( $rel ) . '"'; }
	} else { $html .= ' type="button"' . ( $s['disabled'] ? ' disabled' : '' ); }
	if ( $motion ) { $html .= ' data-close-label="' . esc_attr__( 'Close', 'radmanct-components' ) . '" data-previous-label="' . esc_attr__( 'Previous', 'radmanct-components' ) . '" data-next-label="' . esc_attr__( 'Next', 'radmanct-components' ) . '"'; }
	if ( in_array( $s['action'], array( 'lightbox', 'modal' ), true ) ) { $html .= ' aria-haspopup="dialog" aria-expanded="false"'; }
	if ( $s['rotation'] ) { $html .= ' data-rotation'; }
	foreach ( array( 'color', 'background', 'border_color', 'hover_color', 'hover_background', 'hover_border_color', 'icon_color', 'icon_background', 'icon_border_color', 'separator_color', 'ripple_color', 'hover_icon_color', 'hover_icon_background', 'hover_icon_border_color', 'hover_separator_color', 'hover_ripple_color' ) as $key ) { if ( 'custom' === $s['sticky_mode'] && $s[ 'sticky_' . $key ] ) { $html .= ' data-sticky-' . str_replace( '_', '-', $key ); } }
	$html .= '>';
	$label = esc_html( $s['label'] );
	if ( str_starts_with( $s['text_effect'], 'liquid-' ) || ( 'none' === $s['text_effect'] && in_array( $s['icon_effect'], array( 'swap', 'reveal' ), true ) ) ) { $label = '<span class="rdct-button__text"><span class="rdct-button__label">' . $label . '</span></span>'; }
	elseif ( 'none' !== $s['text_effect'] ) { $label = '<span class="rdct-button__text"><span class="rdct-button__label">' . $label . '</span><span class="rdct-button__clone" aria-hidden="true">' . esc_html( 'change-y' === $s['text_effect'] && $s['secondary_label'] ? $s['secondary_label'] : $s['label'] ) . '</span></span>'; }
	$icon = '';
	if ( '' !== $s['icon'] ) {
		require_once dirname( __DIR__ ) . '/icons.php';
		$svg = \RadmanCTComponents\Icons\render( $s['icon'], array( 'size' => $s['icon_size'], 'mirror' => true ) );
		if ( $svg ) { $icon = '<span class="rdct-button__icon" aria-hidden="true" data-icon-style="' . $s['icon_style'] . '" data-icon-shape="' . $s['icon_shape'] . '" data-icon-effect="' . $s['icon_effect'] . '"' . ( $s['icon_box_size'] > 0 ? ' data-icon-box' : '' ) . ( $s['icon_ripple'] ? ' data-ripple' : '' ) . ( $s['icon_separator'] ? ' data-separator' : '' ) . '>' . $svg . '</span>'; }
	}
	if ( $icon && 'swap' === $s['icon_effect'] ) { $copy = str_replace( 'class="rdct-button__icon"', 'class="rdct-button__icon rdct-button__icon-copy"', $icon ); }
	$html = str_replace( ' data-width=', ' data-icon-effect="' . $s['icon_effect'] . '" data-icon-position="' . $s['icon_position'] . '" data-width=', $html );
	$html .= in_array( $s['icon_position'], array( 'start', 'top' ), true ) ? $icon . $label : $label . $icon;
	$html .= $copy ?? '';
	$inactive = 'custom' === $s['size'] ? array() : array( 'padding_x', 'padding_y', 'font_size', 'custom_width', 'custom_height' );
	if ( 'custom' !== $s['icon_box_preset'] ) { $inactive[] = 'icon_box_size'; }
	foreach ( $inactive as $key ) {
		foreach ( array_keys( $input ) as $name ) { if ( $name === $key || str_starts_with( $name, $key . '_responsive' ) || str_starts_with( $name, $key . '_unit' ) ) { unset( $input[ $name ] ); } }
	}
	return \RadmanCTComponents\Components\Presentation\render( $html . '</' . $s['tag'] . '>', $input, 'button' );
}

function decoration( array $s ): string {
	$css = '--rdct-button-border-style:' . $s['border_style'] . ';';
	if ( $s['rotation'] ) { $css .= 'rotate:' . $s['rotation'] . 'deg;'; }
	foreach ( array( '', 'hover_' ) as $state ) {
		$type = $s[ $state . 'background_type' ];
		$image = 'none';
		if ( 'gradient' === $type ) { $stops = ( $s[ $state . 'background' ] ?: '#235a50' ) . ' ' . $s[ $state . 'gradient_start' ] . '%,' . ( $s[ $state . 'gradient_color' ] ?: '#143d35' ) . ' ' . $s[ $state . 'gradient_end' ] . '%'; $image = 'radial' === $s[ $state . 'gradient_type' ] ? 'radial-gradient(circle at ' . $s[ $state . 'background_position' ] . ',' . $stops . ')' : 'linear-gradient(' . $s[ $state . 'gradient_angle' ] . 'deg,' . $stops . ')'; }
		elseif ( 'image' === $type && $s[ $state . 'background_image' ] > 0 ) {
			$url = wp_get_attachment_image_url( (int) apply_filters( 'wpml_object_id', (int) $s[ $state . 'background_image' ], 'attachment', true ), 'full' );
			if ( $url ) { $image = 'url("' . str_replace( array( '"', '\\' ), array( '%22', '%5C' ), esc_url_raw( $url ) ) . '")'; }
		}
		if ( 'none' !== $image || 'hover_' !== $state || ( 'color' === $type && $s['hover_background'] ) ) { $css .= '--rdct-button-' . str_replace( '_', '-', $state ) . 'image:' . $image . ';'; }
		foreach ( array( 'position', 'size', 'repeat', 'attachment' ) as $property ) { if ( '' === $state || 'color' !== $type ) { $css .= '--rdct-button-' . str_replace( '_', '-', $state ) . 'bg-' . $property . ':' . $s[ $state . 'background_' . $property ] . ';'; } }
		if ( $s[ $state . 'shadow_blur' ] || $s[ $state . 'shadow_x' ] || $s[ $state . 'shadow_y' ] || $s[ $state . 'shadow_spread' ] ) {
			$css .= '--rdct-button-' . str_replace( '_', '-', $state ) . 'shadow:' . ( $s[ $state . 'shadow_inset' ] ? 'inset ' : '' ) . $s[ $state . 'shadow_x' ] . 'px ' . $s[ $state . 'shadow_y' ] . 'px ' . $s[ $state . 'shadow_blur' ] . 'px ' . $s[ $state . 'shadow_spread' ] . 'px ' . ( $s[ $state . 'shadow_color' ] ?: '#00000033' ) . ';';
		}
	}
	return $css;
}

function valid_color( $value ): bool {
	if ( ! is_string( $value ) ) { return false; }
	if ( '' === $value || preg_match( '/^#(?:[a-f0-9]{3,4}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ) { return true; }
	if ( ! preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*((?:0|1)?(?:\.\d+)?))?\s*\)$/D', $value, $match ) ) { return false; }
	return ( str_starts_with( $value, 'rgba(' ) === isset( $match[4] ) ) && (int) $match[1] <= 255 && (int) $match[2] <= 255 && (int) $match[3] <= 255 && ( ! isset( $match[4] ) || ( '' !== $match[4] && (float) $match[4] >= 0 && (float) $match[4] <= 1 ) );
}
