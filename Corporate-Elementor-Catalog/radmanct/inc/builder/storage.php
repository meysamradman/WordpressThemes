<?php

namespace RadmanCT\Builder\Storage;

defined( 'ABSPATH' ) || exit;

function valid_id( $id ): bool {
	return is_string( $id ) && ( 'default' === $id || preg_match( '/^l[a-f0-9]{32}$/D', $id ) );
}

function key( string $kind, string $id = 'default' ): string {
	if ( ! in_array( $kind, array( 'header', 'footer' ), true ) || ! valid_id( $id ) ) {
		throw new \InvalidArgumentException( 'Invalid builder identifier.' );
	}
	return 'radman_builder_' . $kind . ( 'default' === $id ? '' : '__' . $id );
}

function compare_exchange( string $key, $previous, array $next ): bool {
	if ( null === $previous ) {
		return add_option( $key, $next, '', false );
	}
	global $wpdb;
	$changed = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s", maybe_serialize( $next ), $key, maybe_serialize( $previous ) ) );
	if ( 1 !== $changed ) {
		return false;
	}
	wp_cache_delete( $key, 'options' );
	wp_cache_delete( 'alloptions', 'options' );
	wp_cache_delete( 'notoptions', 'options' );
	return true;
}

function index(): array {
	$value = get_option( 'radman_builder_library', array() );
	return is_array( $value ) ? $value : array();
}

function listing( string $kind ): array {
	$index = index();
	$items = exists( $kind, 'default' ) ? array( array( 'id' => 'default', 'title' => $index[ $kind ]['default'] ?? __( 'Original layout', 'radmanct' ) ) ) : array();
	foreach ( index()[ $kind ] ?? array() as $id => $title ) {
		if ( 'default' !== $id && valid_id( $id ) && is_string( $title ) ) {
			$items[] = array( 'id' => $id, 'title' => $title );
		}
	}
	return $items;
}

function exists( string $kind, string $id ): bool {
	return 'default' === $id ? empty( index()['deleted'][ $kind ] ) : ( valid_id( $id ) && isset( index()[ $kind ][ $id ] ) );
}

function save( string $kind, string $id, array $layout, int $revision ): array {
	$option = key( $kind, $id );
	$old = get_option( $option, null );
	$current = is_array( $old ) && is_int( $old['revision'] ?? null ) ? $old['revision'] : 0;
	if ( ! exists( $kind, $id ) || $current !== $revision || ( 'default' !== $id && null === $old ) ) {
		return array( 'status' => 'conflict' );
	}
	$next = $layout + array( 'revision' => $revision + 1 );
	if ( ! compare_exchange( $option, $old, $next ) ) {
		return array( 'status' => 'conflict' );
	}
	set_theme_mod( 'radman_builder_' . $kind . ( 'default' === $id ? '' : '__' . $id ), $layout['enabled'] );
	register_strings( $kind, $id, $layout );
	do_action( 'radmanct/builder/saved', $kind, $id, $next );
	return array( 'status' => 'saved', 'layout' => $layout, 'revision' => $revision + 1 );
}

function register_strings( string $kind, string $id, array $layout ): void {
	$language = function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\source_language' ) ? \RadmanCT\Integrations\Wpml\Builder\source_language( $kind, $id ) : null;
	require_once __DIR__ . '/tree.php';
	foreach ( \RadmanCT\Builder\Tree\items( $layout['rows'] ) as $item ) {
		if ( 'social' === $item['type'] ) {
			require_once __DIR__ . '/elements.php';
			foreach ( \RadmanCT\Builder\Elements\strings( $item ) as $property => $value ) {
				register_string( string_key( $kind, $id, $item['id'], $property ), $value, $language );
			}
		}
		if ( 'button' === $item['type'] && ! empty( $item['buttonOptions']['secondary_label'] ) ) { register_string( string_key( $kind, $id, $item['id'], 'button_secondary_label' ), $item['buttonOptions']['secondary_label'], $language ); }
		if ( 'button' === $item['type'] && ! empty( $item['buttonOptions']['image_caption'] ) ) { register_string( string_key( $kind, $id, $item['id'], 'button_image_caption' ), $item['buttonOptions']['image_caption'], $language ); }
		foreach ( array( 'text', 'url' ) as $property ) {
			if ( isset( $item[ $property ] ) ) {
				register_string( string_key( $kind, $id, $item['id'], $property ), $item[ $property ], $language );
			}
		}
	}
}

function string_key( string $kind, string $id, string $item, string $property ): string {
	return $kind . '/' . ( 'default' === $id ? '' : $id . '/' ) . $item . '/' . $property;
}

function register_string( string $key, string $value, ?string $language ): void {
	$args = array( 'Radman Builder', $key, $value );
	if ( null !== $language ) { $args[] = false; $args[] = $language; }
	do_action( 'wpml_register_single_string', ...$args );
}

function create( string $kind, string $title, array $layout ): ?string {
	$old = get_option( 'radman_builder_library', null );
	$index = is_array( $old ) ? $old : array();
	if ( count( array_diff_key( $index[ $kind ] ?? array(), array( 'default' => true ) ) ) >= 12 ) {
		return null;
	}
	$id = 'l' . str_replace( '-', '', wp_generate_uuid4() );
	$option = key( $kind, $id );
	if ( ! add_option( $option, $layout + array( 'revision' => 1 ), '', false ) ) {
		return null;
	}
	$index[ $kind ][ $id ] = $title;
	if ( ! compare_exchange( 'radman_builder_library', $old, $index ) ) {
		delete_option( $option );
		return null;
	}
	set_theme_mod( $option, $layout['enabled'] );
	register_strings( $kind, $id, $layout );
	do_action( 'radmanct/builder/saved', $kind, $id, $layout + array( 'revision' => 1 ) );
	return $id;
}

function rename( string $kind, string $id, string $title ): bool {
	$old = get_option( 'radman_builder_library', null );
	if ( ! exists( $kind, $id ) ) {
		return false;
	}
	$next = is_array( $old ) ? $old : array();
	$next[ $kind ][ $id ] = $title;
	return $next === $old || compare_exchange( 'radman_builder_library', $old, $next );
}

function remove( string $kind, string $id ): bool {
	$old = get_option( 'radman_builder_library', null );
	if ( ! exists( $kind, $id ) ) {
		return false;
	}
	$next = is_array( $old ) ? $old : array();
	unset( $next[ $kind ][ $id ] );
	if ( 'default' === $id ) { $next['deleted'][ $kind ] = true; }
	if ( ! compare_exchange( 'radman_builder_library', $old, $next ) ) {
		return false;
	}
	delete_option( key( $kind, $id ) );
	remove_theme_mod( key( $kind, $id ) );
	do_action( 'radmanct/builder/deleted', $kind, $id );
	return true;
}
