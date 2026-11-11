<?php

namespace RadmanCT\ContentBlocks;

defined( 'ABSPATH' ) || exit;

function allowed_types(): array {
	return array( 'core/paragraph', 'core/heading', 'core/image', 'core/list', 'core/list-item', 'core/quote', 'core/group', 'core/columns', 'core/column', 'core/buttons', 'core/button', 'core/separator', 'core/spacer', 'core/table', 'core/gallery' );
}

function inspect( int $id ): ?array {
	$state = array( 'remaining' => 256, 'posts' => array(), 'mapped' => array() );
	$root = inspect_reference( $id, $state, array(), 0 );
	return null === $root ? null : array( 'root' => $root, 'posts' => $state['posts'], 'mapped' => $state['mapped'] );
}

function inspect_reference( int $id, array &$state, array $ancestors, int $depth ): ?int {
	if ( $id < 1 || $depth > 16 || count( $ancestors ) >= 8 ) {
		return null;
	}
	$mapped = (int) apply_filters( 'wpml_object_id', $id, 'wp_block', false );
	if ( $mapped < 1 || in_array( $mapped, $ancestors, true ) ) {
		return null;
	}
	$post = get_post( $mapped );
	if ( ! $post || 'wp_block' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password || strlen( $post->post_content ) > 131072 ) {
		return null;
	}
	$nodes = parse_blocks( $post->post_content );
	$state['mapped'][ $id ] = $mapped;
	$state['posts'][ $mapped ] = $nodes;
	$ancestors[] = $mapped;
	return inspect_nodes( $nodes, $state, $ancestors, $depth ) ? $mapped : null;
}

function inspect_nodes( array $nodes, array &$state, array $ancestors, int $depth ): bool {
	if ( $depth > 16 ) {
		return false;
	}
	foreach ( $nodes as $node ) {
		if ( --$state['remaining'] < 0 ) {
			return false;
		}
		$name = $node['blockName'];
		if ( null === $name ) {
			if ( '' !== trim( $node['innerHTML'] ) ) {
				return false;
			}
			continue;
		}
		if ( 'core/block' === $name ) {
			$ref = $node['attrs']['ref'] ?? null;
			if ( ! is_int( $ref ) || null === inspect_reference( $ref, $state, $ancestors, $depth + 1 ) ) {
				return false;
			}
		} elseif ( ! in_array( $name, allowed_types(), true ) || ! inspect_nodes( $node['innerBlocks'], $state, $ancestors, $depth + 1 ) ) {
			return false;
		}
	}
	return true;
}

function render( int $id, string $prefix ): string {
	$tree = inspect( $id );
	if ( null === $tree ) {
		return '';
	}
	$stack = array( $tree['root'] );
	$remaining = 256;
	$guard = static function ( $pre, array $node ) use ( $tree, &$stack, &$remaining ) {
		if ( --$remaining < 0 ) {
			return '';
		}
		if ( 'core/block' === $node['blockName'] ) {
			$ref = $node['attrs']['ref'] ?? 0;
			$mapped = is_int( $ref ) ? ( $tree['mapped'][ $ref ] ?? 0 ) : 0;
			if ( ! $mapped || in_array( $mapped, $stack, true ) || count( $stack ) >= 8 ) {
				return '';
			}
			$stack[] = $mapped;
			try {
				return implode( '', array_map( 'render_block', $tree['posts'][ $mapped ] ) );
			} finally {
				array_pop( $stack );
			}
		}
		if ( null === $node['blockName'] ) {
			return '';
		}
		return in_array( $node['blockName'], allowed_types(), true ) ? $pre : '';
	};
	add_filter( 'pre_render_block', $guard, PHP_INT_MAX, 2 );
	try {
		$html = wp_kses_post( implode( '', array_map( 'render_block', $tree['posts'][ $tree['root'] ] ) ) );
	} finally {
		remove_filter( 'pre_render_block', $guard, PHP_INT_MAX );
	}
	return scope_ids( $html, $prefix );
}

function scope_ids( string $html, string $prefix ): string {
	$ids = array();
	$processor = new \WP_HTML_Tag_Processor( $html );
	while ( $processor->next_tag() ) {
		$id = $processor->get_attribute( 'id' );
		if ( is_string( $id ) && '' !== $id ) {
			$ids[ $id ] = $prefix . $id;
		}
	}
	$processor = new \WP_HTML_Tag_Processor( $html );
	while ( $processor->next_tag() ) {
		$id = $processor->get_attribute( 'id' );
		if ( is_string( $id ) && isset( $ids[ $id ] ) ) {
			$processor->set_attribute( 'id', $ids[ $id ] );
		}
		$href = $processor->get_attribute( 'href' );
		if ( is_string( $href ) && str_starts_with( $href, '#' ) && isset( $ids[ substr( $href, 1 ) ] ) ) {
			$processor->set_attribute( 'href', '#' . $ids[ substr( $href, 1 ) ] );
		}
		foreach ( array( 'for', 'aria-labelledby', 'aria-describedby', 'aria-controls' ) as $attribute ) {
			$value = $processor->get_attribute( $attribute );
			if ( is_string( $value ) ) {
				$tokens = preg_split( '/\s+/', trim( $value ) );
				$processor->set_attribute( $attribute, implode( ' ', array_map( static function ( string $token ) use ( $ids ): string { return $ids[ $token ] ?? $token; }, $tokens ) ) );
			}
		}
	}
	return $processor->get_updated_html();
}
