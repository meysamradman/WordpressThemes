<?php

namespace RadmanCTShared\Elementor\Attributes;

defined( 'ABSPATH' ) || exit;
if ( ! function_exists( __NAMESPACE__ . '\\sanitize' ) ) {
	function sanitize( $value ): string {
		return is_string( $value ) && 1 === preg_match( '{{pattern}}', $value ) ? $value : '';
	}
}
