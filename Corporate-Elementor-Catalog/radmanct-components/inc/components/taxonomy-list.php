<?php

namespace RadmanCTComponents\Components\TaxonomyList;

defined( 'ABSPATH' ) || exit;

function taxonomies(): array {
	$result = array();
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $name => $taxonomy ) { $result[ $name ] = $taxonomy->labels->name; }
	return $result;
}

function fields(): array {
	return array(
		'taxonomy' => array( 'label' => __( 'Taxonomy', 'radmanct-components' ), 'default' => 'category', 'options' => taxonomies() ),
		'layout' => array( 'label' => __( 'Layout', 'radmanct-components' ), 'default' => 'tree', 'options' => array( 'tree' => __( 'Category tree', 'radmanct-components' ), 'chips' => __( 'Tag cloud', 'radmanct-components' ) ) ),
		'counts' => array( 'label' => __( 'Show counts', 'radmanct-components' ), 'default' => true ),
		'hierarchical' => array( 'label' => __( 'Hierarchy', 'radmanct-components' ), 'default' => true ),
		'hide_empty' => array( 'label' => __( 'Hide empty terms', 'radmanct-components' ), 'default' => true ),
		'limit' => array( 'label' => __( 'Maximum terms', 'radmanct-components' ), 'default' => 100, 'min' => 1, 'max' => 200 ),
		'text_color' => array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'accent_color' => array( 'label' => __( 'Accent color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'line_color' => array( 'label' => __( 'Border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
	);
}

function settings( array $input ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : ( 'taxonomy' === $key ? '' : $field['default'] ); }
		elseif ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 'yes', 'true', 1 ), true ); }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) ? max( $field['min'], min( $field['max'], (int) $value ) ) : $field['default']; }
		else { $value = is_string( $value ) && preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ? $value : ''; }
		$result[ $key ] = $value;
	}
	return $result;
}

function assets( array $options ): array { return array( 'chips' === ( $options['layout'] ?? 'tree' ) ? 'taxonomy-chips' : 'category-tree' ); }

function render( array $input = array() ): string {
	$s = settings( $input );
	if ( ! $s['taxonomy'] ) { return ''; }
	$hierarchy = $s['hierarchical'] && is_taxonomy_hierarchical( $s['taxonomy'] );
	$terms = get_terms( array( 'taxonomy' => $s['taxonomy'], 'hide_empty' => $s['hide_empty'], 'number' => $s['limit'], 'orderby' => 'name', 'update_term_meta_cache' => false, 'pad_counts' => $hierarchy && $s['counts'] ) );
	if ( is_wp_error( $terms ) || ! $terms ) { return ''; }
	$current = get_queried_object();
	$id = $current instanceof \WP_Term && $current->taxonomy === $s['taxonomy'] ? (int) $current->term_id : 0;
	$style = '';
	foreach ( array( 'text_color' => 'color', 'accent_color' => '--rdct-accent', 'line_color' => '--rdct-line' ) as $key => $property ) { if ( $s[ $key ] ) { $style .= $property . ':' . $s[ $key ] . ';'; } }
	ob_start();
	echo '<div class="rdct-taxonomy"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
	if ( 'tree' === $s['layout'] ) {
		\RadmanCTComponents\UI\category_tree( $terms, array( 'current' => $id, 'ancestors' => $id ? get_ancestors( $id, $s['taxonomy'], 'taxonomy' ) : array(), 'counts' => $s['counts'], 'hierarchical' => $hierarchy ) );
	} else {
		\RadmanCTComponents\UI\term_chips( $terms, array( 'current' => $id, 'counts' => $s['counts'] ) );
	}
	echo '</div>';
	return (string) ob_get_clean();
}
