<?php

namespace RadmanCT\CustomCode;

defined( 'ABSPATH' ) || exit;

function defaults(): array {
	return array_fill_keys( array( 'custom_css', 'custom_css_desktop', 'custom_css_laptop', 'custom_css_tablet', 'custom_css_mobile', 'custom_js' ), '' );
}

function valid( $code ): bool {
	return is_string( $code ) && strlen( $code ) <= 65536 && ! str_contains( $code, "\0" ) && ! preg_match( '~</\s*(?:script|style)\b|<\?~i', $code );
}

function can_edit(): bool {
	return current_user_can( 'edit_theme_options' ) && current_user_can( 'unfiltered_html' );
}

function css( array $settings ): string {
	$queries = array( 'custom_css' => '', 'custom_css_desktop' => '(min-width:1440px)', 'custom_css_laptop' => '(min-width:1024px) and (max-width:1439.98px)', 'custom_css_tablet' => '(min-width:768px) and (max-width:1023.98px)', 'custom_css_mobile' => '(max-width:767.98px)' );
	$result = '';
	foreach ( $queries as $key => $query ) {
		$code = $settings[ $key ] ?? '';
		if ( ! valid( $code ) || '' === trim( $code ) ) { continue; }
		$result .= $query ? "\n@media " . $query . "{\n" . $code . "\n}\n" : $code . "\n";
	}
	return $result;
}

function enqueue(): void {
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
	$settings = \RadmanCT\Settings\values();
	$css = css( $settings );
	if ( '' !== $css ) {
		wp_register_style( 'rdct-custom-code', false, array( 'rdct-theme' ), null );
		wp_enqueue_style( 'rdct-custom-code' );
		wp_add_inline_style( 'rdct-custom-code', $css );
	}
	$js = $settings['custom_js'];
	if ( ! valid( $js ) || '' === trim( $js ) ) { return; }
	wp_register_script( 'rdct-custom-code', false, array(), null, true );
	wp_enqueue_script( 'rdct-custom-code' );
	wp_add_inline_script( 'rdct-custom-code', "(function(){var run=function(){\n" . $js . "\n};if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',run,{once:true});}else{run();}})();", 'after' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 100 );
