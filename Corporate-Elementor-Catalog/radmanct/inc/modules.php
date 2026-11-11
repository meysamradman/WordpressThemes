<?php

namespace RadmanCT\Modules;

defined( 'ABSPATH' ) || exit;

function register(): void {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}
	$settings = \RadmanCT\PageEffects\values();
	if ( ! ( $settings['scroll_reveal'] || $settings['smooth_scroll'] || $settings['custom_cursor'] || $settings['scroll_progress'] || $settings['preloader'] ) || editor_preview() ) {
		return;
	}
	if ( ( $settings['scroll_reveal'] || $settings['smooth_scroll'] ) && apply_filters( 'radmanct/motion/allowed', true ) ) {
		require_once __DIR__ . '/motion.php';
	}
	if ( $settings['custom_cursor'] && apply_filters( 'radmanct/cursor/allowed', true ) ) {
		require_once __DIR__ . '/cursor.php';
	}
	if ( $settings['scroll_progress'] && apply_filters( 'radmanct/scroll_progress/allowed', true ) ) {
		require_once __DIR__ . '/scroll-progress.php';
	}
	if ( $settings['preloader'] && apply_filters( 'radmanct/preloader/allowed', true ) ) {
		require_once __DIR__ . '/preloader.php';
	}
}

function editor_preview(): bool {
	if ( isset( $_GET['elementor-preview'] ) ) {
		return true;
	}
	if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\\Elementor\\Plugin' ) ) {
		return false;
	}
	$plugin = \Elementor\Plugin::$instance;
	return $plugin && ( ( $plugin->editor && $plugin->editor->is_edit_mode() ) || ( $plugin->preview && $plugin->preview->is_preview_mode() ) );
}

add_action( 'wp', __NAMESPACE__ . '\\register' );
