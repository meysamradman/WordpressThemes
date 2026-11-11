<?php

namespace RadmanCT\Integrations\Elementor\LocalAssets;

defined( 'ABSPATH' ) || exit;

function sources(): array {
	$root = get_template_directory();
	if ( ! is_file( $root . '/vendor/ace/ace.js' ) || ! is_file( $root . '/vendor/ace/ext-language_tools.js' ) ) { return array(); }
	$url = get_template_directory_uri() . '/vendor/ace/';
	return array( 'ace' => $url . 'ace.js', 'ace-language-tools' => $url . 'ext-language_tools.js' );
}

function scripts( $assets ): void {
	$registered = wp_scripts()->registered['ace'] ?? null;
	if ( ! $registered || '1.43.2' !== (string) $registered->ver || ! is_callable( array( $assets, 'append' ) ) ) { return; }
	foreach ( sources() as $handle => $source ) { $assets->append( $handle, $source, 'ace' === $handle ? array() : array( 'ace' ), '1.43.2' ); }
}

function source( string $url, string $handle ): string {
	if ( ! str_contains( $url, 'cdn.jsdelivr.net/npm/ace-builds@1.43.2/' ) ) { return $url; }
	return sources()[ $handle ] ?? $url;
}

function styles(): void {
	$editor = wp_styles()->registered['elementor-editor'] ?? null;
	if ( $editor ) { $editor->deps = array_values( array_diff( $editor->deps, array( 'google-font-roboto' ) ) ); }
	wp_dequeue_style( 'google-font-roboto' );
}

add_action( 'elementor/assets-manager/register_scripts', __NAMESPACE__ . '\\scripts', 1 );
add_filter( 'script_loader_src', __NAMESPACE__ . '\\source', 20, 2 );
add_action( 'elementor/editor/after_enqueue_styles', __NAMESPACE__ . '\\styles', 100 );
