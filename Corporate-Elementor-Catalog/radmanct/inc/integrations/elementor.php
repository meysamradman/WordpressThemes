<?php

namespace RadmanCT\Integrations\Elementor;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/icons.php';
require_once dirname( __DIR__ ) . '/icons/elementor.php';

require_once __DIR__ . '/elementor/editor-media.php';
require_once __DIR__ . '/elementor/local-editor-assets.php';
require_once __DIR__ . '/elementor/custom-css.php';
require_once __DIR__ . '/elementor/atomic.php';
require_once __DIR__ . '/elementor/container-motion.php';
require_once __DIR__ . '/elementor/header-tone.php';
require_once __DIR__ . '/elementor/navigation.php';



function groups( array $groups ): array {
	return array( 'rdct-local' => __( 'Radman CT local fonts', 'radmanct' ) ) + $groups;
}

function fonts( array $fonts ): array {
	$result = array();
	$registry = \RadmanCT\Typography\registry();
	foreach ( \RadmanCT\Typography\used() as $key ) {
		$result[ $registry[ $key ]['family'] ] = 'rdct-local';
	}
	return $result;
}

function selectors( array $control ): array {
	if ( ! str_ends_with( $control['name'] ?? '', 'font_family' ) || ! is_array( $control['selectors'] ?? null ) ) {
		return $control;
	}
	foreach ( $control['selectors'] as $selector => $declarations ) {
		if ( is_string( $declarations ) ) {
			$control['selectors'][ $selector ] = preg_replace( '/font-family\s*:[^;]+;?/i', 'font-family:var(--rdct-elementor-font,var(--rdct-font-body)) !important;', $declarations );
		}
	}
	return $control;
}

function enqueue(): void {
	\RadmanCT\Assets\style( 'rdct-elementor', 'src/css/integrations/elementor.css', 'parent', array( 'rdct-theme' ) );
}

function pro_fonts( $manager ): void {
	if ( is_admin() || ! is_object( $manager ) ) {
		return;
	}
	foreach ( array( 'elementor/css-file/post/parse' => 'enqueue_fonts', 'elementor/css-file/global/parse' => 'enqueue_fonts', 'elementor/fonts/register_styles' => 'register_custom_font_styles', 'elementor/fonts/print_font_links/custom' => 'print_font_link', 'elementor/fonts/print_font_links/variable' => 'print_font_link' ) as $hook => $method ) {
		remove_action( $hook, array( $manager, $method ) );
	}
}

function existing_pro_fonts(): void {
	if ( ! is_admin() && class_exists( '\\ElementorPro\\Modules\\AssetsManager\\Module', false ) ) {
		$module = \ElementorPro\Modules\AssetsManager\Module::instance();
		if ( is_callable( array( $module, 'get_assets_manager' ) ) ) {
			pro_fonts( $module->get_assets_manager( 'font' ) );
		}
	}
}

function hints( array $urls, string $relation ): array {
	if ( ! in_array( $relation, array( 'dns-prefetch', 'preconnect' ), true ) ) {
		return $urls;
	}
	return array_values( array_filter( $urls, static function ( $entry ): bool {
		$url = is_array( $entry ) ? ( $entry['href'] ?? '' ) : $entry;
		if ( ! is_string( $url ) ) {
			return true;
		}
		$url = str_starts_with( $url, '//' ) ? 'https:' . $url : ( str_contains( $url, '://' ) ? $url : 'https://' . $url );
		$host = wp_parse_url( $url, PHP_URL_HOST );
		return ! in_array( strtolower( (string) $host ), array( 'fonts.googleapis.com', 'fonts.gstatic.com', 'use.typekit.net' ), true );
	} ) );
}

function templates( array $templates ): array {
	$templates['templates/elementor.php'] = __( 'Radman CT — Elementor with header and footer', 'radmanct' );
	return $templates;
}

add_filter( 'elementor/frontend/print_google_fonts', '__return_false', PHP_INT_MAX );
add_filter( 'elementor/fonts/groups', __NAMESPACE__ . '\\groups' );
add_filter( 'elementor/fonts/additional_fonts', __NAMESPACE__ . '\\fonts', PHP_INT_MAX );
add_filter( 'elementor/files/css/selectors', __NAMESPACE__ . '\\selectors', 20 );
add_action( 'elementor/frontend/after_enqueue_styles', __NAMESPACE__ . '\\enqueue', 20 );
add_action( 'elementor_pro/fonts_manager_loaded', __NAMESPACE__ . '\\pro_fonts' );
add_action( 'wp_loaded', __NAMESPACE__ . '\\existing_pro_fonts', 20 );
add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\hints', 20, 2 );
add_filter( 'theme_page_templates', __NAMESPACE__ . '\\templates' );

add_action( 'elementor/theme/register_locations', static function ( $manager ): void {
	foreach ( array( 'header', 'footer' ) as $location ) {
		$manager->register_location( $location, array( 'is_core' => false, 'public' => false, 'edit_in_content' => false, 'label' => 'header' === $location ? __( 'Header builder', 'radmanct' ) : __( 'Footer builder', 'radmanct' ) ) );
	}
	foreach ( array( 'single', 'archive' ) as $location ) {
		$manager->register_location( $location, array( 'is_core' => true, 'public' => true, 'overwrite' => false, 'label' => 'single' === $location ? __( 'Single page design', 'radmanct' ) : __( 'Archive design', 'radmanct' ) ) );
	}
} );
add_filter( 'elementor/theme/get_location_templates/template_id', static function ( $id, string $location ) {
	if ( is_admin() || ( current_user_can( 'edit_posts' ) && \RadmanCT\Modules\editor_preview() ) ) { return $id; }
	// The native Radman header/footer builder retains ownership of the site shell.
	return in_array( $location, array( 'header', 'footer' ), true ) ? -1 : $id;
}, 20, 2 );
