<?php
/**
 * Hub demo content importer loader.
 *
 * Loads the vendored upstream WordPress Importer (0.9.6, see
 * vendor/wordpress-importer/) plus the Hub subclass (class-hub-import.php)
 * that adds Liquid mega-menu meta support.
 *
 * This file intentionally does NOT register the Tools > Import admin UI;
 * Hub drives imports programmatically via LiquidImporter::set_demo_data().
 * It is safe to load alongside the official wordpress-importer plugin:
 * every include is guarded by class_exists().
 *
 * @package Hub_Core
 * @subpackage Importer
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$hub_importer_vendor_dir = dirname( __FILE__ ) . '/vendor/wordpress-importer';

// Base importer API.
if ( ! class_exists( 'WP_Importer', false ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) ) {
		require_once $class_wp_importer;
	}
}

// Functions missing in older WordPress versions.
if ( ! function_exists( 'wp_slash_strings_only' ) ) {
	$hub_importer_compat = $hub_importer_vendor_dir . '/compat.php';
	if ( file_exists( $hub_importer_compat ) ) {
		require_once $hub_importer_compat;
	}
}

// URL rewriting toolkit (WPURL, wp_rewrite_urls). Only one copy may be
// loaded per request: the composer autoloader inside php-toolkit declares
// a fixed ComposerAutoloaderInit* class, so a second load.php would fatal.
// class_exists() without the second argument triggers autoloading, letting
// a toolkit loaded by the official plugin (or vice versa) satisfy the check.
if ( ! class_exists( 'WordPress\XML\XMLProcessor' ) ) {
	$hub_importer_toolkit = $hub_importer_vendor_dir . '/php-toolkit/load.php';
	if ( file_exists( $hub_importer_toolkit ) ) {
		require_once $hub_importer_toolkit;
	}
}

// WXR parsers.
if ( ! class_exists( 'WXR_Parser', false ) ) {
	$hub_importer_parsers = array(
		'parsers/class-wxr-parser.php',
		'parsers/class-wxr-parser-simplexml.php',
		'parsers/class-wxr-parser-xml.php',
		'parsers/class-wxr-parser-regex.php',
		'parsers/class-wxr-parser-xml-processor.php',
	);
	foreach ( $hub_importer_parsers as $hub_importer_parser ) {
		$hub_importer_parser_file = $hub_importer_vendor_dir . '/' . $hub_importer_parser;
		if ( file_exists( $hub_importer_parser_file ) ) {
			require_once $hub_importer_parser_file;
		}
	}
}

// Upstream importer class (skipped when the official plugin already loaded it).
if ( ! class_exists( 'WP_Import', false ) ) {
	$hub_importer_class = $hub_importer_vendor_dir . '/class-wp-import.php';
	if ( file_exists( $hub_importer_class ) ) {
		require_once $hub_importer_class;
	}
}

// Hub subclass with Liquid mega-menu meta support. Guarded so a missing or
// corrupt vendor copy fails gracefully in LiquidImporter::set_demo_data()
// ("Error on import") instead of fataling on "Class WP_Import not found".
if ( class_exists( 'WP_Import', false ) ) {
	$hub_import_class = dirname( __FILE__ ) . '/class-hub-import.php';
	if ( file_exists( $hub_import_class ) ) {
		require_once $hub_import_class;
	}
}

unset( $hub_importer_vendor_dir, $hub_importer_compat, $hub_importer_toolkit, $hub_importer_parsers, $hub_importer_parser, $hub_importer_parser_file, $hub_importer_class, $hub_import_class );
