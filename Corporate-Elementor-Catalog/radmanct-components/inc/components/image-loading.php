<?php

namespace RadmanCTComponents\Components\ImageLoading;

defined( 'ABSPATH' ) || exit;

function field(): array {
	return array(
		'label' => __( 'Image loading', 'radmanct-components' ),
		'default' => 'auto',
		'options' => array( 'auto' => __( 'Automatic (WordPress)', 'radmanct-components' ), 'high' => __( 'Main image above the fold', 'radmanct-components' ), 'lazy' => __( 'Lazy loading', 'radmanct-components' ) ),
		'description' => __( 'Use high priority only for the main image visible when the page opens.', 'radmanct-components' ),
	);
}

function attributes( string $policy, bool $initial = true ): array {
	if ( ! $initial || 'lazy' === $policy ) { return array( 'loading' => 'lazy' ); }
	return 'high' === $policy ? array( 'loading' => 'eager', 'fetchpriority' => 'high' ) : array();
}
