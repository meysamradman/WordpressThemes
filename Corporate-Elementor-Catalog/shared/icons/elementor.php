<?php

namespace RadmanCTShared\Icons\Elementor;

defined( 'ABSPATH' ) || exit;
if ( ! function_exists( __NAMESPACE__ . '\\tabs' ) ) {

function picker_data(): array {
	return function_exists( '\\RadmanCT\\Icons\\picker_data' ) ? \RadmanCT\Icons\picker_data() : \RadmanCTComponents\Icons\picker_data();
}

function tabs( array $tabs ): array {
	$icons = function_exists( '\\RadmanCT\\Icons\\labels' ) ? \RadmanCT\Icons\labels() : \RadmanCTComponents\Icons\labels();
	$names = array_values( array_filter( array_keys( $icons ), static fn( $name ) => is_string( $name ) && preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $name ) ) );
	$tabs['rdct-local'] = array( 'name' => 'rdct-local', 'label' => 'Radman CT', 'labelIcon' => 'eicon-star', 'prefix' => 'rdct-icon-', 'displayPrefix' => 'rdct-library', 'url' => false, 'icons' => $names, 'ver' => '1.0.0', 'render_callback' => __NAMESPACE__ . '\\render_icon' );
	$tabs['rdct-local']['fetchJson'] = add_query_arg( array( 'action' => 'rdct_icon_library', '_ajax_nonce' => wp_create_nonce( 'rdct_icon_library' ) ), admin_url( 'admin-ajax.php' ) );
	return $tabs;
}

function library_json(): void {
	if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error( null, 403 ); }
	if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) { wp_send_json_error( null, 405 ); }
	if ( ! is_string( $_REQUEST['_ajax_nonce'] ?? null ) || strlen( $_REQUEST['_ajax_nonce'] ) > 100 ) { wp_send_json_error( null, 403 ); }
	check_ajax_referer( 'rdct_icon_library' );
	$icons = function_exists( '\\RadmanCT\\Icons\\labels' ) ? \RadmanCT\Icons\labels() : \RadmanCTComponents\Icons\labels();
	$names = array_values( array_filter( array_keys( $icons ), static fn( $name ) => is_string( $name ) && preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $name ) ) );
	wp_send_json( array( 'icons' => $names ) );
}

function editor_styles(): void {
	if ( wp_style_is( 'rdct-elementor-icon-library', 'enqueued' ) ) { return; }
	$css = '.rdct-library{display:inline-flex;inline-size:1em;block-size:1em;}.rdct-library::before{content:"";display:block;inline-size:100%;block-size:100%;background:currentColor;mask:var(--rdct-library-icon) center/contain no-repeat;-webkit-mask:var(--rdct-library-icon) center/contain no-repeat;}';
	foreach ( picker_data() as $name => $icon ) { $css .= '.rdct-library.rdct-icon-' . $name . '{--rdct-library-icon:url("' . $icon['src'] . '");}'; }
	wp_register_style( 'rdct-elementor-icon-library', false, array(), null );
	wp_enqueue_style( 'rdct-elementor-icon-library' );
	wp_add_inline_style( 'rdct-elementor-icon-library', $css );
}

function render_icon( array $icon, array $attributes = array(), string $tag = 'i' ): string {
	if ( 'rdct-local' !== ( $icon['library'] ?? '' ) || ! is_string( $icon['value'] ?? null ) || ! preg_match( '/^rdct-library rdct-icon-([a-z][a-z0-9-]{0,70})$/D', $icon['value'], $match ) || ! function_exists( 'rdct_icon' ) ) { return ''; }
	ob_start();
	try { rdct_icon( $match[1] ); $html = ob_get_contents(); }
	finally { ob_end_clean(); }
	if ( ! class_exists( '\\WP_HTML_Tag_Processor' ) ) { return $html; }
	$processor = new \WP_HTML_Tag_Processor( $html );
	if ( ! $processor->next_tag( array( 'tag_name' => 'SVG' ) ) ) { return $html; }
	$fill = $processor->get_attribute( 'fill' );
	if ( is_string( $fill ) && preg_match( '/^(?:none|currentColor|#[a-fA-F0-9]{3,8})$/D', $fill ) ) { $processor->set_attribute( 'style', 'fill:' . $fill . ';' ); }
	foreach ( $attributes as $name => $value ) {
		if ( ! is_string( $name ) || ! preg_match( '/^(?:class|id|role|title|tabindex|aria-[a-z-]+|data-[a-z0-9-]+)$/D', $name ) ) { continue; }
		if ( 'class' === $name ) {
			$value = is_array( $value ) ? implode( ' ', array_filter( $value, 'is_string' ) ) : $value;
			if ( is_string( $value ) ) { $value = $processor->get_attribute( 'class' ) . ' ' . $value; }
		}
		if ( is_string( $value ) || is_bool( $value ) ) { $processor->set_attribute( $name, $value ); }
	}
	return $processor->get_updated_html();
}

add_filter( 'elementor/icons_manager/additional_tabs', __NAMESPACE__ . '\\tabs' );
add_action( 'wp_ajax_rdct_icon_library', __NAMESPACE__ . '\\library_json' );
add_action( 'elementor/editor/after_enqueue_styles', __NAMESPACE__ . '\\editor_styles' );
add_action( 'elementor/preview/enqueue_styles', __NAMESPACE__ . '\\editor_styles' );
add_action( 'elementor/atomic-widgets/settings/transformers/register', static function ( $registry ): void {
	if ( ! class_exists( '\\Elementor\\Modules\\AtomicWidgets\\PropsResolver\\Transformers\\Icon_Transformer' ) ) { return; }
	require_once __DIR__ . '/elementor-transformer.php';
	$key = \Elementor\Modules\AtomicWidgets\PropTypes\Icon_Prop_Type::get_key();
	$original = $registry->get( $key );
	if ( $original instanceof \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base && ! $original instanceof Icon_Transformer ) { $registry->register( $key, new Icon_Transformer( $original ) ); }
}, 30 );
}
