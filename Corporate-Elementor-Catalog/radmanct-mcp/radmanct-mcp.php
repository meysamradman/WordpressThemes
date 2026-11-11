<?php
/**
 * Plugin Name: Radman MCP
 * Description: MCP integration for Radman CT.
 * Version: 1.1.1
 * Requires at least: 6.9
 * Requires PHP: 8.0
 * Author: Radman
 * Text Domain: radmanct-mcp
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

namespace RadmanCT\Mcp;

defined( 'ABSPATH' ) || exit;

const FILE = __FILE__;
const VERSION = '1.1.1';

require_once __DIR__ . '/inc/bootstrap.php';
if ( is_admin() ) { require_once __DIR__ . '/inc/admin/settings.php'; }

add_action( 'init', static function (): void {
	load_plugin_textdomain( 'radmanct-mcp', false, dirname( plugin_basename( FILE ) ) . '/languages' );
}, 0 );
