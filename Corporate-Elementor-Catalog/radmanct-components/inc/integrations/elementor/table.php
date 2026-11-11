<?php

namespace RadmanCTComponents\Integrations\Elementor\Table;

defined( 'ABSPATH' ) || exit;

add_action( 'elementor/controls/register', static function ( $manager ): void {
	require_once __DIR__ . '/table-control.php';
	$manager->register( new GridControl() );
} );

add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/admin/table-controls.js' );
	if ( ! $entry ) { return; }
	$dependencies = array( 'wp-element', 'elementor-editor' );
	if ( wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { $dependencies[] = 'elementor-v2-editor-editing-panel'; }
	wp_enqueue_script( 'rdct-table-controls', \RadmanCTComponents\Assets\url( $entry['file'] ), $dependencies, null, true );
	\RadmanCTComponents\Assets\style( 'rdct-table-controls', 'src/css/admin/table.css' );
	$labels = array( 'table' => __( 'Table content', 'radmanct-components' ), 'row' => __( 'Row', 'radmanct-components' ), 'column' => __( 'Column', 'radmanct-components' ), 'earlier' => __( 'Move earlier', 'radmanct-components' ), 'later' => __( 'Move later', 'radmanct-components' ), 'up' => __( 'Move up', 'radmanct-components' ), 'down' => __( 'Move down', 'radmanct-components' ), 'removeColumn' => __( 'Remove column', 'radmanct-components' ), 'removeRow' => __( 'Remove row', 'radmanct-components' ), 'duplicate' => __( 'Duplicate row', 'radmanct-components' ), 'addRow' => __( 'Add row', 'radmanct-components' ), 'addColumn' => __( 'Add column', 'radmanct-components' ) );
	$labels['limit'] = __( 'Table content is too large. Shorten a cell or remove a row.', 'radmanct-components' );
	wp_add_inline_script( 'rdct-table-controls', 'window.rdctTableControl=' . wp_json_encode( array( 'labels' => $labels ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
} );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array { if ( 'rdct-table-controls-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; } return $attributes; } );
