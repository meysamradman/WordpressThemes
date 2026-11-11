<?php

namespace RadmanCT\Navigation\MegaContent;

defined( 'ABSPATH' ) || exit;

const META = '_rdct_mega_block';

function block( int $item_id ): int { return max( 0, (int) get_post_meta( $item_id, META, true ) ); }

function render( int $item_id, string $prefix ): string {
	$id = block( $item_id );
	if ( ! $id ) { return ''; }
	require_once __DIR__ . '/blocks.php';
	return \RadmanCT\Navigation\Blocks\render( $id, $prefix );
}

function scope( string $id, string $default ): array {
	$panels = array();
	$prepare = static function ( $items, $args ) use ( $id, $default, &$panels ) {
		if ( ( $args->rdct_menu_scope ?? '' ) !== $id ) { return $items; }
		foreach ( $items as $item ) {
			if ( 0 === (int) $item->menu_item_parent && 'mega' === \RadmanCT\Navigation\submenu_type( (int) $item->ID, $default ) ) {
				$html = render( (int) $item->ID, $id . '-block-' . $item->ID . '-' );
				if ( '' !== $html ) { $panels[ (int) $item->ID ] = $html; }
			}
		}
		$parents = array();
		foreach ( $items as $item ) { $parents[ (int) $item->ID ] = (int) $item->menu_item_parent; }
		return array_values( array_filter( $items, static function ( $item ) use ( $parents, $panels ) {
			$parent = (int) $item->menu_item_parent; $seen = array();
			while ( $parent && ! isset( $seen[ $parent ] ) ) { if ( isset( $panels[ $parent ] ) ) { return false; } $seen[ $parent ] = true; $parent = $parents[ $parent ] ?? 0; }
			return true;
		} ) );
	};
	$classes = static function ( $classes, $item, $args, $depth ) use ( $id, &$panels ) {
		if ( ( $args->rdct_menu_scope ?? '' ) === $id && 0 === $depth && isset( $panels[ (int) $item->ID ] ) ) { $classes[] = 'menu-item-has-children'; }
		return array_unique( $classes );
	};
	$content = static function ( $output, $item, $depth, $args ) use ( $id, &$panels ) {
		return ( $args->rdct_menu_scope ?? '' ) === $id && 0 === $depth && isset( $panels[ (int) $item->ID ] ) ? $output . '<div class="sub-menu rdct-mega-content">' . $panels[ (int) $item->ID ] . '</div>' : $output;
	};
	return array( 'wp_nav_menu_objects' => array( $prepare, 2 ), 'nav_menu_css_class' => array( $classes, 4 ), 'walker_nav_menu_start_el' => array( $content, 4 ) );
}
