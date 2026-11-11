<?php

namespace RadmanCTComponents\Components\ArchiveSearch;

defined( 'ABSPATH' ) || exit;

function types(): array {
	$result = array( '' => __( 'All content', 'radmanct-components' ) );
	foreach ( get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'objects' ) as $name => $type ) { $result[ $name ] = $type->labels->name; }
	return $result;
}

function fields(): array {
	return array(
		'post_type' => array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'post', 'options' => types() ),
		'layout' => array( 'label' => __( 'Layout', 'radmanct-components' ), 'default' => 'stacked', 'options' => array( 'stacked' => __( 'Stacked', 'radmanct-components' ), 'inline' => __( 'Inline', 'radmanct-components' ) ) ),
		'label' => array( 'label' => __( 'Field label', 'radmanct-components' ), 'default' => __( 'Search', 'radmanct-components' ) ),
		'show_label' => array( 'label' => __( 'Show label', 'radmanct-components' ), 'default' => true ),
		'placeholder' => array( 'label' => __( 'Placeholder', 'radmanct-components' ), 'default' => __( 'Search the site', 'radmanct-components' ) ),
		'ajax' => array( 'label' => __( 'Use the Radman archive target when available', 'radmanct-components' ), 'default' => true ),
	);
}

function render( array $input = array() ): string {
	$type = $input['post_type'] ?? 'post';
	if ( ! is_string( $type ) || ! array_key_exists( $type, types() ) ) { return ''; }
	$placeholder = is_string( $input['placeholder'] ?? null ) ? sanitize_text_field( $input['placeholder'] ) : __( 'Search the site', 'radmanct-components' );
	$ajax = in_array( $input['ajax'] ?? true, array( true, 'yes', 'true', 1 ), true );
	$id = wp_unique_id( 'rdct-search-' );
	$layout = 'inline' === ( $input['layout'] ?? '' ) ? 'inline' : 'stacked';
	$label = is_string( $input['label'] ?? null ) && '' !== trim( $input['label'] ) ? sanitize_text_field( $input['label'] ) : __( 'Search', 'radmanct-components' );
	$show_label = in_array( $input['show_label'] ?? true, array( true, 'yes', 'true', 1 ), true );
	$html = '<form class="rdct-search rdct-form rdct-component-search rdct-component-search--' . $layout . '" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '"' . ( $ajax ? ' data-rdct-archive-form' : '' ) . '>';
	if ( $type ) { $html .= '<input type="hidden" name="post_type" value="' . esc_attr( $type ) . '">'; }
	ob_start();
	\RadmanCTComponents\UI\input_field( 's', $label, get_search_query( false ), array( 'id' => $id, 'type' => 'search', 'placeholder' => $placeholder, 'maxlength' => 80 ), array( 'hide_label' => ! $show_label ) );
	$html .= ob_get_clean();
	return $html . '</form>';
}
