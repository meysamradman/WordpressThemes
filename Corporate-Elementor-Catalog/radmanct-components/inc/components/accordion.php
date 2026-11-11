<?php
namespace RadmanCTComponents\Components\Accordion;
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/custom-content.php';

function fields(): array {
	return array(
		'style' => array( 'label' => __( 'Accordion style', 'radmanct-components' ), 'default' => 'lined', 'options' => array( 'lined' => __( 'Lined', 'radmanct-components' ), 'boxed' => __( 'Boxed', 'radmanct-components' ) ) ),
		'multiple' => array( 'label' => __( 'Allow multiple open items', 'radmanct-components' ), 'default' => false ),
		'open_item' => array( 'label' => __( 'Initially open item (zero closes all)', 'radmanct-components' ), 'default' => 1, 'min' => 0, 'max' => 100 ),
		'heading_tag' => array( 'label' => __( 'Title HTML tag', 'radmanct-components' ), 'default' => 'h3', 'options' => array( 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6' ) ),
		'indicator' => array( 'label' => __( 'Accordion indicator', 'radmanct-components' ), 'default' => 'plus', 'options' => array( 'plus' => __( 'Plus and minus', 'radmanct-components' ), 'chevron' => __( 'Chevron', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ) ) ),
		'icon_position' => array( 'label' => __( 'Icon position', 'radmanct-components' ), 'default' => 'end', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'gap' => array( 'label' => __( 'Items gap', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 100 ),
		'padding' => array( 'label' => __( 'Content padding', 'radmanct-components' ), 'default' => 24, 'min' => 0, 'max' => 100 ),
		'radius' => array( 'label' => __( 'Border radius', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 100 ),
	);
}
function item_fields(): array {
	return array(
		'title' => array( 'label' => __( 'Title', 'radmanct-components' ), 'default' => '' ),
		'content_type' => array( 'label' => __( 'Content type', 'radmanct-components' ), 'default' => 'text', 'options' => array( 'text' => __( 'Text editor', 'radmanct-components' ), 'template' => __( 'Elementor template', 'radmanct-components' ) ) ),
		'content' => array( 'label' => __( 'Content', 'radmanct-components' ), 'default' => '', 'multiline' => true, 'rich' => true, 'condition' => array( 'content_type' => 'text' ) ),
		'template_id' => array( 'label' => __( 'Select template', 'radmanct-components' ), 'default' => 0, 'template' => true, 'condition' => array( 'content_type' => 'template' ) ),
	);
}
function settings( array $options ): array {
	$s = array();
	foreach ( fields() as $key => $field ) {
		$value = $options[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $s[ $key ] = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( isset( $field['min'] ) ) { $s[ $key ] = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (int) $value ) ) : $field['default']; }
		else { $s[ $key ] = in_array( $value, array( true, 1, '1', 'yes', 'true' ), true ); }
	}
	return $s;
}
function assets( array $options ): array { return array( 'accordion', 'accordion-' . settings( $options )['style'] ); }
function render( array $items, array $options = array() ): string {
	$s = settings( $options ); $rows = ''; $index = 0; $group = wp_unique_id( 'rdct-accordion-' );
	foreach ( array_slice( $items, 0, 100 ) as $item ) {
		if ( ! is_array( $item ) || ! is_string( $item['title'] ?? null ) ) { continue; }
		$title = sanitize_text_field( $item['title'] ); if ( '' === trim( $title ) ) { continue; }
		$content = is_string( $item['content'] ?? null ) ? wp_kses_post( $item['content'] ) : '';
		if ( \RadmanCTComponents\Components\CustomContent\key( $item ) ) { $content = \RadmanCTComponents\Components\CustomContent\render( \RadmanCTComponents\Components\CustomContent\key( $item ), array( 'slot' => 'accordion' ) ); }
		elseif ( 'template' === ( $item['content_type'] ?? '' ) ) { require_once __DIR__ . '/template-library.php'; $content = \RadmanCTComponents\Components\TemplateLibrary\render( is_numeric( $item['template_id'] ?? null ) ? (int) $item['template_id'] : 0 ) ?: $content; }
		$index++;
		$rows .= '<details class="rdct-accordion__item"' . ( $s['multiple'] ? '' : ' name="' . esc_attr( $group ) . '"' ) . ( $index === $s['open_item'] ? ' open' : '' ) . '><summary class="rdct-accordion__summary"><' . $s['heading_tag'] . ' class="rdct-accordion__title">' . esc_html( $title ) . '</' . $s['heading_tag'] . '>';
		if ( 'none' !== $s['indicator'] ) { $rows .= '<span class="rdct-accordion__indicator" aria-hidden="true"></span>'; }
		$rows .= '</summary>';
		if ( '' !== trim( $content ) ) { $rows .= '<div class="rdct-accordion__content">' . $content . '</div>'; }
		$rows .= '</details>';
	}
	if ( '' === $rows ) { return ''; }
	return '<div class="rdct-accordion rdct-accordion--' . esc_attr( $s['style'] ) . '" data-indicator="' . esc_attr( $s['indicator'] ) . '" data-icon-position="' . esc_attr( $s['icon_position'] ) . '" style="--rdct-accordion-gap:' . $s['gap'] . 'px;--rdct-accordion-padding:' . $s['padding'] . 'px;--rdct-accordion-radius:' . $s['radius'] . 'px;">' . $rows . '</div>';
}
