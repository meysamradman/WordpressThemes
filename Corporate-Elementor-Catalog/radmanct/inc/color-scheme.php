<?php

namespace RadmanCT\ColorScheme;

defined( 'ABSPATH' ) || exit;

function defaults(): array {
	return array( 'color_scheme' => 'off', 'dark_background' => '#121315', 'dark_surface' => '#1c1d20', 'dark_text' => '#ececef', 'dark_muted' => '#a3a3a8', 'dark_border' => '#343438' );
}

function normalize( string $key, $value, bool &$valid ) {
	$valid = 'color_scheme' === $key ? in_array( $value, array( 'off', 'light', 'dark', 'system' ), true ) : is_string( $value ) && 1 === preg_match( '/^#[a-f0-9]{6}$/iD', $value );
	return $valid && is_string( $value ) ? strtolower( $value ) : $value;
}

function enabled(): bool { return 'off' !== \RadmanCT\Settings\values()['color_scheme']; }

function toggle_fields(): array {
	$fields = array(
		'light_icon' => array( 'label' => __( 'Light mode icon', 'radmanct' ), 'default' => 'sun', 'icon' => true, 'group' => 'content' ),
		'dark_icon' => array( 'label' => __( 'Dark mode icon', 'radmanct' ), 'default' => 'moon', 'icon' => true, 'group' => 'content' ),
		'label' => array( 'label' => __( 'Accessible label', 'radmanct' ), 'default' => __( 'Dark mode', 'radmanct' ), 'group' => 'content' ),
		'show_label' => array( 'label' => __( 'Show label', 'radmanct' ), 'default' => false, 'group' => 'content' ),
		'style' => array( 'label' => __( 'Button appearance', 'radmanct' ), 'default' => 'plain', 'options' => array( 'plain' => __( 'Plain', 'radmanct' ), 'outline' => __( 'Outline', 'radmanct' ), 'filled' => __( 'Filled', 'radmanct' ) ) ),
		'icon_size' => array( 'label' => __( 'Icon size', 'radmanct' ), 'default' => 24, 'min' => 8, 'max' => 64 ),
		'button_size' => array( 'label' => __( 'Button size (0: automatic)', 'radmanct' ), 'default' => 44, 'min' => 0, 'max' => 160 ),
		'padding' => array( 'label' => __( 'Padding', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => 40 ),
		'radius' => array( 'label' => __( 'Border radius', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => 120 ),
		'border_width' => array( 'label' => __( 'Border width', 'radmanct' ), 'default' => 1, 'min' => 0, 'max' => 8, 'condition' => array( 'style' => array( 'outline' ) ) ),
		'animation' => array( 'label' => __( 'Icon transition', 'radmanct' ), 'default' => 'rotate', 'options' => array( 'none' => __( 'None', 'radmanct' ), 'fade' => __( 'Fade', 'radmanct' ), 'rotate' => __( 'Rotate', 'radmanct' ) ) ),
		'duration' => array( 'label' => __( 'Animation duration (seconds)', 'radmanct' ), 'default' => .35, 'min' => 0, 'max' => 1.5, 'step' => .05, 'unit' => 's' ),
	);
	foreach ( array( 'light' => __( 'Light mode', 'radmanct' ), 'dark' => __( 'Dark mode', 'radmanct' ), 'hover' => __( 'Hover and focus', 'radmanct' ) ) as $state => $label ) {
		foreach ( array( 'color' => __( 'Icon and text color', 'radmanct' ), 'background' => __( 'Background', 'radmanct' ), 'border_color' => __( 'Border color', 'radmanct' ) ) as $key => $title ) {
			$fields[ $state . '_' . $key ] = array( 'label' => $label . ' — ' . $title, 'default' => '', 'color' => true );
		}
	}
	return $fields;
}

function toggle_options( array $input ): ?array {
	$result = array();
	foreach ( toggle_fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { if ( ! is_string( $value ) || ! isset( $field['options'][ $value ] ) ) { return null; } }
		elseif ( ! empty( $field['icon'] ) ) { if ( ! is_string( $value ) || ( '' !== $value && ! preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $value ) ) ) { return null; } }
		elseif ( ! empty( $field['color'] ) ) { if ( ! is_string( $value ) || ( '' !== $value && ! preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) ) { return null; } }
		elseif ( isset( $field['min'] ) ) { if ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || $value < $field['min'] || $value > $field['max'] ) { return null; } $value = (float) $value; }
		elseif ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 1, '1', 'yes', 'true' ), true ); }
		else { if ( ! is_string( $value ) || strlen( $value ) > 320 ) { return null; } $value = sanitize_text_field( $value ); }
		$result[ $key ] = $value;
	}
	return $result;
}

function toggle_assets(): void {
	\RadmanCT\Components\use_component( 'theme-toggle' );
	\RadmanCT\Components\print_late_style( 'theme-toggle' );
}

function bootstrap(): void {
	if ( ! enabled() || is_admin() ) { return; }
	$mode = \RadmanCT\Settings\values()['color_scheme'];
	echo '<script id="rdct-color-scheme-init">(()=>{const r=document.documentElement;let s=' . wp_json_encode( $mode ) . ';r.dataset.rdctSchemeMode=s;try{const v=localStorage.getItem("rdct-color-scheme");if(v==="light"||v==="dark")s=v}catch{}if(s==="system")s=matchMedia("(prefers-color-scheme:dark)").matches?"dark":"light";r.dataset.rdctScheme=s})();</script>';
}

function enqueue(): void {
	if ( ! enabled() || is_admin() ) { return; }
	\RadmanCT\Assets\style( 'rdct-color-scheme', 'src/css/color-scheme.css', 'parent', array( 'rdct-theme' ) );
	$s = \RadmanCT\Settings\values();
	$body = $s['body_color'] ?: 'var(--wp--preset--color--ink,#182522)';
	$heading = $s['headings_color'] ?: $body;
	$css = ':root{';
	foreach ( array( 'body', 'headings', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'link' ) as $role ) {
		$value = $s[ $role . '_color' ] ?? '';
		if ( '' === $value ) { $value = 'link' === $role ? 'var(--rdct-accent)' : ( 'headings' === $role || preg_match( '/^h[1-6]$/D', $role ) ? $heading : $body ); }
		$css .= '--rdct-configured-' . $role . '-color:' . $value . ';';
	}
	$css .= '}html[data-rdct-scheme="dark"]{';
	foreach ( array( 'background' => 'paper', 'surface' => 'surface', 'text' => 'ink', 'muted' => 'muted', 'border' => 'line' ) as $key => $token ) { $css .= '--rdct-' . $token . ':' . $s[ 'dark_' . $key ] . ';'; }
	wp_add_inline_style( 'rdct-color-scheme', $css . '}' );
	$entry = \RadmanCT\Assets\entry( 'src/js/color-scheme.js' );
	if ( $entry ) { wp_enqueue_script_module( 'rdct-color-scheme', \RadmanCT\Assets\url( $entry['file'] ), array(), null ); }
}

function toggle( array $options = array(), bool $preview = false ): string {
	if ( ! enabled() && ! $preview ) { return ''; }
	$s = toggle_options( $options );
	if ( null === $s ) { return ''; }
	toggle_assets();
	require_once __DIR__ . '/icons.php';
	\RadmanCT\Components\use_component( 'icons' );
	\RadmanCT\Components\print_late_style( 'icons' );
	$light = \RadmanCT\Icons\render( $s['light_icon'], array( 'size' => $s['icon_size'] ) );
	$dark = \RadmanCT\Icons\render( $s['dark_icon'], array( 'size' => $s['icon_size'] ) );
	$light = $light ?: $dark; $dark = $dark ?: $light;
	$css = '--rdct-toggle-icon-size:' . $s['icon_size'] . 'px;--rdct-toggle-size:' . ( $s['button_size'] > 0 ? $s['button_size'] . 'px' : 'auto' ) . ';--rdct-toggle-padding:' . $s['padding'] . 'px;--rdct-toggle-radius:' . $s['radius'] . 'px;--rdct-toggle-border-width:' . ( 'outline' === $s['style'] ? $s['border_width'] : 0 ) . 'px;--rdct-toggle-duration:' . $s['duration'] . 's;';
	foreach ( array( 'light', 'dark', 'hover' ) as $state ) {
		foreach ( array( 'color', 'background', 'border_color' ) as $key ) { if ( '' !== $s[ $state . '_' . $key ] ) { $css .= '--rdct-toggle-' . $state . '-' . str_replace( '_', '-', $key ) . ':' . $s[ $state . '_' . $key ] . ';'; } }
	}
	$label = $s['label'] ?: __( 'Dark mode', 'radmanct' );
	$html = '<button class="rdct-theme-toggle" type="button" data-rdct-theme-toggle data-appearance="' . esc_attr( $s['style'] ) . '" data-animation="' . esc_attr( $s['animation'] ) . '" aria-pressed="false" aria-label="' . esc_attr( $label ) . '" style="' . esc_attr( $css ) . '">';
	if ( $light ) { $html .= '<span data-scheme-icon="light" aria-hidden="true">' . $light . '</span><span data-scheme-icon="dark" aria-hidden="true">' . $dark . '</span>'; }
	if ( $s['show_label'] || ! $light ) { $html .= '<span class="rdct-theme-toggle__label"' . ( $light ? ' data-with-icon' : '' ) . '>' . esc_html( $label ) . '</span>'; }
	return $html . '</button>';
}

function register(): void {
	add_action( 'wp_head', __NAMESPACE__ . '\\bootstrap', 1 );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 40 );
	add_shortcode( 'rdct_theme_toggle', static fn( $attributes ): string => toggle( is_array( $attributes ) ? $attributes : array() ) );
}
