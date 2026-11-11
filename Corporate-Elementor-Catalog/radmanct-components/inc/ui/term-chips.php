<?php

namespace RadmanCTComponents\UI;

defined( 'ABSPATH' ) || exit;

function term_chips( array $terms, array $options = array() ): void {
	$options += array( 'current' => 0, 'counts' => false, 'class' => '', 'url' => 'get_term_link', 'link_attributes' => null );
	if ( ! $terms ) { return; }
	echo '<ul class="rdct-taxonomy-chips' . ( is_string( $options['class'] ) && $options['class'] ? ' ' . esc_attr( $options['class'] ) : '' ) . '">';
	foreach ( $terms as $term ) {
		$url = call_user_func( $options['url'], $term );
		if ( is_wp_error( $url ) || ! is_string( $url ) ) { continue; }
		echo '<li><a href="' . esc_url( $url ) . '"' . ( (int) $options['current'] === (int) $term->term_id ? ' aria-current="page"' : '' );
		$attributes = is_callable( $options['link_attributes'] ) ? call_user_func( $options['link_attributes'], $term ) : array();
		foreach ( $attributes as $name => $value ) { if ( preg_match( '/^data-[a-z0-9-]+$/D', $name ) ) { echo ' ' . $name . '="' . esc_attr( $value ) . '"'; } }
		echo '>' . esc_html( $term->name );
		if ( $options['counts'] ) { echo ' <span>' . esc_html( number_format_i18n( $term->count ) ) . '</span>'; }
		echo '</a></li>';
	}
	echo '</ul>';
}
