<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/image.php';

class ImageColorControl extends ColorControl { public function get_type(): string { return 'rdct-color-alpha'; } }

class Image extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-image'; }
	protected static function component(): string { return 'image'; }
	public function get_title() { return ( __( 'Image', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-image'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'image', $this->get_atomic_settings() ); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		foreach ( array( 'image', 'overlay_image' ) as $key ) { $schema[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make(); }
		foreach ( fields( 'image' ) as $key => $field ) {
			$dependencies = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::RELATION_AND );
			foreach ( $field['condition'] ?? array() as $name => $value ) { $dependencies->where( array( 'operator' => 'in', 'path' => array( $name ), 'value' => array( $value ), 'effect' => 'hide' ) ); }
			$bind = ! empty( $field['media'] ) ? ( 'image_id' === $key ? 'image' : 'overlay_image' ) : $key;
			if ( ! empty( $field['condition'] ) ) { $schema[ $bind ]->set_dependencies( $dependencies->get() ); }
		}
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$items = parent::define_atomic_controls()[0]->get_items(); $groups = array();
		foreach ( $items as $control ) {
			$key = preg_replace( '/_responsive$/', '', $control->get_bind() ); $field = fields( 'image' )[ $key ];
			if ( ! empty( $field['media'] ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'image_id' === $key ? 'image' : 'overlay_image' )->set_label( $field['label'] ); }
			elseif ( ! empty( $field['color'] ) ) { $control = ImageColorControl::bind_to( $key )->set_label( $field['label'] ); }
			$groups[ $field['group'] ][] = $control;
		}
		$sections = array();
		foreach ( \RadmanCTComponents\Components\Image\groups() as $group => $label ) { $sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_image_' . $group )->set_label( $label )->set_items( $groups[ $group ] ?? array() ); }
		return $sections;
	}
}
