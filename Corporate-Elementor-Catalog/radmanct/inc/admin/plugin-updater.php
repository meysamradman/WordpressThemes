<?php

namespace RadmanCT\Admin\PluginUpdater;

defined( 'ABSPATH' ) || exit;

function bundles(): array {
	return \RadmanCT\Admin\PluginInstaller\bundles();
}

function token( string $id ): string { return 'rdct-bundled-' . bundles()[ $id ]['slug']; }

function operations(): \WeakMap {
	static $operations;
	return $operations ??= new \WeakMap();
}

function updates( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}
	$transient = clone $transient;
	foreach ( bundles() as $id => $bundle ) {
		$file = $bundle['file'];
		unset( $transient->response[ $file ], $transient->no_update[ $file ] );
		$package = \RadmanCT\Admin\PluginInstaller\bundle_metadata( $id );
		$installed = is_readable( WP_PLUGIN_DIR . '/' . $file ) ? get_file_data( WP_PLUGIN_DIR . '/' . $file, array( 'Version' => 'Version' ) ) : array();
		if ( ! $package || empty( $installed['Version'] ) || ! version_compare( $package['version'], $installed['Version'], '>' ) ) { continue; }
		$transient->response[ $file ] = (object) array(
			'id' => $file, 'slug' => $bundle['slug'], 'plugin' => $file,
			'new_version' => $package['version'], 'package' => token( $id ),
			'url' => '', 'requires' => $bundle['requires'], 'requires_php' => '8.0',
			'autoupdate' => false, 'icons' => array(), 'banners' => array(),
		);
	}
	return $transient;
}

function download( $reply, string $package, $upgrader, array $extra ) {
	if ( false !== $reply ) { return $reply; }
	$id = null;
	$install = 'plugin' === ( $extra['type'] ?? '' ) && 'install' === ( $extra['action'] ?? '' );
	foreach ( bundles() as $key => $candidate ) {
		$path = get_template_directory() . '/bundled-plugins/' . $candidate['slug'] . '.zip';
		if ( ( ! $install && token( $key ) === $package ) || ( $install && $path === $package ) ) { $id = $key; break; }
	}
	if ( null === $id ) {
		return $reply;
	}
	$bundle = bundles()[ $id ];
	unset( operations()[ $upgrader ] );
	if ( ( ! $install && $bundle['file'] !== ( $extra['plugin'] ?? '' ) ) || ! current_user_can( $install ? 'install_plugins' : 'update_plugins' ) ) {
		return new \WP_Error( 'radman_update_denied', __( 'You are not allowed to install or update this plugin.', 'radmanct' ) );
	}
	$local = \RadmanCT\Admin\PluginInstaller\bundle_package( $id );
	$installed = is_readable( WP_PLUGIN_DIR . '/' . $bundle['file'] ) ? get_file_data( WP_PLUGIN_DIR . '/' . $bundle['file'], array( 'Version' => 'Version' ) ) : array();
	if ( ! $local || ( ! $install && ( empty( $installed['Version'] ) || ! version_compare( $local['version'], $installed['Version'], '>' ) ) ) ) {
		return new \WP_Error( 'radman_package_invalid', __( 'The local plugin package is unavailable or invalid. Existing plugin files were preserved.', 'radmanct' ) );
	}
	$temporary = wp_tempnam( $bundle['slug'] . '.zip' );
	if ( ! $temporary || ! copy( $local['path'], $temporary ) || ! hash_equals( $local['sha256'], hash_file( 'sha256', $temporary ) ) ) {
		if ( $temporary ) { wp_delete_file( $temporary ); }
		return new \WP_Error( 'radman_package_copy', __( 'The local plugin package could not be prepared.', 'radmanct' ) );
	}
	operations()[ $upgrader ] = array( 'id' => $id, 'version' => $local['version'], 'action' => $install ? 'install' : 'update' );
	return $temporary;
}

function source( $source, string $remote, $upgrader, array $extra ) {
	$operation = operations()[ $upgrader ] ?? null;
	if ( ! $operation ) { return $source; }
	unset( operations()[ $upgrader ] );
	if ( is_wp_error( $source ) ) { return $source; }
	$bundle = bundles()[ $operation['id'] ];
	$install = 'install' === ( $operation['action'] ?? 'update' );
	if ( $install ? ( 'plugin' !== ( $extra['type'] ?? '' ) || 'install' !== ( $extra['action'] ?? '' ) ) : $bundle['file'] !== ( $extra['plugin'] ?? '' ) ) {
		return new \WP_Error( 'radman_package_invalid', __( 'The local plugin package is unavailable or invalid. Existing plugin files were preserved.', 'radmanct' ) );
	}
	global $wp_filesystem;
	$header = $wp_filesystem->get_contents( $source . basename( $bundle['file'] ) );
	if ( $bundle['slug'] !== basename( rtrim( $source, '/\\' ) ) || ! is_string( $header ) || ! preg_match( '/^[ \t\/*#@]*Version:[ \t]*([^\r\n]+)/mi', substr( $header, 0, 8192 ), $matches ) || trim( $matches[1] ) !== $operation['version'] ) {
		return new \WP_Error( 'radman_package_version', __( 'The plugin package version does not match its manifest. The installed plugin has been preserved.', 'radmanct' ) );
	}
	return $source;
}

function automatic( $update, $item ) {
	return in_array( $item->plugin ?? '', array_column( bundles(), 'file' ), true ) ? false : $update;
}

add_filter( 'site_transient_update_plugins', __NAMESPACE__ . '\\updates' );
add_filter( 'pre_set_site_transient_update_plugins', __NAMESPACE__ . '\\updates' );
add_filter( 'upgrader_pre_download', __NAMESPACE__ . '\\download', 10, 4 );
add_filter( 'upgrader_source_selection', __NAMESPACE__ . '\\source', 10, 4 );
add_filter( 'auto_update_plugin', __NAMESPACE__ . '\\automatic', 10, 2 );
add_action( 'switch_theme', 'wp_clean_plugins_cache', 10, 0 );
