<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/flipbox.php';

class FlipBox extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-flipbox'; }
	protected static function component(): string { return 'flipbox'; }
	public function get_title() { return ( __( 'Flip Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-flip-box'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'flipbox', $this->get_atomic_settings() ); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		foreach ( array( 'front', 'back' ) as $side ) { $schema[ $side . '_image' ] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make(); }
		foreach ( array( 'front', 'back' ) as $side ) {
			foreach ( array( 'content' => 'html', 'template' => 'template' ) as $key => $value ) {
				$schema[ $side . '_' . $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( $side . '_type' ), 'value' => array( $value ), 'effect' => 'hide' ) )->get() );
			}
		}
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$sections = parent::define_atomic_controls(); $groups = array( 'general' => array(), 'front' => array(), 'back' => array() );
		foreach ( $sections[0]->get_items() as $control ) {
			$key = preg_replace( '/_responsive$/', '', $control->get_bind() );
			if ( str_ends_with( $key, '_image_id' ) ) { continue; }
			if ( str_ends_with( $key, '_template' ) ) {
				$options = array();
				foreach ( \RadmanCTComponents\Components\FlipBox\templates() as $id => $title ) { $options[] = array( 'value' => (int) $id, 'label' => $title ); }
				$control = TemplateSelectControl::bind_to( $key )->set_label( __( 'Select template', 'radmanct-components' ) )->set_options( $options );
			}
			$group = str_starts_with( $key, 'front_' ) ? 'front' : ( str_starts_with( $key, 'back_' ) ? 'back' : 'general' );
			$groups[ $group ][] = $control;
		}
		$result = array();
		foreach ( $groups as $group => $controls ) {
			if ( 'general' !== $group ) { $controls[] = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( $group . '_image' )->set_label( __( 'Background image', 'radmanct-components' ) ); }
			$label = 'front' === $group ? __( 'Front side', 'radmanct-components' ) : ( 'back' === $group ? __( 'Back side', 'radmanct-components' ) : __( 'General', 'radmanct-components' ) );
			$result[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_flipbox_' . $group )->set_label( $label )->set_items( $controls );
		}
		return $result;
	}
}
