<?php

namespace Elementor\Modules\DynamicTags { class Module { const IMAGE_CATEGORY = 'image'; } }
namespace RadmanCTComponents\Icons { function catalog() { return array( 'phone' => array(), 'client-logo' => array() ); } }
namespace {
	define( 'ABSPATH', '/' );
	function __( $text, $domain ) { return $text; }
	function esc_html__( $text, $domain ) { return $text; }
	function esc_attr__( $text, $domain ) { return $text; }
	function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
	function do_action( ...$args ) {}
	function apply_filters( $hook, $value, ...$args ) { return $value; }
	function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
	$base = __DIR__ . '/../themes/elementor/';
	require $base . 'core/base/base-object.php';
	foreach ( array( 'base', 'base-data', 'base-multiple', 'icons' ) as $file ) { require $base . 'includes/controls/' . $file . '.php'; }
	require __DIR__ . '/../radmanct-components/inc/integrations/elementor/icon-picker-control.php';
	$control = new \RadmanCTComponents\Integrations\Elementor\IconPicker\IconControl();
	expect( $control instanceof \Elementor\Control_Icons && 'rdct-icon-picker' === $control->get_type(), 'Icon adapter lost its native parent or persisted type.' );
	foreach ( array( '', 'phone', 'removed-icon', 'client-logo' ) as $value ) {
		expect( $value === $control->get_value( array( 'name' => 'icon', 'default' => 'phone' ), array( 'icon' => $value ) ), 'Existing string icon or explicit clear was lost.' );
	}
	expect( 'phone' === $control->get_value( array( 'name' => 'icon', 'default' => 'phone' ), array() ), 'Missing icon lost its declared default.' );
	$settings = $control->get_settings();
	expect( 'inline' === $settings['skin'] && array( 'svg' ) === $settings['exclude_inline_options'] && array( 'phone', 'client-logo' ) === $settings['recommended']['rdct-local'], 'Native picker includes unsupported upload or lost project recommendations.' );
	expect( false === $settings['dynamic']['active'], 'String adapter incorrectly advertises object-valued Dynamic icons.' );
	ob_start(); $control->content_template(); $html = ob_get_clean();
	expect( str_contains( $html, 'elementor-control-icons--inline' ) && ! str_contains( $html, 'data-rdct-icon-picker' ), 'Icon adapter renders a replacement panel instead of native markup.' );
	echo "Passed: real native Icons control inheritance/markup, legacy/default/clear values and local catalog recommendations.\n";
}
