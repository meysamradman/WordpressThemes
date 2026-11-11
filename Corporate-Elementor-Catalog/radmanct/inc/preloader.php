<?php

namespace RadmanCT\Preloader;

defined( 'ABSPATH' ) || exit;

function enqueue(): void {
	\RadmanCT\Assets\style( 'rdct-preloader', 'src/css/motion/preloader.css', 'parent', array( 'rdct-theme' ) );
}

function markup(): void {
	$asset = \RadmanCT\Assets\entry( 'src/js/preloader.js' );
	if ( ! $asset || ! \RadmanCT\Assets\entry( 'src/css/motion/preloader.css' ) ) {
		return;
	}
	$script = file_get_contents( get_template_directory() . '/dist/' . $asset['file'] );
	if ( ! is_string( $script ) || '' === trim( $script ) ) { return; }
	$settings = \RadmanCT\Settings\values();
	$variant = $settings['preloader_style'];
	$duration = array( 'curtain' => 715, 'sliding' => 500, 'dissolve' => 912, 'fade' => 500, 'spinner' => 500, 'spinner-classic' => 500 )[ $variant ] ?? 300;
	$back = $settings['preloader_background_secondary'] ?? '';
	$style = '--rdct-preloader-background:' . $settings['preloader_background'] . ';--rdct-preloader-back:' . ( $back ?: $settings['preloader_color'] ) . ';--rdct-preloader-color:' . $settings['preloader_color'] . ';';
	echo '<div id="rdct-preloader" class="rdct-pre--' . esc_attr( $variant ) . '" data-exit-duration="' . $duration . '" style="' . esc_attr( $style ) . '" aria-hidden="true" inert hidden>';
	if ( in_array( $variant, array( 'curtain', 'sliding', 'dissolve' ), true ) ) {
		$count = 'dissolve' === $variant ? 27 : ( 'curtain' === $variant ? 2 : 1 );
		for ( $i = 0; $i < $count; $i++ ) { echo '<span class="rdct-pre__panel" style="--rdct-panel:' . $i . '"></span>'; }
	}
	if ( 'image' === $settings['preloader_style'] ) {
		echo '<img src="' . esc_url( get_template_directory_uri() . '/images/preloader/img-preloader.gif' ) . '" alt="" width="64" height="64">';
	} elseif ( 'spinner' === $variant ) {
		echo '<svg class="rdct-pre__spinner" width="64" height="64" viewBox="0 0 64 64" focusable="false"><circle cx="32" cy="32" r="28" fill="none" stroke-width="5" /></svg>';
	} elseif ( 'spinner-classic' === $variant ) {
		echo '<span class="rdct-pre__indicator"></span>';
	}
	echo '</div>';
	wp_print_inline_script_tag( $script, array( 'id' => 'rdct-preloader-init', 'data-nowprocket' => true ) );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
add_action( 'wp_body_open', __NAMESPACE__ . '\\markup', 1 );
