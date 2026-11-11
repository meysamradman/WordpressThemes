<?php

namespace RadmanCTChild\Content;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( ! function_exists( 'rdct_register_content' ) ) { return; }
	rdct_register_content( 'site-navigation', array(
		'title' => __( 'Site navigation', 'radmanct-child' ),
		'render' => static function ( array $context ): void {
			get_template_part( 'template-parts/content/navigation', null, $context );
		},
		'assets' => static function (): void {
			\RadmanCT\Assets\style( 'rdct-client-navigation', 'src/css/content/navigation.css', 'child', array( 'radmanct-child' ) );
			if ( did_action( 'wp_head' ) ) { wp_print_styles( 'rdct-client-navigation' ); }
		},
	) );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\\register', 15 );
