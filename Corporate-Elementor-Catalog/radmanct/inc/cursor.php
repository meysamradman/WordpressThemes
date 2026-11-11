<?php

namespace RadmanCT\Cursor;

defined( 'ABSPATH' ) || exit;

function enqueue(): void {
	\RadmanCT\Assets\style( 'rdct-cursor', 'src/css/motion/cursor.css', 'parent', array( 'rdct-theme' ) );
}

function markup(): void {
	$settings = \RadmanCT\Settings\values();
	$style = '--cursor-inner:' . $settings['cursor_inner_size'] . 'px;--cursor-outer:' . $settings['cursor_outer_size'] . 'px;--cursor-color:' . $settings['cursor_color'] . ';--cursor-hover:' . $settings['cursor_hover_color'] . ';mix-blend-mode:' . $settings['cursor_blend'] . ';';
	$style .= '--cursor-hover-duration:' . $settings['cursor_hover_duration'] . 's;--cursor-hover-scale:' . $settings['cursor_hover_scale'] . ';';
	$classes = 'rdct-cursor' . ( $settings['cursor_hide_outer'] ? ' rdct-cursor--no-ring' : '' );
	echo '<div id="rdct-cursor" class="' . esc_attr( $classes ) . '" style="' . esc_attr( $style ) . '" aria-hidden="true" inert hidden';
	foreach ( array( 'image', 'carousel' ) as $kind ) {
		$label = apply_filters( 'wpml_translate_single_string', $settings[ 'cursor_' . $kind . '_label' ], 'Radman Cursor', $kind . '-label' );
		echo ' data-' . $kind . '-label="' . esc_attr( $label ) . '"';
	}
	echo ' data-hover-effect="' . esc_attr( $settings['cursor_hover_effect'] ) . '" data-follow-duration="' . esc_attr( (string) $settings['cursor_follow_duration'] ) . '"><span class="rdct-cursor__dot"></span><span class="rdct-cursor__ring"><span class="rdct-cursor__label"></span></span></div>';
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
add_action( 'wp_footer', __NAMESPACE__ . '\\markup', 30 );
