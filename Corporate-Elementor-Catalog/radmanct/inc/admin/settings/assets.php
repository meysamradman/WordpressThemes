<?php

namespace RadmanCT\Admin\SettingsPage;

defined( 'ABSPATH' ) || exit;

function enqueue( string $hook ): void {
	if ( 'toplevel_page_rdct-settings' !== $hook || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$asset = \RadmanCT\Assets\entry( 'src/js/admin/settings.js' );
	if ( ! $asset ) {
		return;
	}
	\RadmanCT\Assets\style( 'rdct-admin-settings', 'src/css/admin/settings.css' );
	wp_enqueue_media();
	$code_editor = array();
	if ( \RadmanCT\CustomCode\can_edit() ) {
		foreach ( array( 'css' => 'text/css', 'js' => 'application/javascript' ) as $key => $type ) { $code_editor[ $key ] = wp_enqueue_code_editor( array( 'type' => $type, 'codemirror' => array( 'direction' => 'ltr', 'lineNumbers' => true ) ) ); }
	}
	$dependencies = array( 'wp-element' );
	if ( array_filter( $code_editor ) ) { $dependencies[] = 'code-editor'; }
	wp_enqueue_script( 'rdct-admin-settings', \RadmanCT\Assets\url( $asset['file'] ), $dependencies, null, true );
	$samples_path = get_template_directory() . '/languages/font-samples.json';
	$samples = is_readable( $samples_path ) ? json_decode( file_get_contents( $samples_path ), true ) : array();
	require_once dirname( __DIR__ ) . '/builder/labels.php';
	$builder_languages = array( array( 'id' => 'all', 'title' => __( 'All languages', 'radmanct' ) ) );
	if ( function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\languages' ) ) {
		foreach ( \RadmanCT\Integrations\Wpml\Builder\languages() as $code => $title ) { $builder_languages[] = array( 'id' => $code, 'title' => $title ); }
	}
	$builder_language = apply_filters( 'wpml_current_language', null );
	$builder_language = in_array( $builder_language, array_column( $builder_languages, 'id' ), true ) ? $builder_language : 'all';
	$builder_defaults = \RadmanCT\Admin\BuilderApi\defaults();
	$config = array(
		'direction' => is_rtl() ? 'rtl' : 'ltr',
		'primaryFontGroup' => \RadmanCT\Fonts\primary_group( \RadmanCT\Settings\values() ),
		'multilingualFonts' => count( (array) apply_filters( 'wpml_active_languages', array(), array( 'skip_missing' => 0 ) ) ) > 1,
		'canEditCode' => \RadmanCT\CustomCode\can_edit(), 'codeEditor' => $code_editor,
		'values' => \RadmanCT\Settings\values(),
		'save' => array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'radman_settings_save' ) ),
		'layoutFields' => \RadmanCT\Layout\fields(),
		'contentColorFields' => \RadmanCT\ContentColors\fields(),
		'widgetAreas' => widget_areas(),
		'widgetsUrl' => admin_url( 'widgets.php' ),
		'defaults' => \RadmanCT\Settings\defaults(),
		'labels' => labels(),
		'builder' => array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'radman_builder' ), 'labels' => \RadmanCT\Admin\BuilderPage\labels(), 'languages' => $builder_languages, 'activeLanguage' => $builder_language, 'defaults' => $builder_defaults, 'editorUrls' => array( 'header' => admin_url( 'admin.php?page=rdct-header' ), 'footer' => admin_url( 'admin.php?page=rdct-footer' ) ) ),
		'version' => wp_get_theme( 'radmanct' )->get( 'Version' ),
		'logo' => get_template_directory_uri() . '/images/radman_logo.svg',
		'siteTitle' => get_bloginfo( 'name' ),
		'siteLogo' => wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'medium' ) ?: '',
		'identityUrl' => add_query_arg( 'autofocus[section]', 'title_tagline', admin_url( 'customize.php' ) ),
		'fontBase' => get_template_directory_uri() . '/fonts/',
		'fonts' => \RadmanCT\Typography\registry(),
		'fontWeights' => array_map( '\\RadmanCT\\Fonts\\weights', array_combine( array_keys( \RadmanCT\Fonts\registry() ), array_keys( \RadmanCT\Fonts\registry() ) ) ),
		'fontRoles' => \RadmanCT\Fonts\roles(),
		'fontChoices' => array( 'persian' => \RadmanCT\Fonts\choices( 'persian' ), 'latin' => \RadmanCT\Fonts\choices( 'latin' ) ),
		'elementorPro' => function_exists( 'elementor_theme_do_location' ),
		'elementor' => did_action( 'elementor/loaded' ) > 0,
		'elementorAtomic' => function_exists( '\\RadmanCT\\Integrations\\Elementor\\Atomic\\enabled' ) && \RadmanCT\Integrations\Elementor\Atomic\enabled(),
		'pagesUrl' => current_user_can( 'edit_pages' ) ? admin_url( 'edit.php?post_type=page' ) : '',
		'contentModules' => array( 'blog' => true, 'catalog' => function_exists( '\\RadmanCTContent\\Modules\\enabled' ) && \RadmanCTContent\Modules\enabled( 'catalog' ), 'portfolio' => function_exists( '\\RadmanCTContent\\Modules\\enabled' ) && \RadmanCTContent\Modules\enabled( 'portfolio' ) ),
		'contentLinks' => content_links(),
		'samples' => is_array( $samples ) ? $samples : array(),
	);
	$config['builder']['selections'] = array( 'header' => \RadmanCT\Builder\selection( 'header' ), 'footer' => \RadmanCT\Builder\selection( 'footer' ) );
	wp_add_inline_script( 'rdct-admin-settings', 'window.rdctSettings=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}
function widget_areas(): array {
	global $wp_registered_sidebars;
	$result = array();
	foreach ( (array) $wp_registered_sidebars as $id => $area ) { $result[] = array( (string) $id, wp_strip_all_tags( $area['name'] ) ); }
	return $result;
}

function content_links(): array {
	$core = function_exists( '\\RadmanCTContent\\Modules\\enabled' );
	$result = array( 'core' => $core && current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=radmanct-content' ) : '', 'requirements' => admin_url( 'admin.php?page=rdct-requirements' ), 'items' => array(), 'archives' => array() );
	foreach ( array( 'catalog' => 'radman_product', 'portfolio' => 'radman_portfolio' ) as $module => $type ) {
		if ( $core && \RadmanCTContent\Modules\enabled( $module ) && post_type_exists( $type ) ) {
			$result['archives'][ $module ] = get_post_type_archive_link( $type ) ?: '';
			if ( current_user_can( 'edit_posts' ) ) { $result['items'][ $module ] = admin_url( 'edit.php?post_type=' . $type ); }
		}
	}
	return $result;
}
function script_attributes( array $attributes ): array {
	if ( 'rdct-admin-settings-js' === ( $attributes['id'] ?? '' ) ) {
		$attributes['type'] = 'module';
	}
	return $attributes;
}
