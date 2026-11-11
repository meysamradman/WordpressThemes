<?php

namespace RadmanCTComponents\Integrations\Elementor\IconPicker;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 2 ) . '/icons/elementor.php';

add_action( 'elementor/controls/register', static function ( $manager ): void {
	require_once __DIR__ . '/icon-picker-control.php';
	$manager->register( new IconControl() );
} );
add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	$entry = \RadmanCTComponents\Assets\entry( 'src/js/admin/icon-controls.js' );
	if ( ! $entry ) { return; }
	$deps = array( 'wp-element', 'elementor-editor' );
	if ( wp_script_is( 'elementor-v2-editor-editing-panel', 'registered' ) ) { $deps[] = 'elementor-v2-editor-editing-panel'; }
	wp_enqueue_script( 'rdct-icon-controls', \RadmanCTComponents\Assets\url( $entry['file'] ), $deps, null, true );
	$config = array(
		'library' => \RadmanCTShared\Icons\Elementor\tabs( array() )['rdct-local'],
		'names' => array_keys( \RadmanCTComponents\Icons\catalog() ), 'labels' => \RadmanCTComponents\Icons\labels(),
		'choose' => __( 'Choose icon', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ),
		'localOnly' => __( 'Choose an icon from Radman CT. Project icons are registered in the shared local catalog.', 'radmanct-components' ),
		'loadError' => __( 'The icon library could not load. Try again.', 'radmanct-components' ),
	);
	wp_add_inline_script( 'rdct-icon-controls', 'window.rdctIconControls=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
} );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array { if ( 'rdct-icon-controls-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; } return $attributes; } );
