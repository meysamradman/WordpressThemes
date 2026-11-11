<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;

class Classic extends \Elementor\Widget_Base {
	protected static function control_fields(): array { return fields(); }
	public function get_name() { return 'rdct-navigation'; }
	public function show_in_panel() { return true; }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_title() { return ( __( 'Navigation', 'radmanct' ) ); }
	public function get_icon() { return 'eicon-nav-menu'; }
	public function get_categories() { return array( defined( 'RDCT_COMPONENTS_FILE' ) ? 'radmanct-components' : 'general' ); }
	public function get_script_depends() { return array( 'rdct-elementor-navigation' ); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : styles( $this->get_settings_for_display() ); }
	protected function register_controls() {
		foreach ( array( 'menu' => __( 'Menu', 'radmanct' ), 'panel' => __( 'Panel layout', 'radmanct' ), 'style' => __( 'Style', 'radmanct' ) ) as $group => $label ) {
			if ( ! array_filter( static::control_fields(), static fn( $field ) => $field['group'] === $group ) ) { continue; }
			$this->start_controls_section( 'rdct_nav_' . $group, array( 'label' => $label, 'tab' => 'style' === $group ? \Elementor\Controls_Manager::TAB_STYLE : \Elementor\Controls_Manager::TAB_CONTENT ) );
			foreach ( static::control_fields() as $key => $field ) {
				if ( $field['group'] !== $group ) { continue; }
				$control = array( 'label' => $field['label'], 'default' => $field['default'] );
				if ( 'panelImageId' === $key ) { $this->add_control( 'panelImage', array( 'label' => $field['label'], 'type' => \Elementor\Controls_Manager::MEDIA, 'media_types' => array( 'image' ) ) ); continue; }
				if ( isset( $field['options'] ) || isset( $field['choices'] ) ) { $control += array( 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $field['options'] ?? field_choices( $key, $field ) ); }
				elseif ( is_bool( $field['default'] ) ) { $control['type'] = \Elementor\Controls_Manager::SWITCHER; $control['default'] = ''; }
				elseif ( ! empty( $field['color'] ) ) { $control += array( 'type' => \Elementor\Controls_Manager::COLOR, 'alpha' => false ); }
				elseif ( is_int( $field['default'] ) ) { $control += array( 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => $field['min'], 'max' => $field['max'], 'step' => 1 ); }
				else { $control['type'] = \Elementor\Controls_Manager::TEXT; }
				if ( isset( $field['condition'] ) ) { $control['condition'] = $field['condition']; }
				$this->add_control( $key, $control );
			}
			if ( 'style' === $group ) { $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'rdct_nav_type', 'selector' => '{{WRAPPER}} .rdct-menu > li > a', 'exclude' => array( 'font_family' ) ) ); }
			$this->end_controls_section();
		}

	}
	protected function render() { render( $this->get_settings_for_display(), $this->get_id() ); }
}
