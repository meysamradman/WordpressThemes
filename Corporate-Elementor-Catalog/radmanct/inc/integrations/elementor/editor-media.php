<?php

namespace RadmanCT\Integrations\Elementor\EditorMedia;

defined( 'ABSPATH' ) || exit;

function print_templates(): void {
	if ( ! did_action( 'wp_enqueue_media' ) || ! function_exists( 'wp_print_media_templates' ) ) { return; }
	if ( ! did_action( 'print_media_templates' ) ) { wp_print_media_templates(); }
	remove_action( 'wp_footer', 'wp_print_media_templates' );
}

add_action( 'elementor/editor/footer', __NAMESPACE__ . '\\print_templates', 0 );
add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
	remove_action( 'wp_footer', 'wp_print_media_templates' );
} );
