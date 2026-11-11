<?php

namespace RadmanCT\Navigation\Hover;

defined( 'ABSPATH' ) || exit;

function fields(): array {
	return array(
		'menuHover' => array( 'group' => 'style', 'label' => __( 'Menu text hover', 'radmanct' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct' ), 'roll' => __( 'Rolling text', 'radmanct' ) ) ),
		'menuIndicator' => array( 'group' => 'style', 'label' => __( 'Menu hover indicator', 'radmanct' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct' ), 'dot' => __( 'Dot', 'radmanct' ) ) ),
	);
}

function settings( array $input ): ?array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( ! is_string( $value ) || ! isset( $field['options'][ $value ] ) ) { return null; }
		$result[ $key ] = $value;
	}
	return $result;
}

function assets( array $options ): array {
	$result = array();
	if ( 'roll' === ( $options['menuHover'] ?? 'none' ) ) { $result[] = 'menu-hover-roll'; }
	if ( 'dot' === ( $options['menuIndicator'] ?? 'none' ) ) { $result[] = 'menu-indicator-dot'; }
	return $result;
}

function title( string $title ): string {
	return '<span class="rdct-menu-roll"><span class="rdct-menu-roll__text">' . $title . '</span><span class="rdct-menu-roll__copy" aria-hidden="true">' . esc_html( wp_strip_all_tags( $title ) ) . '</span></span>';
}
