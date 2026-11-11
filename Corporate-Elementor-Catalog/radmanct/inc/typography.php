<?php

namespace RadmanCT\Typography;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/font-registry.php';

function registry(): array {
	return \RadmanCT\Fonts\registry();
}

function selections( ?array $settings = null ): array {
	$global = null === $settings;
	$settings = $settings ?? \RadmanCT\Settings\values();
	$choices = array( 'persian' => $settings['persian_font'], 'latin' => $settings['latin_font'] );
	$choices = $global ? apply_filters( 'radmanct/typography/fonts', $choices ) : $choices;
	$choices = is_array( $choices ) ? $choices : array();
	$result = array( 'body' => array(
		'persian' => \RadmanCT\Fonts\valid( $choices['persian'] ?? null, 'persian' ) ? $choices['persian'] : 'iransansx',
		'latin' => \RadmanCT\Fonts\valid( $choices['latin'] ?? null, 'latin' ) ? $choices['latin'] : 'gt-walsheim',
	) );
	foreach ( \RadmanCT\Fonts\roles() as $role ) {
		foreach ( array( 'persian', 'latin' ) as $group ) {
			$key = $settings[ $role . '_' . $group . '_font' ];
			$result[ $role ][ $group ] = $key ?: $result[ \RadmanCT\Fonts\parent_role( $role ) ][ $group ];
		}
	}
	return $result;
}

function role_weight( array $settings, string $role, string $group, string $font ): string {
	$value = $settings[ $role . '_' . $group . '_weight' ] ?? '';
	if ( '' === $value && 'body' !== $role ) {
		$value = role_weight( $settings, \RadmanCT\Fonts\parent_role( $role ), $group, $font );
	}
	if ( '' === $value ) { return ''; }
	$weights = \RadmanCT\Fonts\weights( $font );
	usort( $weights, static fn( $left, $right ) => abs( (int) $left - (int) $value ) <=> abs( (int) $right - (int) $value ) );
	return $weights[0] ?? '';
}

function role_line_height( array $settings, string $role, string $group ): string {
	$value = $settings[ $role . '_' . $group . '_line_height' ] ?? '';
	return '' !== $value || 'body' === $role ? $value : role_line_height( $settings, \RadmanCT\Fonts\parent_role( $role ), $group );
}

function selected(): array {
	return selections()['body'];
}

function role_size( array $settings, string $role, string $group, string $device = '' ): string {
	$devices = '_mobile' === $device ? array( '_mobile', '_tablet', '' ) : ( '_tablet' === $device ? array( '_tablet', '' ) : array( '' ) );
	foreach ( $devices as $point ) {
		$value = $settings[ $role . '_' . $group . $point . '_size' ] ?? '';
		if ( '' !== $value ) { return $value; }
	}
	return in_array( $role, array( 'body', 'headings', 'link', 'span' ), true ) ? '' : role_size( $settings, 'headings', $group, $device );
}

function target( string $scope, string $suffix = '' ): string {
	return $scope . $suffix . ( '.rdct-site' !== $scope && '' !== $suffix ? ':not(.elementor, .elementor *)' : '' );
}

function size_changed( array $settings, ?array $baseline, string $role, string $group ): bool {
	if ( null === $baseline ) { return true; }
	foreach ( array( '', '_tablet', '_mobile' ) as $device ) {
		$size = role_size( $settings, $role, $group, $device );
		if ( '' !== $size && $size !== role_size( $baseline, $role, $group, $device ) ) { return true; }
	}
	return false;
}

function size_css( array $settings, array $groups, string $scope = '.rdct-site', ?array $baseline = null ): string {
	$css = '';
	foreach ( array( '' => '', '_tablet' => '(max-width:1024px)', '_mobile' => '(max-width:767px)' ) as $device => $query ) {
		$rules = '';
		foreach ( $groups as $group ) {
			$lang = count( $groups ) > 1 ? ( 'persian' === $group ? ':is(:lang(fa),:lang(ar),:lang(ur),:lang(ps))' : ':lang(en)' ) : '';
			foreach ( array( 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $role ) {
				if ( ! size_changed( $settings, $baseline, $role, $group ) ) { continue; }
				$size = role_size( $settings, $role, $group, $device );
				if ( '' === $size || ( '' !== $device && $size === role_size( $settings, $role, $group, '_mobile' === $device ? '_tablet' : '' ) ) ) { continue; }
				$rules .= $scope . $lang . '{--rdct-size-' . $role . ':' . ( (float) $size / 16 ) . 'rem;}';
				$tag = 'body' === $role ? $lang : ' :where(' . $role . $lang . ')';
				$rules .= target( $scope, $tag ) . '{font-size:var(--rdct-size-' . $role . ');}';
			}
		}
		if ( '' !== $rules ) { $css .= $query ? '@media' . $query . '{' . $rules . '}' : $rules; }
	}
	return $css;
}

function used( ?array $selections = null, ?array $settings = null ): array {
	$selections = $selections ?? selections();
	unset( $selections['headings'] );
	$groups = \RadmanCT\Fonts\active_groups( $settings ?? \RadmanCT\Settings\values() );
	return array_values( array_unique( array_merge( ...array_map( static fn( $role ) => array_values( array_intersect_key( $role, array_flip( $groups ) ) ), array_values( $selections ) ) ) ) );
}

function file_url( string $file ): string {
	$path = implode( '/', array_map( 'rawurlencode', explode( '/', $file ) ) );
	return esc_url_raw( get_template_directory_uri() . '/fonts/' . $path );
}

function preloads( array $resources ): array {
	$groups = apply_filters( 'radmanct/typography/preload', array() );
	if ( ! is_array( $groups ) ) {
		return $resources;
	}
	$selected = selected();
	$fonts = registry();
	foreach ( \RadmanCT\Fonts\active_groups( \RadmanCT\Settings\values() ) as $group ) {
		if ( ! in_array( $group, $groups, true ) ) {
			continue;
		}
		$files = $fonts[ $selected[ $group ] ]['files'];
		$primary = $files[0];
		$weight = (int) ( role_weight( \RadmanCT\Settings\values(), 'body', $group, $selected[ $group ] ) ?: 400 );
		foreach ( $files as $file ) {
			$range = array_map( 'intval', explode( ' ', $file[1] ) );
			if ( $weight === $range[0] || ( count( $range ) > 1 && $range[0] <= $weight && $range[1] >= $weight ) ) {
				$primary = $file;
				break;
			}
		}
		$resources[] = array( 'href' => file_url( $primary[0] ), 'as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous' );
	}
	return $resources;
}

function font_faces( array $keys ): string {
	$fonts = registry();
	$css = '';
	foreach ( $keys as $key ) {
		$font = $fonts[ $key ];
		foreach ( $font['files'] as $file ) {
			$src = file_url( $file[0] );
			$css .= '@font-face{font-family:"' . $font['family'] . '";src:url("' . $src . '") format("woff2");font-weight:' . $file[1] . ';font-style:normal;font-display:swap;}';
		}
	}
	return $css;
}

function enqueue_font_faces( array $keys ): array {
	$fonts = registry();
	$handles = array();
	foreach ( array_diff( array_unique( $keys ), used() ) as $key ) {
		if ( ! isset( $fonts[ $key ] ) ) { continue; }
		$handle = 'rdct-font-' . sanitize_key( $key );
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, false, array( 'rdct-theme' ), null );
			wp_add_inline_style( $handle, font_faces( array( $key ) ) );
		}
		wp_enqueue_style( $handle );
		$handles[] = $handle;
	}
	return $handles;
}

function css( ?array $settings = null, string $scope = '.rdct-site', bool $include_faces = true, ?array $baseline = null ): string {
	$fonts = registry();
	$selections = selections( $settings );
	$settings = $settings ?? \RadmanCT\Settings\values();
	$selected = $selections['body'];
	$previous = null === $baseline ? null : selections( $baseline );
	$css = $include_faces ? font_faces( used( $selections, $settings ) ) : '';
	$groups = \RadmanCT\Fonts\active_groups( $settings );
	$primary = \RadmanCT\Fonts\primary_group( $settings );
	$tokens = ''; $base = ''; $overrides = ''; $line_css = '';
	$body_font_changed = false; $body_weight_changed = false;
	foreach ( $groups as $group ) {
		$suffix = 'persian' === $group ? 'fa' : 'latin';
		$body_changed = null === $previous || $selected[ $group ] !== $previous['body'][ $group ];
		$body_font_changed = $body_font_changed || $body_changed;
		if ( $body_changed ) { $tokens .= '--rdct-font-' . $suffix . ':"' . $fonts[ $selected[ $group ] ]['family'] . '";'; }
		$role_tokens = '';
		foreach ( array( 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $role ) {
			$weight = role_weight( $settings, $role, $group, $selections[ $role ][ $group ] );
			$weight_changed = null === $previous || $weight !== role_weight( $baseline, $role, $group, $previous[ $role ][ $group ] );
			if ( $weight_changed ) { $role_tokens .= '--rdct-weight-' . $role . ':' . ( $weight ?: 'initial' ) . ';'; }
			if ( 'body' === $role ) { $body_weight_changed = $body_weight_changed || $weight_changed; }
			$line = role_line_height( $settings, $role, $group );
			$line_changed = null === $baseline || $line !== role_line_height( $baseline, $role, $group );
			if ( $line_changed && ( '.rdct-site' === $scope || '' !== $line ) ) { $role_tokens .= '--rdct-line-height-' . $role . ':' . ( $line ?: 'initial' ) . ';'; }
			if ( '' !== $line && $line_changed ) {
				$lang = count( $groups ) > 1 ? ( 'persian' === $group ? ':is(:lang(fa),:lang(ar),:lang(ur),:lang(ps))' : ':lang(en)' ) : '';
				$tag = ( 'body' === $role ? '' : ' ' . $role ) . $lang;
				$target = target( $scope, $tag );
				$line_css .= $target . '{line-height:var(--rdct-line-height-' . $role . ');}';
			}
		}
		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $role ) {
			if ( null !== $previous && $selections[ $role ][ $group ] === $previous[ $role ][ $group ] ) { continue; }
			$tokens .= '--rdct-font-' . $role . '-' . $suffix . ':"' . $fonts[ $selections[ $role ][ $group ] ]['family'] . '";';
			$role_tokens .= '--rdct-font-' . $role . ':var(--rdct-font-' . $role . '-' . $suffix . '),sans-serif;';
		}
		if ( '.rdct-site' !== $scope && $body_changed ) { $role_tokens .= '--rdct-font-body:var(--rdct-font-' . $suffix . '),sans-serif;'; }
		if ( $group === $primary ) { $base = $role_tokens; }
		if ( count( $groups ) > 1 && '' !== $role_tokens ) {
			$selector = 'persian' === $group ? ':root:lang(fa),:root:lang(ar),[lang|="fa"],[lang|="ar"]' : ':root:lang(en),[lang|="en"]';
			if ( '.rdct-site' !== $scope ) { $selector = 'persian' === $group ? $scope . ':is(:lang(fa),:lang(ar),:lang(ur),:lang(ps)), ' . $scope . ' :is([lang|="fa"],[lang|="ar"],[lang|="ur"],[lang|="ps"])' : $scope . ':lang(en), ' . $scope . ' [lang|="en"]'; }
			$overrides .= $selector . '{' . $role_tokens . '}';
		}
	}
	$suffix = 'persian' === $primary ? 'fa' : 'latin';
	$root = '.rdct-site' === $scope ? ':root' : $scope;
	if ( '' !== $tokens . $base ) { $css .= $root . '{' . $tokens . $base . '}'; }
	if ( '.rdct-site' === $scope ) { $css .= ':root:root{--rdct-font-body:var(--rdct-font-' . $suffix . '),sans-serif;}'; }
	$css .= $overrides . $line_css;
	if ( '.rdct-site' !== $scope && ( $body_font_changed || $body_weight_changed ) ) { $css .= $scope . '{' . ( $body_font_changed ? 'font-family:var(--rdct-font-body);' : '' ) . ( $body_weight_changed ? 'font-weight:var(--rdct-weight-body);' : '' ) . '}'; }
	return $css . size_css( $settings, $groups, $scope, $baseline ) . text_role_css( $settings, $groups, $selections, $scope, $baseline );
}

function text_role_css( array $settings, array $groups, array $selections, string $scope = '.rdct-site', ?array $baseline = null ): string {
	$css = '';
	$previous = null === $baseline ? null : selections( $baseline );
	$targets = array( 'body' => '', 'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6', 'link' => 'a:not([role="button"]):not(.rdct-button)', 'span' => ':is(p,h1,h2,h3,h4,h5,h6) span:not([aria-hidden]):not(.rdct-ico):not(.rdct-icon)' );
	foreach ( $targets as $role => $tag ) {
		$color = $settings[ $role . '_color' ] ?? '';
		if ( '' === $color && preg_match( '/^h[1-6]$/D', $role ) ) { $color = $settings['headings_color']; }
		$before_color = $baseline[ $role . '_color' ] ?? '';
		if ( '' === $before_color && preg_match( '/^h[1-6]$/D', $role ) ) { $before_color = $baseline['headings_color'] ?? ''; }
		$selector = target( $scope, $tag ? ' :where(' . $tag . ')' : '' );
		if ( '' !== $color && ( null === $baseline || $color !== $before_color ) ) { $css .= ( '.rdct-site' === $scope ? ':root' : $scope ) . '{--rdct-text-' . $role . '-color:' . $color . ';}' . $selector . '{color:var(--rdct-text-' . $role . '-color);}'; }
		if ( ! in_array( $role, array( 'link', 'span' ), true ) ) { continue; }
		foreach ( $groups as $group ) {
			$lang = count( $groups ) > 1 ? ( 'persian' === $group ? ':is(:lang(fa),:lang(ar),:lang(ur),:lang(ps))' : ':lang(en)' ) : '';
			$target = $selector . $lang;
			$rules = '';
			$font_key = $role . '_' . $group . '_font';
			$font_changed = null === $baseline || ( ( $settings[ $font_key ] !== $baseline[ $font_key ] || $selections[ $role ][ $group ] !== $previous[ $role ][ $group ] ) && ( $settings[ $font_key ] || $baseline[ $font_key ] ) );
			if ( $font_changed && ( $settings[ $font_key ] || '.rdct-site' !== $scope ) ) {
				$family = '"' . registry()[ $selections[ $role ][ $group ] ]['family'] . '",sans-serif';
				$rules .= 'font-family:' . ( $settings[ $role . '_' . $group . '_font' ] ? $family : 'inherit' ) . ';';
				if ( $settings[ $role . '_' . $group . '_font' ] ) { $css .= $scope . $lang . '{--rdct-font-' . $role . ':' . $family . ';}'; }
			}
			$weight_key = $role . '_' . $group . '_weight';
			$line_key = $role . '_' . $group . '_line_height';
			$weight_changed = null === $baseline || $settings[ $weight_key ] !== $baseline[ $weight_key ] || role_weight( $settings, $role, $group, $selections[ $role ][ $group ] ) !== role_weight( $baseline, $role, $group, $previous[ $role ][ $group ] );
			$line_changed = null === $baseline || $settings[ $line_key ] !== $baseline[ $line_key ];
			if ( $settings[ $weight_key ] && $weight_changed ) { $rules .= 'font-weight:' . role_weight( $settings, $role, $group, $selections[ $role ][ $group ] ) . ';'; }
			if ( null !== $baseline && $baseline[ $weight_key ] && ! $settings[ $weight_key ] ) { $rules .= 'font-weight:inherit;'; }
			if ( $settings[ $line_key ] && $line_changed ) { $rules .= 'line-height:' . $settings[ $line_key ] . ';'; }
			if ( $rules ) { $css .= $target . '{' . $rules . '}'; }
			$weight = $weight_changed ? $settings[ $weight_key ] : '';
			$line = $line_changed ? $settings[ $line_key ] : '';
			if ( $weight || $line ) { $css .= $scope . $lang . '{' . ( $weight ? '--rdct-weight-' . $role . ':' . role_weight( $settings, $role, $group, $selections[ $role ][ $group ] ) . ';' : '' ) . ( $line ? '--rdct-line-height-' . $role . ':' . $line . ';' : '' ) . '}'; }
			foreach ( array( '' => '', '_tablet' => '(max-width:1024px)', '_mobile' => '(max-width:767px)' ) as $device => $query ) {
				if ( ! size_changed( $settings, $baseline, $role, $group ) ) { continue; }
				$size = role_size( $settings, $role, $group, $device );
				if ( '' === $size ) { continue; }
				$rule = $scope . $lang . '{--rdct-size-' . $role . ':' . ( (float) $size / 16 ) . 'rem;}' . $target . '{font-size:' . ( (float) $size / 16 ) . 'rem;}';
				$css .= $query ? '@media' . $query . '{' . $rule . '}' : $rule;
			}
		}
	}
	if ( $settings['link_hover_color'] && ( null === $baseline || $settings['link_hover_color'] !== $baseline['link_hover_color'] ) ) { $css .= target( $scope, ' :where(a:not([role="button"]):not(.rdct-button)):is(:hover,:focus-visible)' ) . '{color:' . $settings['link_hover_color'] . ';}'; }
	return $css;
}

function enqueue(): void {
	wp_add_inline_style( 'rdct-theme', css() . \RadmanCT\Settings\tokens() );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 15 );
add_filter( 'wp_preload_resources', __NAMESPACE__ . '\\preloads' );
