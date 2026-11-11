<?php

namespace RadmanCTComponents\Components\Testimonial;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/fields.php';
require_once dirname( __DIR__ ) . '/presentation.php';
require_once dirname( __DIR__ ) . '/image-media.php';

function image( $value, string $class, string $default_size = 'thumbnail' ): string {
	$id = is_array( $value ) ? ( $value['id'] ?? 0 ) : $value;
	$id = is_numeric( $id ) ? (int) apply_filters( 'wpml_object_id', max( 0, (int) $id ), 'attachment', true ) : 0;
	return $id && wp_attachment_is_image( $id ) ? ( wp_get_attachment_image( $id, \RadmanCTComponents\Components\ImageMedia\size( $value, $default_size ), false, array( 'class' => $class, 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ) ?: '' ) : '';
}
function render( array $item, array $options = array(), ?array &$styles = null ): string {
	$content = is_string( $item['content'] ?? null ) ? wp_kses_post( substr( $item['content'], 0, 8000 ) ) : '';
	if ( '' === trim( strip_tags( $content ) ) ) { return ''; }
	$values = array();
	foreach ( array( 'title', 'position', 'date', 'source' ) as $key ) { $values[ $key ] = is_string( $item[ $key ] ?? null ) ? esc_html( sanitize_text_field( substr( $item[ $key ], 0, 500 ) ) ) : ''; }
	$css = '';
	$appearance = presentation();
	foreach ( fields() as $key => $field ) {
		if ( isset( $appearance[ $key ] ) ) { continue; }
		$value = $options[ $key ] ?? $field['default'];
		if ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? min( $field['max'], max( $field['min'], (float) $value ) ) : $field['default']; $css .= '--rdct-tst-' . str_replace( 'testimonial_', '', $key ) . ':' . $value . 'px;'; }
		elseif ( ! empty( $field['color'] ) && is_string( $value ) && preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ) { $css .= '--rdct-tst-' . str_replace( '_', '-', substr( $key, 12 ) ) . ':' . $value . ';'; }
	}
	$align = $options['testimonial_align'] ?? 'auto';
	$align = in_array( $align, array( 'auto', 'start', 'center', 'end' ), true ) ? $align : 'auto';
	$rating = is_numeric( $item['rating'] ?? null ) && is_finite( (float) $item['rating'] ) ? max( 0, min( 5, (int) $item['rating'] ) ) : 0;
	$stars = $rating ? '<span class="rdct-tst__rating" role="img" aria-label="' . esc_attr( sprintf( __( 'Rated %d out of 5', 'radmanct-components' ), $rating ) ) . '"><span aria-hidden="true">' . str_repeat( '&#9733;', $rating ) . str_repeat( '&#9734;', 5 - $rating ) . '</span></span>' : '';
	$link = $item['link'] ?? '';
	$target_blank = is_array( $link ) && ( '_blank' === ( $link['target'] ?? '' ) || ! empty( $link['is_external'] ) );
	$url = is_array( $link ) ? ( $link['href'] ?? $link['url'] ?? '' ) : $link;
	$url = is_string( $url ) ? esc_url( substr( $url, 0, 2048 ), array( 'http', 'https' ) ) : '';
	$source = $values['source'];
	$source_icon = $item['source_icon'] ?? '';
	if ( is_string( $source_icon ) && str_starts_with( $source_icon, 'brand-' ) ) {
		require_once dirname( __DIR__, 2 ) . '/icons.php';
		$icon = \RadmanCTComponents\Icons\render( $source_icon, array( 'size' => 18 ) );
		if ( $icon ) { $label = $values['source'] ?: esc_html( \RadmanCTComponents\Icons\labels()[ $source_icon ] ?? '' ); $source = '<span class="rdct-tst__source-icon" aria-hidden="true">' . $icon . '</span><span>' . $label . '</span>'; }
	}
	if ( $source && $url ) { $source = '<a href="' . $url . '"' . ( $target_blank ? ' target="_blank" rel="nofollow noopener noreferrer"' : ' rel="nofollow"' ) . '>' . $source . '</a>'; }
	$style = style( $options['testimonial_style'] ?? '' );
	if ( str_starts_with( $style, 's' ) && preg_match( '/^s[0-9]{2}$/D', $style ) ) {
		require_once __DIR__ . '/presets.php';
		return \RadmanCTComponents\Components\Presentation\render( preset( $style, $item, $values, $content, $stars, $source, $align, $css ), $options, 'testimonial', $styles );
	}
	$html = '<figure class="rdct-tst rdct-tst--' . style( $options['testimonial_style'] ?? '' ) . '" data-align="' . $align . '" style="' . esc_attr( $css ) . '"><blockquote class="rdct-tst__quote">' . $content . '</blockquote><figcaption class="rdct-tst__info">' . image( $item['image_id'] ?? 0, 'rdct-tst__avatar' ) . '<div class="rdct-tst__author"><cite>' . $values['title'] . '</cite><span>' . $values['position'] . '</span>' . $stars . '<small>' . $values['date'] . ( $values['date'] && $source ? ' · ' : '' ) . $source . '</small></div>' . image( $item['client_image_id'] ?? 0, 'rdct-tst__logo' ) . '</figcaption></figure>';
	return \RadmanCTComponents\Components\Presentation\render( $html, $options, 'testimonial', $styles );
}
