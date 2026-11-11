<?php

namespace RadmanCT\Builder;

defined( 'ABSPATH' ) || exit;

use RadmanCT\Navigation\Component as Menus;

function render( string $kind ): bool {
	if ( ! active( $kind ) ) {
		return false;
	}
	$id = selection( $kind );
	if ( 'none' === $id ) {
		return true;
	}
	$data = read( $kind, $id );
	if ( ! $data['layout']['enabled'] ) {
		return false;
	}
	render_layout( $kind, $data['layout'], $id );
	return true;
}

function render_layout( string $kind, array $layout, string $layout_id = 'default', bool $preview = false ): void {
	$tag = 'header' === $kind ? 'header' : 'footer';
	$sticky = 'header' === $kind && ! $preview ? ( $layout['sticky'] ?? 'off' ) : 'off';
	$transparent = 'header' === $kind && ! $preview && ! empty( $layout['transparent'] );
	$class = in_array( $sticky, array( 'desktop', 'all' ), true ) ? ' rdct-bld--sticky-' . $sticky : '';
	$class .= $transparent ? ' rdct-bld--transparent' : '';
	$class .= 'full' === ( $layout['width'] ?? 'full' ) ? ' rdct-bld--full-width' : '';
	$start = 'off' !== $sticky && 'after-first-section' === ( $layout['stickyStart'] ?? 'immediate' ) ? ' data-rdct-sticky-start="after-first-section" data-sticky-mode="' . esc_attr( $sticky ) . '"' : '';
	echo '<' . $tag . ' class="rdct-bld rdct-bld--' . esc_attr( $kind ) . esc_attr( $class ) . '"' . Layout\attributes( $layout, 'header' === $kind ? HeaderColors\style( $layout ) : '' ) . ( 'header' === $kind && ! $preview && ! empty( $layout['headerColors']['enabled'] ) ? ' data-rdct-header-colors' : '' ) . $start . ( $transparent ? ' data-rdct-transparent-header data-rdct-transparent-devices="' . esc_attr( $layout['transparentDevices'] ?? 'all' ) . '"' . ( '' === $start ? ' data-sticky-mode="' . esc_attr( $sticky ) . '"' : '' ) : '' ) . '>';
	require_once __DIR__ . '/rendering.php';
	foreach ( $layout['rows'] as $row ) { Rendering\row( $kind, $row, $layout_id, $preview ); }
	echo '</' . $tag . '>';
}

function menu( string $kind, string $id, int $menu_id = 0, array $options = array() ): void {
	require_once dirname( __DIR__ ) . '/navigation/component.php';
	Menus\render( $kind, $id, $menu_id, $options );
}

function enqueue(): void {
	$styled = false;
	foreach ( array( 'header', 'footer' ) as $kind ) {
		if ( ! active( $kind ) ) {
			continue;
		}
		$id = selection( $kind );
		if ( 'none' === $id ) {
			continue;
		}
		$layout = read( $kind, $id )['layout'];
		if ( ! $layout['enabled'] ) {
			continue;
		}
		if ( ! $styled ) {
			$styled = \RadmanCT\Assets\style( 'rdct-builder', 'src/css/builder.css', 'parent', array( 'rdct-theme' ) );
		}
		if ( 'header' === $kind && 'off' !== ( $layout['sticky'] ?? 'off' ) ) {
			\RadmanCT\Assets\style( 'rdct-sticky-header', 'src/css/components/sticky-header.css', 'parent', array( 'rdct-builder' ) );
		}
		if ( 'header' === $kind && ! empty( $layout['transparent'] ) ) {
			$media = 'desktop' === ( $layout['transparentDevices'] ?? 'all' ) ? 'screen and (min-width:48rem)' : 'all';
			\RadmanCT\Assets\style( 'rdct-transparent-header', 'src/css/components/transparent-header.css', 'parent', array( 'rdct-builder' ), $media );
		}
		if ( 'header' === $kind && ! empty( $layout['headerColors']['enabled'] ) ) {
			\RadmanCT\Assets\style( 'rdct-header-contrast', 'src/css/components/header-contrast.css', 'parent', array( 'rdct-builder' ) );
		}
		foreach ( Tree\items( $layout['rows'] ) as $item ) {
			if ( 'search' === $item['type'] && function_exists( '\\RadmanCTComponents\\Components\\enqueue' ) ) { \RadmanCTComponents\Components\enqueue( 'archive-search' ); }
			if ( 'theme-toggle' === $item['type'] && \RadmanCT\ColorScheme\enabled() ) { \RadmanCT\ColorScheme\toggle_assets(); }
			if ( 'button' === $item['type'] ) { require_once __DIR__ . '/button-component.php'; ButtonComponent\assets( $item ); }
			if ( 'panel' === $item['type'] ) { require_once dirname( __DIR__ ) . '/navigation/panel.php'; \RadmanCT\Navigation\Panel\assets( $item ); }
			if ( 'menu' === $item['type'] ) {
				require_once dirname( __DIR__ ) . '/navigation/component.php';
				Menus\assets( $item, $kind );
			}
			if ( in_array( $item['type'], array( 'icon', 'contact', 'social' ), true ) ) {
				require_once __DIR__ . '/elements.php';
				Elements\assets( $item );
			}
			if ( 'block' === $item['type'] ) {
				content( $kind . '-' . $id, $item );
			}
		}
	}
}

function sticky_header(): bool {
	if ( ! active( 'header' ) ) {
		return false;
	}
	$id = selection( 'header' );
	if ( 'none' === $id ) {
		return false;
	}
	$layout = read( 'header', $id )['layout'];
	return $layout['enabled'] && 'off' !== ( $layout['sticky'] ?? 'off' );
}

function content( string $kind, array $item ): string {
	static $fragments = array();
	$key = $kind . '-' . $item['id'];
	if ( ! array_key_exists( $key, $fragments ) ) {
		require_once dirname( __DIR__ ) . '/content-blocks.php';
		$fragments[ $key ] = \RadmanCT\ContentBlocks\render( $item['blockId'], 'rdct-block-' . $key . '-' );
	}
	return $fragments[ $key ];
}
