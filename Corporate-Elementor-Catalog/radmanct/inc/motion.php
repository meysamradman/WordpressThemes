<?php

namespace RadmanCT\Motion;

defined( 'ABSPATH' ) || exit;

function classes( array $classes ): array {
	$settings = \RadmanCT\PageEffects\values();
	if ( $settings['scroll_reveal'] ) {
		$classes[] = 'rdct-reveal-enabled';
	}
	if ( $settings['smooth_scroll'] ) {
		$classes[] = 'rdct-smooth-scroll-enabled';
	}
	return $classes;
}

function open(): void {
	if ( \RadmanCT\PageEffects\values()['smooth_scroll'] ) {
		echo '<div id="rdct-scroll-wrapper" data-smooth-duration="' . esc_attr( (string) \RadmanCT\PageEffects\values()['smooth_duration'] ) . '"><div id="rdct-scroll-content">';
	}
}

function close(): void {
	if ( \RadmanCT\PageEffects\values()['smooth_scroll'] ) {
		echo '</div></div>';
	}
}

function enqueue(): void {
	if ( \RadmanCT\PageEffects\values()['smooth_scroll'] ) {
		\RadmanCT\Assets\style( 'rdct-smooth-scroll', 'src/css/motion/smooth-scroll.css', 'parent', array( 'rdct-theme' ) );
	}
}

add_filter( 'body_class', __NAMESPACE__ . '\\classes' );
add_action( 'radmanct/before_site', __NAMESPACE__ . '\\open' );
add_action( 'radmanct/after_site', __NAMESPACE__ . '\\close' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
