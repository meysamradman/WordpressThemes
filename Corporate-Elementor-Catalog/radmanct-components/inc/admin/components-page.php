<?php

namespace RadmanCTComponents\Admin\ComponentsPage;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/components/library.php';

function allowed(): bool {
	return current_user_can( 'edit_posts' ) || current_user_can( 'edit_theme_options' );
}

function screen( ?string $value = null ): string {
	static $hook = '';
	if ( null !== $value ) { $hook = $value; }
	return $hook;
}

function menu(): void {
	$parent = function_exists( '\\RadmanCT\\Settings\\tokens' ) ? 'rdct-settings' : 'tools.php';
	screen( add_submenu_page( $parent, __( 'Components', 'radmanct-components' ), __( 'Components', 'radmanct-components' ), 'edit_theme_options', 'rdct-components', __NAMESPACE__ . '\\render' ) );
}

function assets( bool $editor = false ): void {
	$handle = $editor ? 'rdct-elementor-library' : 'rdct-admin-components';
	\RadmanCTComponents\Assets\style( $handle, $editor ? 'src/css/admin/elementor-library.css' : 'src/css/admin/components.css' );
	if ( ! $editor ) { wp_add_inline_style( $handle, apply_filters( 'radmanct_components/admin_css', '' ) ); }
	$asset = \RadmanCTComponents\Assets\entry( $editor ? 'src/js/admin/component-editor.js' : 'src/js/admin/components.js' );
	if ( $asset ) {
		wp_enqueue_script( 'rdct-component-library', \RadmanCTComponents\Assets\url( $asset['file'] ), $editor ? array( 'elementor-editor' ) : array(), null, true );
	}
}

function preview_url( string $id ): string {
	return add_query_arg( array( 'action' => 'radman_component_preview', 'sample' => $id, '_wpnonce' => wp_create_nonce( 'radman_component_preview' ) ), admin_url( 'admin-post.php' ) );
}

function library( bool $editor = false ): void {
	require_once __DIR__ . '/library-gallery.php';
	gallery( $editor );
}

function render(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
	echo '<div class="wrap"><h1>' . esc_html__( 'Component library', 'radmanct-components' ) . '</h1>';
	library();
	echo '</div>';
}

function editor(): void {
	if ( ! allowed() ) { return; }
	echo '<button type="button" class="rdct-lib-launch" data-library-open data-library-close-label="' . esc_attr__( 'Close', 'radmanct-components' ) . '" data-library-url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-library-nonce="' . esc_attr( wp_create_nonce( 'radman_component_library' ) ) . '" data-library-loading="' . esc_attr__( 'Loading library...', 'radmanct-components' ) . '" data-library-error="' . esc_attr__( 'Could not load the library. Try again.', 'radmanct-components' ) . '" hidden>' . esc_html__( 'Radman CT component library', 'radmanct-components' ) . '</button>';
}

function editor_library(): void {
	if ( ! allowed() ) { wp_send_json_error( null, 403 ); }
	check_ajax_referer( 'radman_component_library' );
	$id = isset( $_POST['post_id'] ) && is_scalar( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $id || ! current_user_can( 'edit_post', $id ) || ! did_action( 'elementor/loaded' ) ) { wp_send_json_error( null, 403 ); }
	$view = $_POST['view'] ?? 'catalog';
	if ( 'model' === $view ) {
		$sample = $_POST['sample'] ?? null; $language = $_POST['language'] ?? null; $mode = $_POST['mode'] ?? null;
		if ( ! is_string( $sample ) || strlen( $sample ) > 80 || ! isset( \RadmanCTComponents\Components\Library\catalog()[ $sample ] ) || ! in_array( $language, array( 'fa', 'en' ), true ) || ! in_array( $mode, array( 'classic', 'atomic' ), true ) ) { wp_send_json_error( null, 400 ); }
		if ( 'atomic' === $mode ) { \Elementor\Plugin::$instance->widgets_manager->get_widget_types(); }
		$model = \RadmanCTComponents\Components\Library\model( $sample, $language, $mode );
		if ( ! $model ) { wp_send_json_error( null, 400 ); }
		wp_send_json_success( array( 'model' => $model ) );
	}
	if ( 'catalog' !== $view ) { wp_send_json_error( null, 400 ); }
	ob_start();
	library( true );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_radman_component_library', __NAMESPACE__ . '\\editor_library' );

function preview(): void {
	if ( ! allowed() ) { wp_die( '', '', array( 'response' => 403 ) ); }
	check_admin_referer( 'radman_component_preview' );
	$id = isset( $_GET['sample'] ) && is_string( $_GET['sample'] ) ? sanitize_key( wp_unslash( $_GET['sample'] ) ) : '';
	$item = \RadmanCTComponents\Components\Library\catalog()[ $id ] ?? null;
	if ( ! $item ) { wp_die( '', '', array( 'response' => 404 ) ); }
	$language = 'en' === ( $_GET['language'] ?? '' ) ? 'en' : 'fa';
	nocache_headers();
	header( 'Content-Type: text/html; charset=UTF-8' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'X-Robots-Tag: noindex, nofollow' );
	header( "Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data:; connect-src 'none'; frame-ancestors 'self'; base-uri 'none'; form-action 'none'" );
	require_once dirname( __DIR__ ) . '/components/library-preview.php';
	\RadmanCTComponents\Components\Library\preview_document( $id, $language );
	exit;
}

add_action( 'admin_menu', __NAMESPACE__ . '\\menu', 20 );
add_action( 'admin_enqueue_scripts', static function ( string $hook ): void {
	if ( '' !== screen() && screen() === $hook && current_user_can( 'edit_theme_options' ) ) { assets(); }
} );
add_action( 'admin_post_radman_component_preview', __NAMESPACE__ . '\\preview' );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( 'rdct-component-library-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; }
	return $attributes;
} );
