<?php

namespace RadmanCT\Integrations\Elementor\CustomCSS;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/custom-css-compiler.php';

function description(): string {
	return __( 'Use selector for this element, or write child selectors. Maximum 16 KB. Use local relative URLs. Imports, fonts and keyframes belong in site styles.', 'radmanct' );
}

function classic_controls( $element, string $section ): void {
	if ( 'section_custom_css_pro' !== $section || ! current_user_can( 'unfiltered_html' ) || $element->get_controls( 'rdct_custom_css' ) ) { return; }
	$native = (bool) $element->get_controls( 'custom_css' );
	if ( $native ) {
		$stored = $element->get_data( 'settings' );
		if ( ! is_array( $stored ) || ! is_string( $stored['rdct_custom_css'] ?? null ) || '' === trim( $stored['rdct_custom_css'] ) ) { return; }
	}
	$element->start_controls_section( 'rdct_custom_css_section', array( 'label' => $native ? __( 'Saved Radman CSS', 'radmanct' ) : __( 'Custom CSS', 'radmanct' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
	$element->add_control( 'rdct_custom_css', array( 'type' => \Elementor\Controls_Manager::CODE, 'language' => 'css', 'render_type' => 'ui', 'description' => description() ) );
	$element->end_controls_section();
}

function atomic_schema( array $schema ): array {
	$schema['rdct_custom_css'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make()->default( '' );
	return $schema;
}

function atomic_controls( array $controls ): array {
	if ( ! current_user_can( 'unfiltered_html' ) ) { return $controls; }
	$control = \Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control::bind_to( 'rdct_custom_css' )->set_label( __( 'Custom CSS', 'radmanct' ) )->set_description( description() )->set_placeholder( 'selector { color: inherit; }' );
	$controls[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_custom_css_section' )->set_label( __( 'Custom CSS', 'radmanct' ) )->set_items( array( $control ) );
	return $controls;
}

function data( $element ): array {
	return compile( $element->get_settings( 'rdct_custom_css' ), $element->get_id() );
}

function render( $element ): void {
	$css = data( $element );
	if ( $css ) { $element->add_render_attribute( '_wrapper', 'class', $css['class'] ); }
}

function parse( $post_css, $element ): void {
	if ( $post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS ) { return; }
	$css = data( $element );
	if ( $css ) { $post_css->get_stylesheet()->add_raw_css( $css['css'] ); }
}

function preview(): void {
	if ( ! current_user_can( 'unfiltered_html' ) ) { return; }
	$entry = \RadmanCT\Assets\entry( 'src/js/integrations/elementor-custom-css-preview.js' );
	if ( $entry ) { wp_enqueue_script( 'rdct-custom-css-preview', \RadmanCT\Assets\url( $entry['file'] ), array( 'elementor-frontend' ), null, true ); }
}

add_action( 'elementor/element/after_section_end', __NAMESPACE__ . '\\classic_controls', 30, 2 );
add_filter( 'elementor/atomic-widgets/props-schema', __NAMESPACE__ . '\\atomic_schema' );
add_filter( 'elementor/atomic-widgets/controls', __NAMESPACE__ . '\\atomic_controls', 30 );
add_action( 'elementor/frontend/before_render', __NAMESPACE__ . '\\render', 15 );
add_action( 'elementor/element/parse_css', __NAMESPACE__ . '\\parse', 20, 2 );
add_action( 'elementor/preview/enqueue_scripts', __NAMESPACE__ . '\\preview' );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array { if ( 'rdct-custom-css-preview-js' === ( $attributes['id'] ?? '' ) ) { $attributes['type'] = 'module'; } return $attributes; } );
