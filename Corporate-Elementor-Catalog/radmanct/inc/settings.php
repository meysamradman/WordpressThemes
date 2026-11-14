<?php

namespace RadmanCT\Settings;
require_once __DIR__ . '/page-title-settings.php';

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/font-registry.php';
require_once __DIR__ . '/typography-profiles.php';
require_once __DIR__ . '/layout-settings.php';
require_once __DIR__ . '/content-colors-settings.php';
require_once __DIR__ . '/sidebar-settings.php';
require_once __DIR__ . '/custom-code.php';
require_once __DIR__ . '/analytics.php';
require_once __DIR__ . '/color-scheme.php';

function defaults(): array {
	$base = array(
		'persian_font' => 'iransansx', 'latin_font' => 'gt-walsheim', 'brand_color' => '#235a50', 'container_width' => 1200,
		'scroll_reveal' => false, 'smooth_scroll' => false, 'custom_cursor' => false, 'cursor_hide_outer' => false,
		'smooth_duration' => 0.8,
		'custom_scrollbar' => true, 'scrollbar_width' => 8, 'scrollbar_thumb' => '#888888', 'scrollbar_track' => '#f2f2f2',
		'cursor_inner_size' => 7, 'cursor_outer_size' => 35, 'cursor_color' => '#235a50', 'cursor_hover_color' => '#143d35',
		'cursor_blend' => 'normal', 'cursor_image_label' => '', 'cursor_carousel_label' => '',
		'cursor_hover_effect' => 'scale', 'cursor_follow_duration' => .18, 'cursor_hover_duration' => .35, 'cursor_hover_scale' => 1.8,
		'scroll_progress' => false, 'scroll_progress_height' => 3, 'scroll_progress_color' => '#235a50', 'scroll_progress_track' => '#e7ece9',
		'preloader' => false, 'preloader_style' => 'spinner', 'preloader_background' => '#ffffff', 'preloader_color' => '#235a50',
		'preloader_background_secondary' => '',
		'svg_uploads' => false, 'block_google_fonts' => true,
		'font_language' => 'auto', 'mixed_language_fonts' => false,
		'analytics_enabled' => false, 'analytics_measurement_id' => '',
	);
	foreach ( \RadmanCT\Fonts\roles() as $role ) {
		$base[ $role . '_persian_font' ] = '';
		$base[ $role . '_latin_font' ] = '';
		$base[ $role . '_color' ] = '';
		if ( 'link' === $role ) { $base['link_hover_color'] = ''; }
	}
	$base['body_color'] = '';
	foreach ( \RadmanCT\Typography\Profiles\scopes() as $scope ) { $base[ $scope . '_typography' ] = array(); }
	foreach ( array_merge( array( 'body' ), \RadmanCT\Fonts\roles() ) as $role ) {
		foreach ( array( 'persian', 'latin' ) as $group ) { $base[ $role . '_' . $group . '_weight' ] = ''; $base[ $role . '_' . $group . '_line_height' ] = ''; }
		foreach ( array( 'persian', 'latin' ) as $group ) {
			foreach ( array( '', '_tablet', '_mobile' ) as $device ) { $base[ $role . '_' . $group . $device . '_size' ] = ''; }
		}
	}
	$base += \RadmanCT\Layout\defaults();
	$base += \RadmanCT\ContentColors\defaults();
	$base += \RadmanCT\Sidebars\defaults();
	$base += \RadmanCT\CustomCode\defaults();
	$base += \RadmanCT\ColorScheme\defaults();
	$base += \RadmanCT\PageTitle\defaults();
	foreach ( \RadmanCT\ContentColors\scopes() as $rdct_scope ) { $base[ $rdct_scope . '_background' ] = array(); }
	return normalize( apply_filters( 'radmanct/settings/defaults', $base ), $base );
}

function normalize( $input, array $fallback, bool $report = false ): array {
	$input = is_array( $input ) ? $input : array();
	$result = $fallback;
	$invalid = false;
	$color_defaults = \RadmanCT\ContentColors\defaults();
	$sidebar_defaults = \RadmanCT\Sidebars\defaults();
	$code_defaults = \RadmanCT\CustomCode\defaults();
	foreach ( $input as $key => $value ) {
		if ( ! array_key_exists( $key, $fallback ) ) {
			continue;
		}
		$valid = false;
		if ( preg_match( '/^(blog|portfolio|catalog|page_title)_typography$/D', $key ) ) {
			$value = \RadmanCT\Typography\Profiles\normalize( $value, $fallback, $valid );
		} elseif ( array_key_exists( $key, \RadmanCT\PageTitle\defaults() ) ) {
			$value = \RadmanCT\PageTitle\normalize( $value ); $valid = null !== $value;
		} elseif ( preg_match( '/^(blog|portfolio|catalog)_(archive|single)_background$/D', $key ) ) {
			$value = function_exists( '\\RadmanCTComponents\\Background\\normalize' ) ? \RadmanCTComponents\Background\normalize( $value, array( 'color', 'gradient' ) ) : ( array() === $value ? array() : null ); $valid = null !== $value;
		} elseif ( array_key_exists( $key, \RadmanCT\ColorScheme\defaults() ) ) {
			$value = \RadmanCT\ColorScheme\normalize( $key, $value, $valid );
		} elseif ( array_key_exists( $key, $code_defaults ) ) {
			$valid = \RadmanCT\CustomCode\valid( $value ) && ( ! $report || \RadmanCT\CustomCode\can_edit() );
		} elseif ( 'analytics_measurement_id' === $key ) {
			$normalized = \RadmanCT\Analytics\measurement_id( $value );
			$valid = null !== $normalized;
			$value = $valid ? $normalized : $value;
		} elseif ( 'analytics_enabled' === $key ) {
			$valid = in_array( $value, array( true, false, 0, 1, '0', '1' ), true );
			$value = $valid ? in_array( $value, array( true, 1, '1' ), true ) : $value;
		} elseif ( preg_match( '/^(body|headings|h[1-6]|link|span)(?:_hover)?_color$/D', $key ) ) {
			$valid = '' === $value || ( is_string( $value ) && 1 === preg_match( '/^#[a-f0-9]{6}$/iD', $value ) );
			$value = $valid ? strtolower( $value ) : $value;
		} elseif ( 'font_language' === $key ) {
			$valid = in_array( $value, array( 'auto', 'persian', 'latin' ), true );
		} elseif ( 'mixed_language_fonts' === $key ) {
			$valid = in_array( $value, array( true, false, 0, 1, '0', '1' ), true );
			$value = $valid ? in_array( $value, array( true, 1, '1' ), true ) : $value;
		} elseif ( array_key_exists( $key, $sidebar_defaults ) ) {
			$value = \RadmanCT\Sidebars\normalize( $key, $value, $valid );
		} elseif ( array_key_exists( $key, $color_defaults ) ) {
			$valid = \RadmanCT\ContentColors\valid( $value );
			$value = $valid ? strtolower( $value ) : $value;
		} elseif ( array_key_exists( $key, \RadmanCT\Layout\defaults() ) ) {
			$value = \RadmanCT\Layout\normalize( $key, $value, $valid );
		} elseif ( preg_match( '/^(body|headings|h[1-6]|link|span)_(persian|latin)(?:_tablet|_mobile)?_size$/D', $key ) ) {
			$valid = '' === $value || ( ( is_string( $value ) || is_int( $value ) || is_float( $value ) ) && is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 8 && (float) $value <= 200 );
			$value = $valid && '' !== $value ? (string) round( (float) $value, 2 ) : $value;
		} elseif ( preg_match( '/^(body|headings|h[1-6]|link|span)_(persian|latin)_line_height$/D', $key ) ) {
			$valid = '' === $value || ( ( is_string( $value ) || is_int( $value ) || is_float( $value ) ) && is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 0.8 && (float) $value <= 4 );
			$value = $valid && '' !== $value ? (string) round( (float) $value, 2 ) : $value;
		} elseif ( preg_match( '/^(body|headings|h[1-6]|link|span)_(persian|latin)_weight$/D', $key ) ) {
			$valid = is_string( $value ) || is_int( $value );
			$value = $valid ? (string) $value : $value;
			$valid = $valid && ( '' === $value || 1 === preg_match( '/^(?:[1-9]00|1000)$/D', $value ) );
		} elseif ( 'persian_font' === $key ) {
			$valid = \RadmanCT\Fonts\valid( $value, 'persian' );
		} elseif ( 'latin_font' === $key ) {
			$valid = \RadmanCT\Fonts\valid( $value, 'latin' );
		} elseif ( preg_match( '/^(headings|h[1-6]|link|span)_(persian|latin)_font$/D', $key, $match ) ) {
			$valid = '' === $value || \RadmanCT\Fonts\valid( $value, $match[2] );
		} elseif ( in_array( $key, array( 'brand_color', 'scrollbar_thumb', 'scrollbar_track', 'cursor_color', 'cursor_hover_color', 'scroll_progress_color', 'scroll_progress_track', 'preloader_background', 'preloader_background_secondary', 'preloader_color' ), true ) ) {
			$valid = ( 'preloader_background_secondary' === $key && '' === $value ) || ( is_string( $value ) && 1 === preg_match( '/^#[a-f0-9]{6}$/iD', $value ) );
			$value = $valid ? strtolower( $value ) : $value;
		} elseif ( 'smooth_duration' === $key ) {
			$valid = is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= 0.1 && (float) $value <= 3;
			$value = $valid ? round( (float) $value, 2 ) : $value;
		} elseif ( 'container_width' === $key ) {
			$valid = ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && (int) $value >= 960 && (int) $value <= 1600;
			$value = $valid ? (int) $value : $value;
		} elseif ( in_array( $key, array( 'scrollbar_width', 'cursor_inner_size', 'cursor_outer_size', 'scroll_progress_height' ), true ) ) {
			$ranges = array( 'scrollbar_width' => array( 4, 20 ), 'cursor_inner_size' => array( 2, 24 ), 'cursor_outer_size' => array( 16, 120 ), 'scroll_progress_height' => array( 1, 8 ) );
			$range = $ranges[ $key ];
			$valid = ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && (int) $value >= $range[0] && (int) $value <= $range[1];
			$value = $valid ? (int) $value : $value;
		} elseif ( 'cursor_hover_effect' === $key ) {
			$valid = in_array( $value, array( 'none', 'scale', 'fill', 'magnetic' ), true );
		} elseif ( in_array( $key, array( 'cursor_follow_duration', 'cursor_hover_duration', 'cursor_hover_scale' ), true ) ) {
			$range = array( 'cursor_follow_duration' => array( .05, 1 ), 'cursor_hover_duration' => array( .1, 1.2 ), 'cursor_hover_scale' => array( 1, 4 ) )[ $key ];
			$valid = is_numeric( $value ) && is_finite( (float) $value ) && (float) $value >= $range[0] && (float) $value <= $range[1];
			$value = $valid ? round( (float) $value, 2 ) : $value;
		} elseif ( 'cursor_blend' === $key ) {
			$valid = in_array( $value, array( 'normal', 'difference' ), true );
		} elseif ( 'preloader_style' === $key ) {
			$valid = in_array( $value, array( 'curtain', 'fade', 'sliding', 'spinner', 'spinner-classic', 'dissolve', 'image' ), true );
		} elseif ( in_array( $key, array( 'cursor_image_label', 'cursor_carousel_label' ), true ) ) {
			$valid = is_string( $value ) && strlen( $value ) <= 320;
			$value = $valid ? sanitize_text_field( $value ) : $value;
		} elseif ( in_array( $key, array( 'custom_scrollbar', 'scroll_reveal', 'smooth_scroll', 'custom_cursor', 'cursor_hide_outer', 'scroll_progress', 'preloader', 'svg_uploads', 'block_google_fonts' ), true ) ) {
			$valid = in_array( $value, array( true, false, 0, 1, '0', '1' ), true );
			$value = $valid ? in_array( $value, array( true, 1, '1' ), true ) : $value;
		}
		if ( $valid ) {
			$result[ $key ] = $value;
		} else {
			$invalid = true;
		}
	}
	foreach ( array_merge( array( 'body' ), \RadmanCT\Fonts\roles() ) as $role ) {
		foreach ( array( 'persian', 'latin' ) as $group ) {
			$key = $role . '_' . $group . '_weight';
			if ( '' !== $result[ $key ] && ! in_array( $result[ $key ], \RadmanCT\Fonts\weights( \RadmanCT\Fonts\role_font( $result, $role, $group ) ), true ) ) { $result[ $key ] = ''; $invalid = true; }
		}
	}
	if ( $report && $invalid ) {
		add_settings_error( 'radman_settings', 'rdct-invalid-value', __( 'Some values were invalid and kept their previous settings.', 'radmanct' ) );
	}
	return $result;
}

function values(): array {
	return normalize( get_option( 'radman_settings', array() ), defaults() );
}

function sanitize( $input ): array {
	if ( ! is_array( $input ) ) {
		add_settings_error( 'radman_settings', 'rdct-invalid-settings', __( 'Settings were not saved because the submitted data was invalid.', 'radmanct' ) );
		return values();
	}
	return normalize( $input, values(), true );
}

function scrollbar_css( array $settings ): string {
	if ( ! $settings['custom_scrollbar'] ) { return ''; }
	$root = 'html:has(>body.rdct-site)';
	$native = $settings['scrollbar_width'] <= 10 ? 'thin' : 'auto';
	return $root . '{scrollbar-width:' . $native . ';scrollbar-color:' . $settings['scrollbar_thumb'] . ' ' . $settings['scrollbar_track'] . ';}'
		. '@supports selector(::-webkit-scrollbar){' . $root . '{scrollbar-width:auto;scrollbar-color:auto;}'
		. $root . '::-webkit-scrollbar{width:' . $settings['scrollbar_width'] . 'px;height:' . $settings['scrollbar_width'] . 'px;}'
		. $root . '::-webkit-scrollbar-thumb{background:' . $settings['scrollbar_thumb'] . ';border-radius:99px;}'
		. $root . '::-webkit-scrollbar-track{background:' . $settings['scrollbar_track'] . ';}}'
		. '@media(forced-colors:active){' . $root . '{scrollbar-color:auto;scrollbar-width:auto;}' . $root . '::-webkit-scrollbar-thumb,' . $root . '::-webkit-scrollbar-track{background:revert;}}';
}

function tokens(): string {
	$settings = values();
	return ':root{--rdct-accent:' . $settings['brand_color'] . ';--wp--preset--color--brand:' . $settings['brand_color'] . ';--rdct-container:' . $settings['container_width'] . 'px;--rdct-reading:' . $settings['reading_width'] . 'px;' . \RadmanCT\Layout\variables( \RadmanCT\Layout\resolve( $settings ) ) . '}' . scrollbar_css( $settings );
}
