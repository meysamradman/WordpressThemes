<?php

namespace RadmanCT\ScrollProgress;

defined( 'ABSPATH' ) || exit;

function enqueue(): void {
	\RadmanCT\Assets\style( 'rdct-scroll-progress', 'src/css/motion/scroll-progress.css', 'parent', array( 'rdct-theme' ) );
}

function markup(): void {
	$settings = \RadmanCT\Settings\values();
	$style = '--progress-height:' . $settings['scroll_progress_height'] . 'px;--progress-color:' . $settings['scroll_progress_color'] . ';--progress-track:' . $settings['scroll_progress_track'] . ';';
	echo '<div id="rdct-scroll-progress" style="' . esc_attr( $style ) . '" aria-hidden="true" hidden><span></span></div>';
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
add_action( 'wp_footer', __NAMESPACE__ . '\\markup', 30 );
