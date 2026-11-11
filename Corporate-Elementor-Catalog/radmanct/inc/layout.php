<?php

namespace RadmanCT\Layout;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/layout-page.php';

function custom_template( int $post_id = 0 ): bool {
	$templates = apply_filters( 'radmanct/layout/custom_templates', array( 'templates/custom.php' ) );
	return is_array( $templates ) && in_array( get_page_template_slug( $post_id ?: null ), $templates, true );
}

function elementor_content(): bool {
	if ( is_page() && custom_template() ) { return false; }
	if ( \RadmanCT\Renderers\uses_elementor() ) { return true; }
	if ( ! is_page() ) { return false; }
	$template = get_page_template_slug();
	if ( in_array( $template, array( 'templates/elementor.php', 'elementor_canvas', 'elementor_header_footer' ), true ) ) { return true; }
	return did_action( 'elementor/loaded' ) && 'builder' === get_post_meta( get_queried_object_id(), '_elementor_edit_mode', true );
}

function content_attributes( ?string $scope = null ): string {
	if ( null === $scope && elementor_content() ) { return 'class="rdct-lyt-external"'; }
	$full_width = null === $scope && is_page() && 'templates/full-width.php' === get_page_template_slug();
	if ( null === $scope && is_page() && get_page_template_slug() && ! $full_width && ! custom_template() && ! apply_filters( 'radmanct/layout/manage_custom_template', false ) ) { return ''; }
	if ( null === $scope ) {
		$context = \RadmanCT\Renderers\context();
		$scope = $context ? implode( '_', $context ) : 'content';
	}
	$settings = \RadmanCT\Settings\values();
	$values = resolve( $settings, $scope );
	if ( $full_width ) { $values['layout'] = 'full'; }
	if ( 'content' === $scope && is_page() ) { $values = Page\resolve( $values, get_post_meta( get_queried_object_id(), Page\META_KEY, true ) ); }
	return 'class="rdct-lyt-content" data-rdct-layout="' . esc_attr( $values['layout'] ) . '" style="' . esc_attr( variables( $values ) ) . '"';
}

function theme_json( $data ) {
	$settings = \RadmanCT\Settings\values();
	$values = resolve( $settings, 'content' );
	$wide = 'custom' === $values['layout'] ? $values['width'] : $settings['container_width'];
	$data->update_with( array( 'version' => 3, 'settings' => array( 'layout' => array( 'contentSize' => min( $settings['reading_width'], $wide ) . 'px', 'wideSize' => $wide . 'px' ) ) ) );
	return $data;
}

add_filter( 'wp_theme_json_data_theme', __NAMESPACE__ . '\\theme_json' );
