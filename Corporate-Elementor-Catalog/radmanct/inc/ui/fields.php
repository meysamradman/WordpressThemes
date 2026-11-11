<?php

namespace RadmanCT\UI;

defined( 'ABSPATH' ) || exit;

function field_assets( bool $select = false, bool $combobox = false ): void {
	if ( function_exists( '\\RadmanCTComponents\\UI\\field_assets' ) ) { \RadmanCTComponents\UI\field_assets( $select, $combobox ); }
}
function searchable_options( array $options, $searchable = 'auto' ): bool {
	return function_exists( '\\RadmanCTComponents\\UI\\searchable_options' ) && \RadmanCTComponents\UI\searchable_options( $options, $searchable );
}
function select_field( string $name, string $label, array $options, string|array $value = '', array $configuration = array() ): void {
	if ( function_exists( '\\RadmanCTComponents\\UI\\select_field' ) ) { \RadmanCTComponents\UI\select_field( $name, $label, $options, $value, $configuration ); }
}
function input_field( string $name, string $label, string $value = '', array $attributes = array(), array $configuration = array() ): void {
	if ( function_exists( '\\RadmanCTComponents\\UI\\input_field' ) ) { \RadmanCTComponents\UI\input_field( $name, $label, $value, $attributes, $configuration ); }
}
