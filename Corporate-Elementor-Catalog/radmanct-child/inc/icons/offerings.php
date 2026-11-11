<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'radmanct/icons/packs', static function ( array $packs ): array {
	$packs['client'] = static function (): array {
		$icons = array();
		$shapes = array(
			'client-code' => array( __( 'Custom development', 'radmanct-child' ), '<path d="m8 7-5 5 5 5m8-10 5 5-5 5M14 4l-4 16"/>' ),
			'client-network' => array( __( 'Multisite network', 'radmanct-child' ), '<rect x="8" y="2" width="8" height="6" rx="1"/><rect x="2" y="16" width="7" height="6" rx="1"/><rect x="15" y="16" width="7" height="6" rx="1"/><path d="M12 8v4M5.5 16v-4h13v4"/>' ),
			'client-security' => array( __( 'Security', 'radmanct-child' ), '<path d="M12 3 4 6v5c0 5 4 8 8 10 4-2 8-5 8-10V6l-8-3Z"/>' ),
			'client-responsive' => array( __( 'Responsive layout', 'radmanct-child' ), '<rect x="2" y="4" width="15" height="12" rx="1.5"/><path d="M6 20h7M9.5 16v4"/><rect x="15" y="10" width="7" height="11" rx="1.2"/>' ),
			'client-performance' => array( __( 'Performance', 'radmanct-child' ), '<path d="m13 2-9 12h7l-1 8 10-13h-7l0-7Z"/>' ),
		);
		foreach ( $shapes as $name => $shape ) { $icons[ $name ] ??= array( 'label' => $shape[0], 'directional' => false, 'viewbox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'body' => $shape[1] ); }
		return $icons;
	};
	return $packs;
} );
