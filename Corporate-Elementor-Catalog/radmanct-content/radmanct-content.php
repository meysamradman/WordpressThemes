<?php
/**
 * Plugin Name: Radman Content
 * Description: Catalog and portfolio for Radman CT.
 * Version: 1.1.1
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * Author: Radman
 * Text Domain: radmanct-content
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

namespace RadmanCTContent;

defined( 'ABSPATH' ) || exit;

const FILE = __FILE__;
const VERSION = '1.1.1';

require_once __DIR__ . '/inc/modules.php';
Modules\boot();

add_action( 'init', static function (): void {
	load_plugin_textdomain( 'radmanct-content', false, dirname( plugin_basename( FILE ) ) . '/languages' );
}, 0 );

if ( is_admin() ) {
	require_once __DIR__ . '/inc/admin/settings.php';
}

register_activation_hook( FILE, static function (): void {
	Modules\register_types();
	flush_rewrite_rules( false );
} );
register_deactivation_hook( FILE, static function (): void {
	foreach ( array( 'radman_product', 'radman_portfolio' ) as $type ) {
		if ( post_type_exists( $type ) ) {
			unregister_post_type( $type );
		}
	}
	foreach ( array( 'radman_product_cat', 'radman_product_brand', 'radman_product_feature', 'radman_product_tag', 'radman_portfolio_cat' ) as $taxonomy ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			unregister_taxonomy( $taxonomy );
		}
	}
	flush_rewrite_rules( false );
} );
