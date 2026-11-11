<?php

namespace RadmanCTComponents\UI;

defined( 'ABSPATH' ) || exit;

function field_assets( bool $select = false, bool $combobox = false ): void {
	\RadmanCTComponents\Components\use_component( 'form-controls' );
	if ( $select ) { \RadmanCTComponents\Components\use_component( 'select-control' ); }
	if ( $combobox ) {
		\RadmanCTComponents\Components\use_component( 'combobox' );
		\RadmanCTComponents\Assets\frontend();
	}
}

function searchable_options( array $options, $searchable = 'auto' ): bool {
	if ( 'auto' !== $searchable ) { return true === $searchable; }
	return count( array_filter( $options, static fn( $option ): bool => '' !== (string) $option[0] ) ) >= 8;
}

function select_field( string $name, string $label, array $options, string|array $value = '', array $configuration = array() ): void {
	$configuration += array( 'searchable' => false, 'multiple' => false, 'disabled' => false, 'required' => false );
	$combobox = $configuration['multiple'] || searchable_options( $options, $configuration['searchable'] );
	$id = $combobox ? wp_unique_id( 'rdct-combobox-' ) : '';
	if ( $combobox ) { echo '<div class="rdct-field"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>'; }
	else { echo '<label>' . esc_html( $label ); }
	if ( $configuration['multiple'] && ! str_ends_with( $name, '[]' ) ) { $name .= '[]'; }
	echo '<select data-rdct-select name="' . esc_attr( $name ) . '"';
	if ( $combobox ) {
		echo ' id="' . esc_attr( $id ) . '" data-rdct-combobox aria-label="' . esc_attr( $label ) . '" data-search-label="' . esc_attr( sprintf( __( 'Search %s', 'radmanct-components' ), $label ) ) . '" data-empty-label="' . esc_attr__( 'No results found.', 'radmanct-components' ) . '" data-remove-label="' . esc_attr__( 'Remove item', 'radmanct-components' ) . '"';
	}
	foreach ( array( 'multiple', 'disabled', 'required' ) as $attribute ) { if ( $configuration[ $attribute ] ) { echo ' ' . $attribute; } }
	echo '>';
	$values = array_map( 'strval', (array) $value );
	foreach ( $options as $option ) {
		echo '<option value="' . esc_attr( $option[0] ) . '"' . ( in_array( (string) $option[0], $values, true ) ? ' selected' : '' ) . ( ! empty( $option[2] ) ? ' disabled' : '' ) . '>' . esc_html( $option[1] ) . '</option>';
	}
	echo $combobox ? '</select></div>' : '</select></label>';
}

function input_field( string $name, string $label, string $value = '', array $attributes = array(), array $configuration = array() ): void {
	$search = 'search' === ( $attributes['type'] ?? '' );
	$label_html = esc_html( $label );
	if ( ! empty( $configuration['hide_label'] ) ) { $label_html = '<span class="rdct-form-label-hidden">' . $label_html . '</span>'; }
	echo '<label>' . $label_html . ( $search ? '<span class="rdct-search-field">' : '' ) . '<input name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"';
	foreach ( $attributes as $key => $attribute ) {
		if ( ! in_array( $key, array( 'id', 'type', 'maxlength', 'min', 'max', 'step', 'placeholder', 'autocomplete', 'required', 'disabled', 'readonly' ), true ) && ! preg_match( '/^(aria|data)-[a-z0-9-]+$/D', $key ) ) { continue; }
		if ( false === $attribute || null === $attribute ) { continue; }
		echo ' ' . esc_attr( $key ) . '="' . esc_attr( true === $attribute ? $key : $attribute ) . '"';
	}
	echo '>';
	if ( $search ) { echo \RadmanCTComponents\Icons\render( 'search', array( 'size' => 20, 'stroke' => 1.75 ) ) . '</span>'; }
	echo '</label>';
}
