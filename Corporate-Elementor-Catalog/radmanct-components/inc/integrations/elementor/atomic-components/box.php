<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/box.php';

class Box extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-box'; }
	protected static function component(): string { return 'box'; }
	public function get_title() { return ( __( 'Content Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-info-box'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'box', $this->get_atomic_settings() ); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['image'] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make();
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$sections = parent::define_atomic_controls();
		$content = array(); $style = array(); $fields = fields( 'box' );
		foreach ( $sections[0]->get_items() as $control ) {
			$base_key = preg_replace( '/_responsive$/', '', $control->get_bind() ); $field = $fields[ $base_key ];
			if ( ! empty( $field['color'] ) || str_ends_with( $base_key, '_size' ) || in_array( $control->get_bind(), array( 'padding', 'label_padding', 'radius' ), true ) ) { $style[] = $control; } else { $content[] = $control; }
		}
		$content[] = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'image' )->set_label( __( 'Image', 'radmanct-components' ) );
		$sections[0]->set_items( $content );
		$sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_box_style' )->set_label( __( 'Style', 'radmanct-components' ) )->set_items( $style );
		return $sections;
	}
}
