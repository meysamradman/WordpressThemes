<?php

namespace RadmanCT\Builder;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/header-colors.php';

use RadmanCT\Navigation\Component as Menus;

function types(): array {
	return array( 'container', 'logo', 'image', 'menu', 'panel', 'theme-toggle', 'text', 'button', 'block', 'search', 'copyright', 'icon', 'contact', 'social' );
}

function validate( $value ): ?array {
	if ( ! is_array( $value ) || 1 !== ( $value['version'] ?? null ) || ! is_bool( $value['enabled'] ?? null ) || ! is_array( $value['rows'] ?? null ) || array_values( $value['rows'] ) !== $value['rows'] || count( $value['rows'] ) > 8 ) {
		return null;
	}
	$sticky = $value['sticky'] ?? 'off';
	$sticky_start = $value['stickyStart'] ?? 'immediate';
	$transparent = $value['transparent'] ?? false;
	$transparent_devices = $value['transparentDevices'] ?? 'all';
	if ( ! in_array( $transparent_devices, array( 'all', 'desktop' ), true ) ) { return null; }
	if ( ! in_array( $sticky, array( 'off', 'desktop', 'all' ), true ) || ! in_array( $sticky_start, array( 'immediate', 'after-first-section' ), true ) || ! is_bool( $transparent ) ) {
		return null;
	}
	$width = $value['width'] ?? 'full';
	$dimensions = Layout\validate( $value );
	if ( ! in_array( $width, array( 'full', 'contained', 'custom' ), true ) || null === $dimensions ) {
		return null;
	}
	$result = array( 'version' => 1, 'enabled' => $value['enabled'], 'sticky' => $sticky, 'stickyStart' => $sticky_start, 'transparent' => $transparent, 'width' => $width, 'rows' => array() ) + $dimensions;
	$colors = HeaderColors\validate( $value['headerColors'] ?? array() );
	if ( null === $colors ) { return null; }
	$result['headerColors'] = $colors;
	$result['transparentDevices'] = $transparent_devices;
	$ids = array();
	foreach ( $value['rows'] as $row ) {
		if ( ! is_array( $row ) || ! valid_id( $row['id'] ?? null, $ids ) || ! is_array( $row['items'] ?? null ) || array_values( $row['items'] ) !== $row['items'] || count( $row['items'] ) > 6 ) {
			return null;
		}
		$items = validate_items( $row['items'], $ids );
		if ( null === $items ) { return null; }
		$appearance = appearance( $row['appearance'] ?? array(), true );
		if ( null === $appearance ) {
			return null;
		}
		$result['rows'][] = array( 'id' => $row['id'], 'items' => $items, 'appearance' => $appearance );
	}
	if ( $result['enabled'] && ! array_filter( array_column( $result['rows'], 'items' ) ) ) {
		return null;
	}
	return $result;
}

function validate_items( array $input, array &$ids, int $depth = 0 ): ?array {
	$items = array();
	foreach ( $input as $item ) {
		if ( ! is_array( $item ) || ! valid_id( $item['id'] ?? null, $ids ) || ! in_array( $item['type'] ?? null, types(), true ) ) {
			return null;
		}
		$clean = array( 'id' => $item['id'], 'type' => $item['type'] );
		$appearance = appearance( $item['appearance'] ?? array(), false );
		if ( null === $appearance ) {
			return null;
		}
		$clean['appearance'] = $appearance;
		$color_mode = $item['headerColorMode'] ?? 'auto';
		if ( ! in_array( $color_mode, array( 'auto', 'inherit', 'fixed' ), true ) ) { return null; }
		$clean['headerColorMode'] = $color_mode;
		if ( 'container' === $item['type'] ) {
			if ( $depth >= 2 || ! is_array( $item['items'] ?? null ) || array_values( $item['items'] ) !== $item['items'] || count( $item['items'] ) > 6 ) { return null; }
			$layout = appearance( $item['layout'] ?? array(), true );
			$children = validate_items( $item['items'], $ids, $depth + 1 );
			if ( null === $layout || null === $children ) { return null; }
			$items[] = $clean + array( 'layout' => $layout, 'items' => $children );
			continue;
		}
		if ( in_array( $item['type'], array( 'logo', 'image', 'button' ), true ) ) {
			require_once __DIR__ . '/visuals.php';
			$visuals = Visuals\validate( $item );
			if ( null === $visuals ) { return null; }
			$clean += $visuals;
		}
		if ( in_array( $item['type'], array( 'icon', 'contact', 'social' ), true ) ) {
			require_once __DIR__ . '/elements.php';
			$fields = Elements\validate( $item, $ids );
			if ( null === $fields ) { return null; }
			$items[] = $clean + $fields;
			continue;
		}
		if ( 'panel' === $item['type'] ) { require_once dirname( __DIR__ ) . '/navigation/panel.php'; $panel = \RadmanCT\Navigation\Panel\settings( $item ); if ( null === $panel ) { return null; } $clean += $panel; }
		if ( 'theme-toggle' === $item['type'] ) {
			$options = $item['schemeOptions'] ?? array();
			if ( ! is_array( $options ) || count( $options ) > 32 ) { return null; }
			$options = \RadmanCT\ColorScheme\toggle_options( $options );
			if ( null === $options ) { return null; }
			$clean['schemeOptions'] = $options;
		}
		if ( 'menu' === $item['type'] ) {
			$menu_id = $item['menuId'] ?? 0;
			if ( ! is_int( $menu_id ) || $menu_id < 0 ) {
				return null;
			}
			$clean['menuId'] = $menu_id;
			require_once dirname( __DIR__ ) . '/navigation/component.php';
			$menu_settings = Menus\settings( $item );
			if ( null === $menu_settings ) { return null; }
			$clean += $menu_settings;
		}
		if ( 'block' === $item['type'] ) {
			if ( ! is_int( $item['blockId'] ?? null ) || $item['blockId'] < 1 ) {
				return null;
			}
			$clean['blockId'] = $item['blockId'];
		}
		if ( in_array( $item['type'], array( 'text', 'button' ), true ) ) {
			if ( ! is_string( $item['text'] ?? null ) || strlen( $item['text'] ) > 2000 ) {
				return null;
			}
			$clean['text'] = sanitize_textarea_field( $item['text'] );
		}
		if ( 'button' === $item['type'] ) {
			if ( ! is_string( $item['url'] ?? null ) || strlen( $item['url'] ) > 2048 ) {
				return null;
			}
			$url = $item['url'];
			if ( '' !== $url && ! preg_match( '~^(https?://|mailto:|tel:|/(?!/)|#)~i', $url ) ) {
				return null;
			}
			$clean['url'] = esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );
		}
		$items[] = $clean;
	}
	return $items;
}

function valid_id( $id, array &$ids ): bool {
	if ( count( $ids ) >= 96 || ! is_string( $id ) || ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,47}$/', $id ) || isset( $ids[ $id ] ) ) {
		return false;
	}
	$ids[ $id ] = true;
	return true;
}
