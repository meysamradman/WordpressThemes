<?php

namespace RadmanCTComponents\Elementor\AtomicComponents;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/icon-box.php';

class IconBox extends Component {
	public static function get_element_type(): string { return 'rdct-atomic-icon-box'; }
	protected static function component(): string { return 'icon-box'; }
	public function get_title() { return ( __( 'Icon Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-icon-box'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'icon-box', $this->get_atomic_settings() ); }
	public function get_script_depends() { return array(); }
	protected static function define_props_schema(): array {
		$schema = parent::define_props_schema();
		$schema['image'] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make();
		foreach ( array( 'icon' => 'icon', 'image' => 'image' ) as $key => $value ) { $schema[ $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'icon_type' ), 'value' => array( $value ), 'effect' => 'hide' ) )->get() ); }
		foreach ( array( 'animation_target', 'animation_trigger', 'animation_duration' ) as $key ) { $schema[ $key ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'animation' ), 'value' => array( 'spin', 'float', 'pulse', 'draw' ), 'effect' => 'hide' ) )->get() ); }
		foreach ( array( 'icon_padding', 'border_width' ) as $key ) { $schema[ $key . '_responsive' ]->set_dependencies( \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make()->where( array( 'operator' => 'in', 'path' => array( 'shape' ), 'value' => array( 'square', 'circle', 'lozenge' ), 'effect' => 'hide' ) )->get() ); }
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$groups = array();
		foreach ( parent::define_atomic_controls()[0]->get_items() as $control ) {
			$key = preg_replace( '/_responsive$/', '', $control->get_bind() ); if ( 'image_id' === $key ) { continue; }
			$group = str_starts_with( $key, 'animation' ) ? 'animation' : ( str_starts_with( $key, 'hover_' ) ? 'hover' : ( in_array( $key, array( 'icon_color', 'icon_background', 'border_color', 'shape', 'icon_size', 'icon_padding', 'border_width' ), true ) ? 'style' : ( in_array( $key, array( 'title', 'title_tag', 'title_size' ), true ) ? 'title' : ( in_array( $key, array( 'text', 'text_size', 'url', 'new_tab', 'nofollow', 'link_text' ), true ) ? 'description' : ( in_array( $key, array( 'gap', 'title_gap', 'icon_side', 'vertical_align', 'align' ), true ) ? 'spacing' : 'icon' ) ) ) ) );
			$groups[ $group ][] = $control;
		}
		$groups['icon'][] = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'image' )->set_label( __( 'Image', 'radmanct-components' ) );
		$sections = array();
		foreach ( array( 'icon' => __( 'Icon', 'radmanct-components' ), 'title' => __( 'Title', 'radmanct-components' ), 'description' => __( 'Description', 'radmanct-components' ), 'style' => __( 'Icon style (Normal)', 'radmanct-components' ), 'hover' => __( 'Icon style (Hover)', 'radmanct-components' ), 'spacing' => __( 'Alignment and spacing', 'radmanct-components' ), 'animation' => __( 'SVG animation', 'radmanct-components' ) ) as $group => $label ) { $sections[] = \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_icon_box_' . $group )->set_label( $label )->set_items( $groups[ $group ] ?? array() ); }
		return $sections;
	}
}
