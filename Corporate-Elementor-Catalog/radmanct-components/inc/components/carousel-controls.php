<?php

namespace RadmanCTComponents\Components\Carousel;

defined( 'ABSPATH' ) || exit;

function control_slots( array $settings ): array {
	$slots = array( 'top' => '', 'middle' => '', 'bottom' => '' );
	if ( $settings['arrows'] ) {
		$colors = '';
		foreach ( array( 'arrow_color' => 'color', 'arrow_hover_color' => 'hover-color', 'arrow_background' => 'background', 'arrow_hover_background' => 'hover-background', 'arrow_border_color' => 'border-color', 'arrow_hover_border_color' => 'hover-border-color' ) as $key => $property ) { if ( '' !== $settings[ $key ] ) { $colors .= '--rdct-crs-arrow-' . $property . ':' . $settings[ $key ] . ';'; } }
		$buttons = '';
		foreach ( array( 'previous' => __( 'Previous slide', 'radmanct-components' ), 'next' => __( 'Next slide', 'radmanct-components' ) ) as $direction => $label ) {
			$buttons .= '<button type="button" data-' . $direction . ' aria-label="' . esc_attr( $label ) . '">' . \RadmanCTComponents\Icons\render( $settings[ $direction . '_icon' ], array( 'size' => $settings['arrow_icon_size'] ) ) . '</button>';
		}
		$slots[ $settings['position'] ] .= '<div class="rdct-crs__arrows" data-style="' . esc_attr( $settings['arrow_style'] ) . '" data-fill="' . esc_attr( $settings['arrow_fill'] ) . '" data-shadow="' . esc_attr( $settings['arrow_shadow'] ) . '" style="--rdct-crs-btn:' . $settings['arrow_size'] . 'px;--rdct-crs-nav-gap:' . $settings['arrow_gap'] . 'px;' . esc_attr( $colors ) . '" data-align="' . esc_attr( $settings['arrow_alignment'] ) . '">' . $buttons . '</div>';
	}
	$pages = 'none' !== $settings['pagination'] ? '<div class="rdct-crs__pagination"></div>' : '';
	if ( $settings['autoplay'] ) { $pages .= '<button type="button" data-play aria-pressed="true">' . esc_html__( 'Autoplay', 'radmanct-components' ) . '</button>'; }
	if ( '' !== $pages ) { $slots[ $settings['pagination_position'] ] .= '<div class="rdct-crs__pages" data-align="' . esc_attr( $settings['pagination_alignment'] ) . '">' . $pages . '</div>'; }
	return $slots;
}

function control_slot( string $html, string $position, bool $overlay = false ): string {
	return '' === $html ? '' : '<div class="rdct-crs__controls' . ( $overlay ? ' rdct-crs__controls--overlay' : '' ) . '" data-rdct-carousel-controls data-position="' . esc_attr( $position ) . '" hidden>' . $html . '</div>';
}
