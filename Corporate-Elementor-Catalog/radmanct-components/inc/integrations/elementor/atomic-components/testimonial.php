<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/testimonial/fields.php';

class Testimonial extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-testimonial'; }
	protected static function component(): string { return 'testimonial'; }
	public function get_title() { return ( __( 'Customer review', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-testimonial'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'testimonial', $this->get_atomic_settings() ); }
	public function get_script_depends() { return array(); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['link'] = \Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type::make();
		foreach ( array( 'image_id', 'client_image_id' ) as $key ) { $schema[ $key ] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make(); }
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$groups = array( 'review' => array(), 'appearance' => array() );
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) {
			$key = $control->get_bind();
			if ( 'link' === $key ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control::bind_to( $key )->set_label( \RadmanCTComponents\Components\Testimonial\item_fields()[ $key ]['label'] ); }
			if ( in_array( $key, array( 'image_id', 'client_image_id' ), true ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( $key )->set_label( \RadmanCTComponents\Components\Testimonial\item_fields()[ $key ]['label'] ); }
			$groups[ str_starts_with( $key, 'testimonial_' ) ? 'appearance' : 'review' ][] = $control;
		}
		return array(
			\Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_testimonial_review' )->set_label( __( 'Customer review', 'radmanct-components' ) )->set_items( $groups['review'] ),
			\Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_testimonial_appearance' )->set_label( __( 'Style', 'radmanct-components' ) )->set_items( $groups['appearance'] ),
		);
	}
}
