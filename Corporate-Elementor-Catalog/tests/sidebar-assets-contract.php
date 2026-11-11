<?php

namespace RadmanCT\Renderers {
	function context() { return array( 'blog', 'archive' ); }
	function uses_elementor() { return false; }
}
namespace RadmanCT\Settings {
	function values() { return array( 'blog_archive_sidebar_enabled' => true, 'blog_archive_sidebar_area' => 'test', 'blog_archive_sidebar_position' => 'end', 'blog_archive_sidebar_width' => 320 ); }
}
namespace RadmanCT\Sidebars {
	function scopes() { return array( 'blog_archive' ); }
}
namespace RadmanCT\Assets {
	function style( $handle, ...$args ) { $GLOBALS['styles'][] = $handle; }
}
namespace RadmanCTComponents\Components {
	function use_component( $name ) { $GLOBALS['styles'][] = $name; }
	function enqueue( $name ) { $GLOBALS['styles'][] = $name; }
}
namespace RadmanCTComponents\UI {
	function field_assets() { $GLOBALS['styles'][] = 'form-controls'; }
}
namespace {
	define( 'ABSPATH', '/' );
	function add_action( $name, $callback, ...$args ) { $GLOBALS['actions'][ $name ][] = $callback; }
	function add_filter( ...$args ) {}
	function is_admin() { return false; }
	function is_feed() { return false; }
	function is_active_sidebar( $id ) { return true; }
	function wp_get_sidebars_widgets() { return array( 'test' => $GLOBALS['widgets'] ); }
	function get_option( $key, $default = array() ) { return 'widget_categories' === $key ? array( 1 => array( 'dropdown' => false ) ) : $default; }
	function has_block( ...$args ) { return false; }
	$GLOBALS['wp_registered_sidebars'] = array( 'test' => array() );
	require __DIR__ . '/../radmanct/inc/sidebars.php';
	foreach ( array( array( 'search-1', 'categories-1' ), array( 'categories-1', 'search-1' ) ) as $widgets ) {
		$GLOBALS['widgets'] = $widgets; $GLOBALS['styles'] = array();
		foreach ( $GLOBALS['actions']['wp_enqueue_scripts'] as $callback ) { $callback(); }
		foreach ( array( 'form-controls', 'category-tree', 'archive-search' ) as $required ) {
			if ( ! in_array( $required, $GLOBALS['styles'], true ) ) { throw new \RuntimeException( 'Widget order skipped a required asset: ' . $required ); }
		}
	}
	echo "Passed: search/category widget order does not skip shared styles.\n";
}
