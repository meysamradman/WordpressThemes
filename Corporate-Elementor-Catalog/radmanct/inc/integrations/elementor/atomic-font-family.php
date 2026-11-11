<?php

namespace RadmanCT\Integrations\Elementor\Atomic;

defined( 'ABSPATH' ) || exit;

final class FontFamily extends \Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base {
	public function transform( $value, \Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context $context ) {
		return is_string( $value ) ? 'var(--rdct-elementor-font,var(--rdct-font-body))' : null;
	}
}
