<?php

namespace RadmanCT\Admin\PluginInstaller;

defined( 'ABSPATH' ) || exit;

function bundles(): array {
	return array(
		'components' => array( 'slug' => 'radmanct-components', 'name' => __( 'Radman Components', 'radmanct' ), 'file' => 'radmanct-components/radmanct-components.php', 'required' => true, 'requires' => '6.6' ),
		'content' => array( 'slug' => 'radmanct-content', 'name' => __( 'Radman Content', 'radmanct' ), 'file' => 'radmanct-content/radmanct-content.php', 'requires' => '6.6' ),
		'mcp' => array( 'slug' => 'radmanct-mcp', 'name' => __( 'Radman MCP', 'radmanct' ), 'file' => 'radmanct-mcp/radmanct-mcp.php', 'requires' => '6.9' ),
	);
}

function bundle_metadata( string $id = 'content' ): ?array {
	$bundle = bundles()[ $id ] ?? null;
	if ( ! $bundle ) { return null; }
	$directory = get_template_directory() . '/bundled-plugins/';
	$manifest = $directory . $bundle['slug'] . '.json';
	$package = $directory . $bundle['slug'] . '.zip';
	if ( ! is_readable( $manifest ) || filesize( $manifest ) > 2048 || ! is_readable( $package ) || filesize( $package ) > 10485760 ) {
		return null;
	}
	$data = json_decode( file_get_contents( $manifest ), true );
	if ( ! is_array( $data ) || ! is_string( $data['version'] ?? null ) || ! preg_match( '/^[0-9A-Za-z.+-]{1,40}$/D', $data['version'] ) || ! is_string( $data['sha256'] ?? null ) || ! preg_match( '/^[a-f0-9]{64}$/D', $data['sha256'] ) ) {
		return null;
	}
	return array( 'path' => $package, 'version' => $data['version'], 'sha256' => $data['sha256'] );
}

function bundle_package( string $id = 'content' ): ?array {
	$package = bundle_metadata( $id );
	$hash = $package ? hash_file( 'sha256', $package['path'] ) : false;
	if ( ! $package || ! is_string( $hash ) || ! hash_equals( $package['sha256'], $hash ) ) {
		return null;
	}
	return $package;
}

