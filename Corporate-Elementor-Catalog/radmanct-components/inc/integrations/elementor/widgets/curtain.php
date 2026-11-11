<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/curtain.php';
require_once dirname( __DIR__, 3 ) . '/components/template-library.php';
require_once dirname( __DIR__ ) . '/presentation.php';

require_once __DIR__ . '/repeater-controls.php';

class Curtain extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-curtain'; }
	public function get_title() { return ( __( 'Expandable Panels', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-accordion'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'curtain', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array( 'rdct-elementor-components' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'radmanct-components' ) ) );
		$repeater = new \Elementor\Repeater();
		foreach ( array( 'content', 'normal', 'active' ) as $group ) {
			if ( 'normal' === $group ) { $repeater->start_controls_tabs( 'item_states' ); }
			if ( 'content' !== $group ) { $repeater->start_controls_tab( 'item_' . $group, array( 'label' => 'normal' === $group ? __( 'Normal', 'radmanct-components' ) : __( 'Active', 'radmanct-components' ) ) ); }
		foreach ( \RadmanCTComponents\Components\Curtain\item_fields() as $key => $field ) {
			if ( ( $field['group'] ?? 'content' ) !== $group ) { continue; }
			$control = field_control( $key, $field );
			$repeater->add_control( $key, $control );
		}
			if ( 'content' !== $group ) { $repeater->end_controls_tab(); }
			if ( 'active' === $group ) { $repeater->end_controls_tabs(); }
		}
		$this->add_control( 'items', array( 'label' => __( 'Items', 'radmanct-components' ), 'type' => Controls::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'default' => array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ), array( 'title' => __( 'Third item', 'radmanct-components' ) ) ) ) );
		foreach ( \RadmanCTComponents\Components\Curtain\fields() as $key => $field ) {
			if ( isset( \RadmanCTComponents\Components\Presentation\controls( 'curtain' )[ $key ] ) ) { $this->add_control( $key, array( 'type' => Controls::HIDDEN, 'default' => $field['default'] ) ); continue; }
			$control = array( 'label' => $field['label'], 'default' => $field['default'], 'type' => isset( $field['options'] ) ? Controls::SELECT : Controls::NUMBER );
			foreach ( array( 'options', 'min', 'max', 'condition' ) as $property ) { if ( isset( $field[ $property ] ) ) { $control[ $property ] = $field[ $property ]; } }
			$this->add_control( $key, $control );
		}
		$this->end_controls_section();
		$this->start_controls_section( 'style', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Presentation\controls( 'curtain' ) as $key => $spec ) { \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'curtain', $key, \RadmanCTComponents\Components\Curtain\fields()[ $key ] ); }
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'title_type', 'label' => __( 'Title', 'radmanct-components' ), 'selector' => '{{WRAPPER}} .rdct-curtain__title, {{WRAPPER}} .rdct-curtain__trigger', 'exclude' => array( 'font_family' ) ) );
		$this->add_responsive_control( 'radius', array( 'label' => __( 'Border radius', 'radmanct-components' ), 'type' => Controls::SLIDER, 'size_units' => array( 'px', 'rem' ), 'selectors' => array( '{{WRAPPER}} .rdct-curtain__item' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items = is_array( $settings['items'] ?? null ) ? $settings['items'] : array();
		foreach ( $items as &$item ) {
			foreach ( array( 'image_id', 'background_id' ) as $key ) { $item[ $key ] = is_array( $item[ $key ] ?? null ) ? (int) ( $item[ $key ]['id'] ?? 0 ) : 0; }
		}
		unset( $item );
		rdct_curtain( $items, $settings );
	}
}
