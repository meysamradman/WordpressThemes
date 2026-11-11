<?php

namespace RadmanCT\Admin\BuilderPreview;

defined( 'ABSPATH' ) || exit;

function document( string $kind, array $layout, string $direction ): string {
	$direction = 'rtl' === $direction ? 'rtl' : 'ltr';
	$styles = wp_styles();
	$before = $styles->queue;
	\RadmanCT\Assets\style( 'rdct-theme', 'src/css/theme.css' );
	\RadmanCT\Assets\style( 'rdct-builder', 'src/css/builder.css', 'parent', array( 'rdct-theme' ) );
	ob_start();
	\RadmanCT\Builder\render_layout( $kind, $layout, 'default', true );
	$markup = ob_get_clean();
	wp_enqueue_style( 'wp-block-library' );
	wp_enqueue_global_styles();
	$handles = array_values( array_unique( array_merge( array( 'rdct-theme', 'rdct-builder' ), array_diff( $styles->queue, $before ), array( 'wp-block-library', 'global-styles' ) ) ) );
	$printed = $styles->done;
	$styles->done = array();
	ob_start();
	try { wp_print_styles( $handles ); $block_css = ob_get_contents(); }
	finally { ob_end_clean(); $styles->done = $printed; }
	$policy = "default-src 'none'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; form-action 'none'; base-uri 'none'";
	$lang = 'rtl' === $direction ? 'fa' : 'en';
	return '<!doctype html><html lang="' . $lang . '" dir="' . $direction . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><meta http-equiv="Content-Security-Policy" content="' . esc_attr( $policy ) . '">' . $block_css . '<style>' . \RadmanCT\Typography\css() . \RadmanCT\Settings\tokens() . 'body{margin:0;background:#fff} .rdct-preview{min-height:48px} .rdct-preview [data-builder-preview-item]{min-height:32px} .rdct-preview [data-builder-preview-row]{min-height:48px} </style></head><body class="rdct-site"><div class="rdct-preview" inert>' . $markup . '</div></body></html>';
}
