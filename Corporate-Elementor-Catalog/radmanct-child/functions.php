<?php

namespace RadmanCTChild;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/inc/home.php';
require_once __DIR__ . '/inc/blog/archive.php';
require_once __DIR__ . '/inc/blog/journal.php';
require_once __DIR__ . '/inc/blog/sidebar.php';
require_once __DIR__ . '/inc/blog/single.php';
require_once __DIR__ . '/inc/catalog/sidebar.php';
require_once __DIR__ . '/inc/catalog/archive.php';
require_once __DIR__ . '/inc/portfolio/archive.php';
require_once __DIR__ . '/inc/home/offering-art.php';
require_once __DIR__ . '/inc/icons/offerings.php';
require_once __DIR__ . '/inc/content/navigation.php';

function setup(): void {
	load_child_theme_textdomain( 'radmanct-child', get_stylesheet_directory() . '/languages' );
}

function assets(): void {
	\RadmanCT\Assets\style( 'radmanct-child', 'src/css/client.css', 'child', array( 'rdct-theme' ) );
	if ( is_page_template( 'templates/custom-page.php' ) ) {
		\RadmanCT\Assets\style( 'rdct-client-page', 'src/css/pages/custom-page.css', 'child', array( 'radmanct-child' ) );
	}
	if ( is_page_template( Home\TEMPLATE ) ) {
		\RadmanCT\Assets\style( 'rdct-client-home', 'src/css/pages/client-home.css', 'child', array( 'radmanct-child' ) );
		\RadmanCT\Assets\style( 'rdct-client-offerings', 'src/css/pages/home/offerings.css', 'child', array( 'rdct-client-home' ) );
		if ( Home\image_id( get_queried_object_id() ) || Home\OfferingArt\available( get_queried_object_id() ) ) {
			$art = \RadmanCT\Assets\entry( 'src/js/pages/home/offering-art.js', 'child' );
			if ( $art ) { wp_enqueue_script_module( 'rdct-client-offer-art', \RadmanCT\Assets\url( $art['file'], 'child' ), array(), null ); }
		}
	}
}

function templates( array $templates ): array {
	$templates['templates/custom-page.php'] = __( 'Custom client page', 'radmanct-child' );
	$templates[ Home\TEMPLATE ] = __( 'Custom client home', 'radmanct-child' );
	return $templates;
}

function custom_templates( array $templates ): array {
	$templates[] = 'templates/custom-page.php';
	$templates[] = Home\TEMPLATE;
	return array_values( array_unique( $templates ) );
}

function defaults( array $settings ): array {
	$settings['brand_color'] = '#305da8';
	$settings['container_width'] = 1216;
	return $settings;
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup', 11 );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 20 );
add_filter( 'theme_page_templates', __NAMESPACE__ . '\\templates' );
add_filter( 'radmanct/layout/custom_templates', __NAMESPACE__ . '\\custom_templates' );
add_filter( 'radmanct/settings/defaults', __NAMESPACE__ . '\\defaults' );
