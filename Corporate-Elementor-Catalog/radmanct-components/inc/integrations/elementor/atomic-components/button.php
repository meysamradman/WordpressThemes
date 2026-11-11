<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/button.php';

class Button extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-button'; }
	protected static function component(): string { return 'button'; }
	public function get_title() { return ( __( 'Button', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-button'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'button', $this->get_atomic_settings() ); }
	public function get_script_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\script_depends( 'button', $this->get_atomic_settings() ); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['duration'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::make()->float();
		foreach ( fields( 'button' ) as $key => $field ) { if ( ! empty( $field['media'] ) ) { $schema[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make();
			$rules = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make();
			foreach ( $field['condition'] ?? array() as $path => $values ) { $rules->where( array( 'operator' => 'in', 'path' => array( $path ), 'value' => $values, 'effect' => 'hide' ) ); }
			$schema[ $key ]->set_dependencies( $rules->get() ); } }
		$schema['link'] = \Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type::make();
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$groups = array();
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) {
			$key = preg_replace( '/_responsive$/', '', $control->get_bind() );
			if ( in_array( $key, array( 'url', 'new_tab', 'tag' ), true ) ) { continue; }
			$field = fields( 'button' )[ $key ] ?? array();
			$group = $field['group'] ?? 'appearance';
			if ( ! empty( $field['media'] ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( $key )->set_label( $field['label'] ); }
			$groups[ $group ][] = $control;
		}
		$groups['content'][] = \Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control::bind_to( 'link' )->set_label( __( 'Link', 'radmanct-components' ) );
		$sections = array();
		foreach ( \RadmanCTComponents\Components\Button\groups() as $key => $title ) {
			$sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_button_' . $key )->set_label( $title )->set_items( $groups[ $key ] ?? array() );
		}
		return $sections;
	}
}
