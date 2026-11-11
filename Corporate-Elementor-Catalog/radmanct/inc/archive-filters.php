<?php

namespace RadmanCT\ArchiveFilters;

defined( 'ABSPATH' ) || exit;

function terms( string $taxonomy, string $selected ): array {
	static $cache = array();
	$language = apply_filters( 'wpml_current_language', '' );
	$key = ( is_string( $language ) ? $language : '' ) . '/' . $taxonomy;
	if ( ! isset( $cache[ $key ] ) ) {
		$found = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'number' => 100, 'orderby' => 'name' ) );
		$cache[ $key ] = is_wp_error( $found ) ? array() : $found;
	}
	$result = $cache[ $key ];
	if ( '' !== $selected && ! in_array( $selected, wp_list_pluck( $result, 'slug' ), true ) ) {
		$selected_key = $key . '/' . $selected;
		if ( ! array_key_exists( $selected_key, $cache ) ) { $cache[ $selected_key ] = get_term_by( 'slug', $selected, $taxonomy ); }
		if ( $cache[ $selected_key ] ) { $result[] = $cache[ $selected_key ]; }
	}
	return $result;
}

function sorts(): array {
	return array( '' => __( 'Default order', 'radmanct' ), 'newest' => __( 'Newest', 'radmanct' ), 'oldest' => __( 'Oldest', 'radmanct' ), 'title' => __( 'Title', 'radmanct' ) );
}

function active( array $active, array $values, string $action ): void {
	if ( $active ) {
		echo '<ul class="rdct-cat-active" aria-label="' . esc_attr__( 'Active filters', 'radmanct' ) . '">';
		foreach ( $active as $key => $label ) {
			$remaining = $values;
			unset( $remaining[ $key ] );
			$remaining = array_filter( $remaining, static function ( string $value ): bool { return '' !== $value; } );
			$url = add_query_arg( $remaining, $action );
			echo '<li><a data-rdct-archive-link href="' . esc_url( $url ) . '" aria-label="' . esc_attr( sprintf( __( 'Remove filter: %s', 'radmanct' ), $label ) ) . '">' . esc_html( $label ) . ' <span aria-hidden="true">&times;</span></a></li>';
		}
		echo '</ul>';
	}
}
