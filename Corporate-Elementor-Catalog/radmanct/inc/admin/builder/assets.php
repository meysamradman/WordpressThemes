<?php

namespace RadmanCT\Admin\BuilderPage;

defined( 'ABSPATH' ) || exit;

function enqueue( string $hook ): void {
	if ( ! in_array( $hook, screens(), true ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( ! editing() ) {
		$asset = \RadmanCT\Assets\entry( 'src/js/admin/builder-list.js' );
		if ( ! $asset ) { return; }
		wp_enqueue_script( 'rdct-admin-builder-list', \RadmanCT\Assets\url( $asset['file'] ), array(), null, true );
		$config = array( 'kind' => kind(), 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'radman_builder' ), 'labels' => array(
			'confirmDelete' => __( 'Delete this saved layout?', 'radmanct' ),
			'deleted' => __( 'Layout deleted.', 'radmanct' ),
			'requestError' => __( 'The request failed. Reload the page and try again.', 'radmanct' ),
		) );
		wp_add_inline_script( 'rdct-admin-builder-list', 'window.rdctBuilderList=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
		return;
	}
	$asset = \RadmanCT\Assets\entry( 'src/js/admin/builder.js' );
	if ( ! $asset ) {
		return;
	}
	\RadmanCT\Assets\style( 'rdct-admin-builder', 'src/css/admin/builder.css' );
	wp_enqueue_media();
	require_once dirname( __DIR__, 2 ) . '/navigation/presentation.php';
	wp_enqueue_script( 'rdct-admin-builder', \RadmanCT\Assets\url( $asset['file'] ), array( 'wp-element' ), null, true );
	$languages = array( array( 'id' => 'all', 'title' => __( 'All languages', 'radmanct' ) ) );
	if ( function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\languages' ) ) {
		foreach ( \RadmanCT\Integrations\Wpml\Builder\languages() as $code => $title ) { $languages[] = array( 'id' => $code, 'title' => $title ); }
	} else {
		foreach ( (array) apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) ) as $language ) {
			if ( is_array( $language ) && is_string( $language['code'] ?? null ) && preg_match( '/^[a-zA-Z][a-zA-Z0-9_-]{0,19}$/D', $language['code'] ) ) {
				$languages[] = array( 'id' => $language['code'], 'title' => $language['native_name'] ?? $language['code'] );
			}
		}
	}
	$menus = array();
	foreach ( wp_get_nav_menus() as $menu ) {
		$menus[] = array( 'id' => (int) $menu->term_id, 'title' => $menu->name );
	}
	$config = array( 'settingsUrl' => admin_url( 'admin.php?page=rdct-settings#rdct-header_footer' ), 'kind' => kind(), 'id' => selected_id(), 'defaults' => \RadmanCT\Admin\BuilderApi\defaults()[ kind() ] ?? array(), 'creating' => creating(), 'data' => creating() ? draft_data() : \RadmanCT\Builder\read( kind(), selected_id() ), 'layouts' => \RadmanCT\Builder\Storage\listing( kind() ), 'labels' => labels(), 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'radman_builder' ), 'languages' => $languages, 'menus' => $menus, 'navigationPanels' => editor_panel_fields( \RadmanCT\Navigation\Presentation\fields() + \RadmanCT\Navigation\Presentation\submenu_fields() ), 'menuSkins' => \RadmanCT\Navigation\Presentation\skins(), 'menuHoverFields' => \RadmanCT\Navigation\Hover\fields(), 'direction' => is_rtl() ? 'rtl' : 'ltr', 'origin' => home_url( '/' ) );
	require_once dirname( __DIR__, 2 ) . '/icons.php';
	require_once dirname( __DIR__, 2 ) . '/navigation/panel.php';
	require_once dirname( __DIR__, 2 ) . '/navigation/templates.php';
	$config['menusUrl'] = admin_url( 'nav-menus.php' );
	$config['panelFields'] = editor_panel_fields( \RadmanCT\Navigation\Panel\fields() );
	$config['schemeFields'] = \RadmanCT\ColorScheme\toggle_fields();
	$config['panelTemplates'] = array_map( static fn( $id, $title ) => array( 'id' => (int) $id, 'title' => $title ), array_keys( \RadmanCT\Navigation\Templates\options() ), array_values( \RadmanCT\Navigation\Templates\options() ) );
	$config['elementTypes'] = array_values( array_diff( \RadmanCT\Builder\types(), array( 'block' ) ) );
	$config['translation'] = ! creating() && function_exists( '\\RadmanCT\\Integrations\\Wpml\\Builder\\translation_state' ) ? \RadmanCT\Integrations\Wpml\Builder\translation_state( kind(), selected_id() ) : null;
	$config['focusTitle'] = creating() || isset( $_GET['rename'] );
	$config['copySource'] = copy_source();
	$config['libraryUrl'] = admin_url( 'admin.php?page=rdct-' . kind() );
	$config['initialSettingsTab'] = ! creating() && isset( $_GET['translations'] ) ? 'translations' : null;
	require_once dirname( __DIR__, 2 ) . '/builder/button-component.php';
	$config['buttonFields'] = \RadmanCT\Builder\ButtonComponent\fields();
	$config['buttonGroups'] = \RadmanCT\Builder\ButtonComponent\available() ? \RadmanCTComponents\Components\Button\groups() : array();
	$config['buttonResponsive'] = \RadmanCT\Builder\ButtonComponent\available() ? \RadmanCTComponents\Components\Button\presentation() : array();
	$config['iconChoices'] = \RadmanCT\Icons\picker_data();
	wp_add_inline_script( 'rdct-admin-builder', 'window.rdctBuilder=' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}
function editor_panel_fields( array $fields ): array {
    foreach ( $fields as $key => &$field ) {
        if ( 'blocks' === ( $field['choices'] ?? null ) ) { unset( $fields[ $key ] ); continue; }
        if ( 'panelContentType' === $key ) { unset( $field['options']['block'] ); }
    }
    unset( $field );
    return $fields;
}
function script_attributes( array $attributes ): array {
	if ( in_array( $attributes['id'] ?? '', array( 'rdct-admin-builder-js', 'rdct-admin-builder-list-js' ), true ) ) {
		$attributes['type'] = 'module';
	}
	return $attributes;
}
