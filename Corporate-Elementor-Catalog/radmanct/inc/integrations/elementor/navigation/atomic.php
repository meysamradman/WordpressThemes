<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/safe-attributes.php';

class Choice extends \Elementor\Modules\AtomicWidgets\Controls\Base\Atomic_Control_Base {
	private array $options = array();
	public function get_type(): string { return 'rdct-navigation-choice'; }
	public function configure( array $choices ): self { foreach ( $choices as $value => $label ) { $this->options[] = array( 'value' => $value, 'label' => $label ); } return $this; }
	public function get_props(): array { return array( 'options' => $this->options ); }
}

class Color extends Choice { public function get_type(): string { return 'rdct-navigation-color'; } }

class Atomic extends \Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base {
	use \Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template { get_shared_templates as private native_shared_templates; }
	protected function get_shared_templates(): array { return $this->native_shared_templates() + array( 'radmanct/attributes' => dirname( __DIR__ ) . '/safe-attributes.html.twig' ); }
	protected static function control_fields(): array { return fields(); }
	public static function get_element_type(): string { return 'rdct-atomic-navigation'; }
	public function get_title() { return ( __( 'Navigation', 'radmanct' ) ); }
	public function get_icon() { return 'eicon-nav-menu'; }
	public function get_categories(): array { return array( defined( 'RDCT_COMPONENTS_FILE' ) ? 'radmanct-atomic' : 'v4-elements' ); }
	public static function html_tag_follows_link(): bool { return false; }
	public static function get_computed_html_tag( array $settings ): string { return 'div'; }
	public function get_style_depends() { return $this->is_type_instance() ? array() : styles( $this->get_atomic_settings() ); }
	public function get_script_depends() { return array( 'rdct-elementor-navigation' ); }
	protected static function define_props_schema(): array {
		$schema = array( 'classes' => \Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type::make()->default( array() ), 'attributes' => \Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type::make(), 'panelImage' => \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::make() );
		foreach ( static::control_fields() as $key => $field ) {
			if ( 'panelImageId' === $key ) { continue; }
			$class = is_bool( $field['default'] ) ? 'Boolean' : ( is_int( $field['default'] ) ? 'Number' : 'String' );
			$type = '\\Elementor\\Modules\\AtomicWidgets\\PropTypes\\Primitives\\' . $class . '_Prop_Type';
			$prop = $type::make()->default( $field['default'] );
			if ( isset( $field['options'] ) ) { $prop->enum( array_keys( $field['options'] ) ); }
			if ( isset( $field['condition'] ) ) { $dependencies = \Elementor\Modules\AtomicWidgets\PropDependencies\Manager::make( 'and' ); foreach ( $field['condition'] as $path => $values ) { $dependencies->where( array( 'operator' => 'in', 'path' => array( $path ), 'value' => $values, 'effect' => 'hide' ) ); } $prop->set_dependencies( $dependencies->get() ); }
			$schema[ $key ] = $prop;
		}
		return $schema;
	}
	protected function define_atomic_controls(): array {
		$groups = array( 'menu' => array(), 'panel' => array(), 'style' => array() );
		foreach ( static::control_fields() as $key => $field ) {
			if ( 'panelImageId' === $key ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control::bind_to( 'panelImage' ); }
			elseif ( isset( $field['choices'] ) ) { $control = Choice::bind_to( $key )->configure( field_choices( $key, $field ) ); }
			elseif ( isset( $field['options'] ) ) {
				$choices = array(); foreach ( $field['options'] as $value => $label ) { $choices[] = array( 'value' => $value, 'label' => $label ); }
				$control = \Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control::bind_to( $key )->set_options( $choices );
			} elseif ( is_bool( $field['default'] ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control::bind_to( $key ); }
			elseif ( is_int( $field['default'] ) ) { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control::bind_to( $key )->set_min( $field['min'] )->set_max( $field['max'] ); }
			elseif ( ! empty( $field['color'] ) ) { $control = Color::bind_to( $key ); }
			else { $control = \Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control::bind_to( $key ); }
			$group = $field['group'];
			$groups[ $group ][] = $control->set_label( $field['label'] );
		}
		return array_values( array_filter( array( \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_nav_menu' )->set_label( __( 'Menu', 'radmanct' ) )->set_items( $groups['menu'] ), \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_nav_panel' )->set_label( __( 'Panel layout', 'radmanct' ) )->set_items( $groups['panel'] ), \Elementor\Modules\AtomicWidgets\Controls\Section::make()->set_id( 'rdct_nav_style' )->set_label( __( 'Style', 'radmanct' ) )->set_items( $groups['style'] ) ), static fn( $section ) => count( $section->get_items() ) > 0 ) );
	}
	protected function get_templates(): array { return array( 'radmanct/atomic/navigation' => __DIR__ . '/navigation.html.twig' ); }
	protected function render() {
		$settings = $this->get_atomic_settings();
		echo '<div class="' . esc_attr( implode( ' ', array_filter( $settings['classes'] ?? array(), 'is_string' ) ) ) . '" data-interaction-id="' . esc_attr( $this->get_interaction_id() ) . '"';
		if ( ! empty( $settings['_cssid'] ) ) { echo ' id="' . esc_attr( $settings['_cssid'] ) . '"'; }
		echo ' ' . \RadmanCTShared\Elementor\Attributes\sanitize( $settings['attributes'] ?? '' ) . '>'; $this->render_navigation_settings( $settings ); echo '</div>';
	}
	protected function render_navigation_settings( array $settings ): void { render( $settings, $this->get_id() ); }

}
