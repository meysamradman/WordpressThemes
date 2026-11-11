<?php

namespace RadmanCT\Integrations\Elementor\Atomic;

defined( 'ABSPATH' ) || exit;

function enabled(): bool {
	return class_exists( '\\Elementor\\Modules\\AtomicWidgets\\Module' ) && \Elementor\Modules\AtomicWidgets\Module::is_active();
}

function transformers( $registry ): void {
	if ( ! is_object( $registry ) || ! is_callable( array( $registry, 'register' ) ) || ! class_exists( '\\Elementor\\Modules\\AtomicWidgets\\PropsResolver\\Transformer_Base' ) ) {
		return;
	}
	require_once __DIR__ . '/atomic-font-family.php';
	$registry->register( 'font-family', new FontFamily() );
}

add_action( 'elementor/atomic-widgets/styles/transformers/register', __NAMESPACE__ . '\\transformers', 100 );
