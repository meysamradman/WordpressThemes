<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;

function rich_text_prop( string $default = '' ) {
	return \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type::make()
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Escaped_Html_Prop_Type::make() )
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make() )
		->default( $default, 'escaped-html' );
}

function image_field_prop() {
	return \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type::make()
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make() )
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make() );
}

function template_field_prop() {
	return \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type::make()
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make() )
		->add_prop_type( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make() )
		->default( 0, 'number' );
}

function typed_field( $prop, $value ): array {
	if ( $prop instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type ) {
		if ( $prop->get_prop_type( 'number' ) && ! $prop->get_prop_type( 'image' ) ) { return \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( is_numeric( $value ) ? (int) $value : 0 ); }
		if ( $prop->get_prop_type( 'image' ) ) {
			return is_array( $value ) ? \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::generate( $value ) : \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( is_numeric( $value ) ? (int) $value : 0 );
		}
		return \Elementor\Modules\AtomicWidgets\PropTypes\Escaped_Html_Prop_Type::generate( is_string( $value ) ? $value : '' );
	}
	return $prop::generate( $value );
}

class RichTextControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-rich-text'; }
	public function get_props(): array { return array(); }
}

class TemplateSelectControl extends \Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control {
	public function get_type(): string { return 'rdct-template-select'; }
}
