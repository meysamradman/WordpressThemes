<?php
/**
 * Plugin Name: Radman Components
 * Description: Reusable components for Radman CT.
 * Version: 1.1.1
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * Author: Radman
 * Text Domain: radmanct-components
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

namespace RadmanCTComponents;

defined( 'ABSPATH' ) || exit;

define( 'RDCT_COMPONENTS_FILE', __FILE__ );
define( 'RDCT_COMPONENTS_PATH', __DIR__ );

function url( string $path = '' ): string { return plugins_url( $path, RDCT_COMPONENTS_FILE ); }

require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/components.php';
require_once __DIR__ . '/inc/ui/fields.php';
require_once __DIR__ . '/inc/ui/category-tree.php';
require_once __DIR__ . '/inc/ui/term-chips.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/template-functions.php';
require_once __DIR__ . '/inc/components/card-preview.php';
require_once __DIR__ . '/inc/components/background.php';
require_once __DIR__ . '/inc/components/page-title.php';

add_action( 'init', static function (): void {
	load_plugin_textdomain( 'radmanct-components', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

function elementor(): void {
	require_once __DIR__ . '/inc/integrations/elementor/table.php';
	require_once __DIR__ . '/inc/integrations/elementor/icon-picker.php';
	require_once __DIR__ . '/inc/integrations/elementor/atomic.php';
	require_once __DIR__ . '/inc/integrations/elementor/widgets.php';
	require_once __DIR__ . '/inc/integrations/elementor/atomic-components.php';
	require_once __DIR__ . '/inc/integrations/elementor/hover-border.php';
	add_action( 'elementor/editor/after_enqueue_scripts', static function (): void {
		if ( function_exists( '\\RadmanCTComponents\\Admin\\ComponentsPage\\allowed' ) && \RadmanCTComponents\Admin\ComponentsPage\allowed() ) { \RadmanCTComponents\Admin\ComponentsPage\assets( true ); }
	} );
	add_action( 'elementor/editor/footer', static function (): void {
		if ( function_exists( '\\RadmanCTComponents\\Admin\\ComponentsPage\\editor' ) ) { \RadmanCTComponents\Admin\ComponentsPage\editor(); }
	} );
}

add_action( 'plugins_loaded', static function (): void {
	if ( did_action( 'elementor/loaded' ) ) { elementor(); }
	else { add_action( 'elementor/loaded', __NAMESPACE__ . '\\elementor' ); }
}, 20 );

add_action( 'after_setup_theme', static function (): void {
	if ( ! function_exists( '\\RadmanCT\\Integrations\\Elementor\\EditorMedia\\print_templates' ) ) {
		require_once __DIR__ . '/inc/integrations/elementor/editor-media.php';
	}
}, 30 );

if ( is_admin() ) {
 require_once __DIR__ . '/inc/admin/components-page.php';
 require_once __DIR__ . '/inc/admin/component-render.php';
}
