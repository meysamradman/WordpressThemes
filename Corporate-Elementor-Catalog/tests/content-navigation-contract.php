<?php

namespace RadmanCTContent\Modules {
	function enabled( $module ) { return $GLOBALS['modules'][ $module ] ?? false; }
}
namespace {
	define( 'ABSPATH', '/' );
	$modules = array();
	$capabilities = array();
	$archives = 0;
	function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	function add_action( ...$args ) {}
	function add_filter( ...$args ) {}
	function current_user_can( $cap ) { return in_array( $cap, $GLOBALS['capabilities'], true ); }
	function admin_url( $path ) { return '/wp-admin/' . $path; }
	function post_type_exists( $type ) { return true; }
	function get_post_type_archive_link( $type ) { ++$GLOBALS['archives']; return '/' . $type . '/'; }
	require __DIR__ . '/../radmanct/inc/admin/settings-page.php';
	$links = \RadmanCT\Admin\SettingsPage\content_links();
	expect( '' === $links['core'] && ! $links['items'] && ! $links['archives'] && 0 === $archives, 'Disabled modules or unauthorized Core management exposed links.' );
	$capabilities = array( 'manage_options', 'edit_posts' );
	$modules['catalog'] = true;
	$links = \RadmanCT\Admin\SettingsPage\content_links();
	expect( '/wp-admin/admin.php?page=radmanct-content' === $links['core'] && isset( $links['items']['catalog'], $links['archives']['catalog'] ) && ! isset( $links['archives']['portfolio'] ), 'Module links crossed enabled module boundaries.' );
	$capabilities = array();
	$links = \RadmanCT\Admin\SettingsPage\content_links();
	expect( ! $links['items'] && isset( $links['archives']['catalog'] ), 'Edit capability was not respected independently of the public archive.' );
	echo "Passed: optional content module navigation, Core management capabilities and enabled archive links.\n";
}
