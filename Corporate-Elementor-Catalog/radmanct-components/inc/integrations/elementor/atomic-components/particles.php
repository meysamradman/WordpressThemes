<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/particles.php';

class ParticleColor extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type {
	public static function get_key(): string { return 'rdct-particle-color'; }
	protected function define_shape(): array { return array( 'scolor' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::make()->default( '#ffffff' ) ); }
}
class ParticleColors extends \Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type {
	public static function get_key(): string { return 'rdct-particle-colors'; }
	protected function define_item_type(): \Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type { return ParticleColor::make(); }
	protected function validate_value( $value ): bool { return is_array( $value ) && count( $value ) <= 16 && parent::validate_value( $value ); }
}
class ParticleColorsControl extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	public function get_type(): string { return 'rdct-particle-colors'; }
	public function get_props(): array { return array(); }
}
class Particles extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-particles'; }
	protected static function component(): string { return 'particles'; }
	public function get_title() { return ( __( 'Particles', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-bullet-list'; }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['image'] = image_field_prop();
		$schema['image']->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'shape_image' ), 'value' => array( true ), 'effect' => 'hide' ) )->get() );
		$schema['multi_color_values'] = ParticleColors::make()->default( array() )->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'color_type' ), 'value' => array( 'multi_color' ), 'effect' => 'hide' ) )->get() );
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$groups = array();
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) {
			$key = $control->get_bind();
			if ( 'image' === $key ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'image' )->set_label( __( 'Image', 'radmanct-components' ) ); }
			$groups[ fields( 'particles' )[ $key ]['group'] ][] = $control;
			if ( 'color_type' === $key ) { $groups['options'][] = ParticleColorsControl::bind_to( 'multi_color_values' )->set_label( __( 'Multiple colors', 'radmanct-components' ) ); }
		}
		$sections = array();
		foreach ( \RadmanCTComponents\Components\Particles\groups() as $key => $label ) { $sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_particles_' . $key )->set_label( $label )->set_items( $groups[ $key ] ?? array() ); }
		return $sections;
	}
}
