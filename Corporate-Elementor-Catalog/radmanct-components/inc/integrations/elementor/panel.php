<?php

namespace RadmanCTComponents\Integrations\Elementor\Panel;

defined( 'ABSPATH' ) || exit;

function categories( array $categories ): array {
	$first = array();
	foreach ( array( 'radmanct-atomic', 'radmanct-components' ) as $key ) { if ( isset( $categories[ $key ] ) ) { $first[ $key ] = $categories[ $key ]; } }
	return $first + $categories;
}

function editor_settings( array $settings ): array {
	if ( is_array( $settings['initial_document']['panel']['elements_categories'] ?? null ) ) {
		$settings['initial_document']['panel']['elements_categories'] = categories( $settings['initial_document']['panel']['elements_categories'] );
	}
	return $settings;
}

add_action( 'elementor/elements/categories_registered', static function ( $manager ): void {
	if ( \RadmanCTComponents\Integrations\Elementor\Atomic\enabled() ) {
		$manager->add_category( 'radmanct-atomic', array( 'title' => __( 'Radman CT — Atomic', 'radmanct-components' ), 'icon' => 'eicon-star', 'active' => true, 'hideIfEmpty' => true ) );
	}
	$manager->add_category( 'radmanct-components', array( 'title' => __( 'Radman CT — Classic', 'radmanct-components' ), 'icon' => 'eicon-star', 'active' => ! \RadmanCTComponents\Integrations\Elementor\Atomic\enabled(), 'hideIfEmpty' => true ) );
} );
add_filter( 'elementor/editor/localize_settings', __NAMESPACE__ . '\\editor_settings', 100 );
