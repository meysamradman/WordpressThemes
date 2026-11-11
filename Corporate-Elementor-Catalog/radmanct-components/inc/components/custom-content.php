<?php

namespace RadmanCTComponents\Components\CustomContent;

defined( 'ABSPATH' ) || exit;

function key( array $input ): string {
	$value = $input['custom_content'] ?? '';
	return is_string( $value ) && preg_match( '/^[a-z][a-z0-9-]{0,79}$/D', $value ) ? $value : '';
}

function render( string $key, array $context = array() ): string {
	return function_exists( 'rdct_render_content' ) ? rdct_render_content( $key, $context ) : '';
}
