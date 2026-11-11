<?php

namespace RadmanCT\Integrations\Elementor\FrontendTools;

defined( 'ABSPATH' ) || exit;

function allowed(): bool {
	return is_admin() || is_customize_preview() || \RadmanCT\Modules\editor_preview() || \RadmanCT\Layout\elementor_content() || \RadmanCT\Renderers\uses_elementor();
}

function prepare(): void {
	if ( allowed() || ! class_exists( '\\Elementor\\Plugin', false ) || apply_filters( 'radmanct/elementor/frontend_tools', false ) ) { return; }
	$plugin = \Elementor\Plugin::$instance;
	$common = $plugin ? $plugin->common : null;
	if ( ! $common ) { return; }
	remove_action( 'wp_enqueue_scripts', array( $common, 'register_scripts' ), 9 );
	remove_action( 'wp_enqueue_scripts', array( $common, 'register_styles' ), 9 );
	remove_action( 'wp_footer', array( $common, 'print_templates' ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\prepare', 8 );
