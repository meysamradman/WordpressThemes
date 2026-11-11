<?php

namespace RadmanCT\UI;

defined( 'ABSPATH' ) || exit;

function category_tree( array $terms, array $options = array() ): void {
	$options += array( 'current' => 0, 'ancestors' => array(), 'counts' => true, 'hierarchical' => true, 'url' => 'get_term_link', 'link_attributes' => null );
	$groups = array();
	$ids = array_fill_keys( wp_list_pluck( $terms, 'term_id' ), true );
	foreach ( $terms as $term ) {
		$parent = $options['hierarchical'] && isset( $ids[ $term->parent ] ) ? (int) $term->parent : 0;
		$groups[ $parent ][] = $term;
	}
	$branches = count( $groups ) > 1;
	$arrow = $branches ? \RadmanCT\icon( 'chevron-down' ) : '';
	$render = static function ( int $parent, int $depth = 0 ) use ( &$render, $groups, $options, $branches, $arrow ): void {
		if ( $depth > 20 || empty( $groups[ $parent ] ) ) { return; }
		echo '<ul' . ( 0 === $depth ? ' class="rdc-cat-tree" data-branches="' . ( $branches ? 'true' : 'false' ) . '" data-counts="' . ( $options['counts'] ? 'true' : 'false' ) . '"' : '' ) . '>';
		foreach ( $groups[ $parent ] as $term ) {
			$url = call_user_func( $options['url'], $term );
			if ( is_wp_error( $url ) || ! is_string( $url ) ) { continue; }
			$current = (int) $options['current'] === (int) $term->term_id;
			$children = ! empty( $groups[ $term->term_id ] );
			echo '<li>';
			if ( $children ) {
				echo '<details' . ( $current || in_array( (int) $term->term_id, $options['ancestors'], true ) ? ' open' : '' ) . '><summary class="rdc-cat-tree__row">';
			} else { echo '<div class="rdc-cat-tree__row">'; }
			echo '<a href="' . esc_url( $url ) . '"' . ( $current ? ' aria-current="page"' : '' );
			$attributes = is_callable( $options['link_attributes'] ) ? call_user_func( $options['link_attributes'], $term ) : array();
			foreach ( $attributes as $name => $value ) {
				if ( preg_match( '/^data-[a-z0-9-]+$/D', $name ) ) { echo ' ' . $name . '="' . esc_attr( $value ) . '"'; }
			}
			echo '>' . esc_html( $term->name ) . '</a>';
			if ( $options['counts'] ) { echo '<span class="rdc-cat-tree__num">' . esc_html( number_format_i18n( $term->count ) ) . '</span>'; }
			echo $children ? $arrow . '</summary>' : '</div>';
			if ( $children ) { $render( (int) $term->term_id, $depth + 1 ); echo '</details>'; }
			echo '</li>';
		}
		echo '</ul>';
	};
	$render( 0 );
}
