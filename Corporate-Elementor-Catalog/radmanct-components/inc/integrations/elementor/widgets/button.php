<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/button.php';
require_once dirname( __DIR__ ) . '/presentation.php';

class Button extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-button'; }
	public function get_title() { return ( __( 'Button', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'button', $this->get_settings_for_display() ); }
	public function get_script_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\script_depends( 'button', $this->get_settings_for_display() ); }
	protected function register_controls() {
		$fields = \RadmanCTComponents\Components\Button\fields();
		$groups = array();
		foreach ( $fields as $key => $field ) { if ( ! in_array( $key, array( 'url', 'new_tab', 'nofollow', 'font_weight', 'font_style', 'text_transform', 'text_decoration', 'line_height', 'line_height_unit', 'letter_spacing', 'letter_spacing_unit', 'word_spacing', 'word_spacing_unit' ), true ) ) { $groups[ $field['group'] ][] = $key; } }
		foreach ( $groups as $group => $keys ) {
			$titles = \RadmanCTComponents\Components\Button\groups();
			$this->start_controls_section( 'button_' . $group, array( 'label' => $titles[ $group ], 'tab' => 'content' === $group ? Controls::TAB_CONTENT : Controls::TAB_STYLE ) );
			foreach ( $keys as $key ) {
				if ( str_ends_with( $key, '_unit' ) ) { continue; }
				$field = $fields[ $key ];
				$control = array( 'label' => $field['label'], 'default' => 'duration' === $key ? '' : $field['default'] );
				if ( isset( $field['condition'] ) ) { $control['condition'] = $field['condition']; }
				if ( isset( \RadmanCTComponents\Components\Button\presentation()[ $key ] ) ) {
					$this->add_control( $key, array( 'type' => Controls::HIDDEN, 'default' => $field['default'] ) );
					\RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'button', $key, $field, isset( $field['condition'] ) ? array( 'condition' => $field['condition'] ) : array() ); continue;
				}
				if ( isset( $field['condition'] ) ) { $control['condition'] = $field['condition']; }
				if ( ! empty( $field['media'] ) ) { $control['type'] = Controls::MEDIA; $control['default'] = array( 'id' => 0 ); }
				elseif ( 'icon' === $key ) { $control['type'] = 'rdct-icon-picker'; }
				elseif ( isset( $field['options'] ) ) { $control['type'] = Controls::SELECT; $control['options'] = $field['options']; }
				elseif ( ! empty( $field['color'] ) ) { $control['type'] = Controls::COLOR; }
				elseif ( is_bool( $field['default'] ) ) { $control['type'] = Controls::SWITCHER; $control['return_value'] = 'yes'; }
				elseif ( isset( $field['min'] ) ) { $control += array( 'type' => Controls::NUMBER, 'min' => $field['min'], 'max' => $field['max'], 'step' => .1 ); }
				else { $control['type'] = Controls::TEXT; }
				$this->add_control( $key, $control );
			}
			if ( 'content' === $group ) { $this->add_control( 'link', array( 'label' => __( 'Link', 'radmanct-components' ), 'type' => Controls::URL, 'default' => array( 'url' => '#' ), 'options' => array( 'url', 'is_external', 'nofollow' ), 'condition' => array( 'tag' => 'a' ) ) ); }
			if ( 'appearance' === $group ) { $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .rdct-button', 'exclude' => array( 'font_family', 'font_size' ) ) ); }
			$this->end_controls_section();
		}

	}
	protected function render() { \rdct_button( $this->get_settings_for_display() ); }
}
