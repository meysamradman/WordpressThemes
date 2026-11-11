<?php

namespace RadmanCTShared\Elementor\Attributes;

defined( 'ABSPATH' ) || exit;
if ( ! function_exists( __NAMESPACE__ . '\\sanitize' ) ) {
	function sanitize( $value ): string {
		return is_string( $value ) && 1 === preg_match( '/^(?=[\\s\\S]{0,16384}$)(?:\\s*(?:data-[a-z][a-z0-9-]*|aria-[a-z][a-z0-9-]*|role|title|dir|lang|tabindex|hidden|inert|translate)="[^"<>\\x00-\\x1f]*")*\\s*$/', $value ) ? $value : '';
	}
}
