<?php

namespace RadmanCT\Layout;

defined( 'ABSPATH' ) || exit;

function elementor_content(): bool {
	return is_singular() || \RadmanCT\Renderers\uses_elementor();
}

function content_attributes( ?string $scope = null ): string {
	return 'class="rdct-lyt-external"';
}

function theme_json( $data ) {
	$settings = \RadmanCT\Settings\values();
	$values = resolve( $settings, 'content' );
	$wide = 'custom' === $values['layout'] ? $values['width'] : $settings['container_width'];
	$data->update_with( array( 'version' => 3, 'settings' => array( 'layout' => array( 'contentSize' => min( $settings['reading_width'], $wide ) . 'px', 'wideSize' => $wide . 'px' ) ) ) );
	return $data;
}

add_filter( 'wp_theme_json_data_theme', __NAMESPACE__ . '\\theme_json' );
