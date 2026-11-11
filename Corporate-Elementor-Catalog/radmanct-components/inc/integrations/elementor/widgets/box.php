<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/presentation.php';
require_once dirname( __DIR__, 3 ) . '/components/box.php';

class Box extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-box'; }
	public function get_title() { return ( __( 'Content Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-info-box'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'box', $this->get_settings_for_display() ); }
	public function get_script_depends() { return \RadmanCTComponents\Components\script_depends( 'box', $this->is_type_instance() ? array() : $this->get_settings_for_display(), $this->is_type_instance() ); }
	protected function register_controls() {
		foreach ( array( 'content', 'appearance' ) as $section ) {
		$this->start_controls_section( $section, array( 'label' => 'content' === $section ? __( 'Content', 'radmanct-components' ) : __( 'Appearance', 'radmanct-components' ), 'tab' => 'content' === $section ? Controls::TAB_CONTENT : Controls::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Box\fields() as $key => $field ) {
			$is_style = ! empty( $field['color'] ) || isset( \RadmanCTComponents\Components\Presentation\controls( 'box' )[ $key ] ) || in_array( $key, array( 'padding', 'label_padding', 'radius', 'height', 'content_alignment' ), true );
			if ( ( 'appearance' === $section ) !== $is_style ) { continue; }
			if ( isset( \RadmanCTComponents\Components\Presentation\controls( 'box' )[ $key ] ) ) { $this->add_control( $key, array( 'type' => Controls::HIDDEN, 'default' => $field['default'] ) ); \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'box', $key, $field ); continue; }
			$type = isset( $field['options'] ) || 'icon' === $key ? Controls::SELECT : ( is_bool( $field['default'] ) ? Controls::SWITCHER : ( isset( $field['min'] ) ? Controls::NUMBER : ( ! empty( $field['color'] ) ? Controls::COLOR : ( ! empty( $field['multiline'] ) ? Controls::TEXTAREA : Controls::TEXT ) ) ) );
			$control = array( 'label' => $field['label'], 'type' => ! empty( $field['rich'] ) ? Controls::WYSIWYG : $type, 'default' => is_bool( $field['default'] ) ? ( $field['default'] ? 'yes' : '' ) : $field['default'] );
			foreach ( array( 'min', 'max', 'options', 'description' ) as $property ) { if ( isset( $field[ $property ] ) ) { $control[ $property ] = $field[ $property ]; } }
			if ( isset( $field['styles'] ) ) { $control['condition'] = array( 'template' => $field['styles'] ); }
			if ( 'icon' === $key ) { $control['type'] = 'rdct-icon-picker'; $control['options'] = array( '' => __( 'None', 'radmanct-components' ) ) + \RadmanCTComponents\Icons\labels(); }
			if ( ! empty( $field['color'] ) ) { $control['alpha'] = false; }
			$this->add_control( $key, $control );
			if ( 'label' === $key ) { $this->add_control( 'image', array( 'label' => __( 'Image', 'radmanct-components' ), 'type' => Controls::MEDIA, 'condition' => array( 'template!' => 's10' ) ) ); }
		}
		$this->end_controls_section();
		}
		$this->start_controls_section( 'typography', array( 'label' => __( 'Typography', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( array( 'title', 'label', 'subtitle', 'text' ) as $part ) { $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => $part . '_typography', 'selector' => '{{WRAPPER}} .rdct-box__' . $part, 'exclude' => array( 'font_family' ) ) ); }
		$this->add_responsive_control( 'content_dimensions', array( 'label' => __( 'Content padding', 'radmanct-components' ), 'type' => Controls::DIMENSIONS, 'size_units' => array( 'px', 'rem' ), 'selectors' => array( '{{WRAPPER}} .rdct-box__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'label_dimensions', array( 'label' => __( 'Label padding', 'radmanct-components' ), 'type' => Controls::DIMENSIONS, 'size_units' => array( 'px', 'rem' ), 'selectors' => array( '{{WRAPPER}} .rdct-box__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'height_responsive', array( 'label' => __( 'Height', 'radmanct-components' ), 'type' => Controls::SLIDER, 'size_units' => array( 'px', 'vh', 'rem' ), 'selectors' => array( '{{WRAPPER}} .rdct-box' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings['image_id'] = (int) ( $settings['image']['id'] ?? 0 );
		rdct_box( $settings );
	}
}
