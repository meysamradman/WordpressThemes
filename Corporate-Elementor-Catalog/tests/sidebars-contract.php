<?php

namespace RadmanCT\Settings {
	function values(): array { return $GLOBALS['settings']; }
}
namespace RadmanCT\Renderers {
	function context(): array { return $GLOBALS['context']; }
	function uses_elementor(): bool { return $GLOBALS['elementor']; }
}
namespace RadmanCTContent\Modules {
	function enabled( string $module ): bool { return $GLOBALS['modules'][ $module ] ?? false; }
}
namespace RadmanCT\Assets {
	function style( ...$args ): void { $GLOBALS['styles'][] = $args; }
}
namespace {
	define( 'ABSPATH', '/' );
	$hooks = $wp_registered_sidebars = $styles = $active = $modules = array();
	$context = array(); $elementor = false; $settings = array();
	function add_action( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ][] = $callback; }
	function apply_filters( $hook, $value, ...$args ) { return $value; }
	function __( $value, $domain ) { return $value; }
	function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
	function is_admin() { return false; }
	function is_feed() { return false; }
	function is_active_sidebar( $area ) { return in_array( $area, $GLOBALS['active'], true ); }
	function register_sidebar( $area ) { $GLOBALS['wp_registered_sidebars'][ $area['id'] ] = $area; }
	function get_template_part( $partial, $name, $args ) { $GLOBALS['parts'][] = array( $partial, $args ); echo '<article></article>'; }
	function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	require __DIR__ . '/../radmanct/inc/sidebar-settings.php';
	require __DIR__ . '/../radmanct/inc/sidebars.php';
	\RadmanCT\Sidebars\register();
	expect( 2 === count( $wp_registered_sidebars ), 'Disabled modules registered widget areas.' );
	$modules = array( 'catalog' => true, 'portfolio' => true ); \RadmanCT\Sidebars\register();
	expect( 6 === count( $wp_registered_sidebars ), 'Six independent widget areas were not registered.' );
	$settings = \RadmanCT\Sidebars\defaults();
	foreach ( \RadmanCT\Sidebars\scopes() as $scope ) {
		$context = explode( '_', $scope );
		expect( ! \RadmanCT\Sidebars\current(), 'Sidebar must default to disabled.' );
		$settings[ $scope . '_sidebar_enabled' ] = true;
		expect( ! \RadmanCT\Sidebars\current(), 'Empty widget area created a sidebar.' );
		$active = array( $settings[ $scope . '_sidebar_area' ] );
		expect( $scope === \RadmanCT\Sidebars\current()['scope'], 'Sidebar leaked across views or content types.' );
		$parts = array(); ob_start(); \RadmanCT\Sidebars\render( 'custom/partial', array( 'catalog' => true ) ); $html = ob_get_clean();
		expect( str_contains( $html, 'rdct-content-layout' ) && 2 === count( $parts ) && 'custom/partial' === $parts[0][0] && true === $parts[0][1]['catalog'], 'Shared wrapper lost the existing design or arguments.' );
		$before = count( $styles ); $hooks['wp_enqueue_scripts'][0]();
		expect( $before + 1 === count( $styles ), 'Active sidebar stylesheet was not enqueued.' );
		$elementor = true; expect( ! \RadmanCT\Sidebars\current(), 'Theme sidebar entered an Elementor-owned view.' ); $elementor = false;
		$active = array(); $before = count( $styles ); $hooks['wp_enqueue_scripts'][0](); expect( $before === count( $styles ), 'Empty sidebar enqueued assets.' );
	}
	$parts = array(); ob_start(); \RadmanCT\Sidebars\render( 'custom/partial' ); $html = ob_get_clean();
	expect( '<article></article>' === $html && 1 === count( $parts ), 'Disabled/empty sidebar added a wrapper.' );
	foreach ( array( '../sidebar', '<script>', array(), '', str_repeat( 'a', 81 ) ) as $value ) {
		$valid = true; \RadmanCT\Sidebars\normalize( 'blog_archive_sidebar_area', $value, $valid ); expect( ! $valid, 'Unsafe area identifier accepted.' );
	}
	foreach ( array( 239, 481, '320px', '300;display:none', array() ) as $value ) {
		$valid = true; \RadmanCT\Sidebars\normalize( 'blog_archive_sidebar_width', $value, $valid ); expect( ! $valid, 'Unsafe sidebar width accepted.' );
	}
	$valid = false; expect( 360 === \RadmanCT\Sidebars\normalize( 'blog_archive_sidebar_width', '360', $valid ) && $valid, 'Width was not normalized.' );
	$valid = true; \RadmanCT\Sidebars\normalize( 'blog_archive_sidebar_enabled', 'false', $valid ); expect( ! $valid, 'Ambiguous boolean accepted.' );
	$settings['portfolio_single_sidebar_area'] = 'unregistered'; $active = array( 'unregistered' ); expect( ! \RadmanCT\Sidebars\current(), 'Unregistered area was rendered.' );
	echo "Passed: six independent native widget areas, empty/disabled/Elementor boundaries, conditional assets and bounded settings.\n";
}
