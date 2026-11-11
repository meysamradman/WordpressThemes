<?php

namespace RadmanCT\Navigation;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/navigation/mega-content.php';
require_once __DIR__ . '/navigation/hover.php';

const SUBMENU_META = '_rdct_submenu_type';
const SUBMENU_STYLES = array( 'dropdown', 'mega' );

function submenu_type( int $item_id, string $default = 'dropdown' ): string {
	$value = get_post_meta( $item_id, SUBMENU_META, true );
	return in_array( $value, SUBMENU_STYLES, true ) ? $value : $default;
}

function menu_has_mega( int $menu_id ): bool {
	if ( ! $menu_id ) { return false; }
	$items = wp_get_nav_menu_items( $menu_id, array( 'update_post_term_cache' => false ) );
	$parents = array();
	foreach ( is_array( $items ) ? $items : array() as $item ) { if ( $item->menu_item_parent ) { $parents[ (int) $item->menu_item_parent ] = true; } }
	foreach ( is_array( $items ) ? $items : array() as $item ) {
		if ( 0 === (int) $item->menu_item_parent && ( isset( $parents[ (int) $item->ID ] ) || MegaContent\block( (int) $item->ID ) ) && 'mega' === submenu_type( (int) $item->ID ) ) { return true; }
	}
	return false;
}

function menu( array $args ): void {
	$scope = $args['menu_id'] ?? '';
	if ( ! is_string( $scope ) || ! preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]*$/D', $scope ) ) { return; }
	$args['rdct_menu_scope'] = $scope;
	$style = in_array( $args['rdct_submenu_style'] ?? '', SUBMENU_STYLES, true ) ? $args['rdct_submenu_style'] : 'dropdown';
	$mega = false;
	$decorate = static function ( $classes, $item, $menu_args, $depth ) use ( $scope, $style, &$mega ) {
		if ( ( $menu_args->rdct_menu_scope ?? '' ) !== $scope || 0 !== $depth ) { return $classes; }
		$value = submenu_type( (int) $item->ID, $style );
		$classes = array_values( array_diff( $classes, array( 'rdct-submenu--mega', 'rdct-submenu--dropdown' ) ) );
		$classes[] = 'rdct-submenu--' . $value;
		if ( 'mega' === $value && in_array( 'menu-item-has-children', $classes, true ) ) { $mega = true; }
		return $classes;
	};
	$identify = static function ( $id, $item, $menu_args ) use ( $scope ) {
		if ( ( $menu_args->rdct_menu_scope ?? '' ) !== $scope || ! is_string( $id ) || '' === $id ) { return $id; }
		$suffix = isset( $item->ID ) && 'menu-item-' . $item->ID === $id ? 'i' . $item->ID : $id;
		return $scope . '-' . $suffix;
	};
	$title = static function ( $value, $item, $menu_args, $depth ) use ( $scope ) {
		return ( $menu_args->rdct_menu_scope ?? '' ) === $scope && 0 === $depth && 'roll' === ( $menu_args->rdct_menu_hover ?? 'none' ) ? Hover\title( $value ) : $value;
	};
	add_filter( 'nav_menu_item_title', $title, 20, 4 );
	add_filter( 'nav_menu_item_id', $identify, 20, 3 );
	add_filter( 'nav_menu_css_class', $decorate, 20, 4 );
	$mega_filters = MegaContent\scope( $scope, $style );
	foreach ( $mega_filters as $hook => $callback ) { add_filter( $hook, $callback[0], 10, $callback[1] ); }
	try {
		wp_nav_menu( $args );
		if ( $mega ) {
			\RadmanCT\Components\use_component( 'menu-mega' );
			\RadmanCT\Components\print_late_style( 'menu-mega' );
		}
	} finally {
		foreach ( $mega_filters as $hook => $callback ) { remove_filter( $hook, $callback[0], 10 ); }
		remove_filter( 'nav_menu_item_title', $title, 20 );
		remove_filter( 'nav_menu_item_id', $identify, 20 );
		remove_filter( 'nav_menu_css_class', $decorate, 20 );
	}
}
