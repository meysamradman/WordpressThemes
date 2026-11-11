<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/icon-box.php';
require_once dirname( __DIR__ ) . '/presentation.php';

class IconBox extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-icon-box'; }
	public function get_title() { return ( __( 'Icon Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-icon-box'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'icon-box', $this->get_settings_for_display() ); }
	protected function register_controls() {
		$fields = \RadmanCTComponents\Components\IconBox\fields();
		$this->start_controls_section( 'content', array( 'label' => __( 'Icon', 'radmanct-components' ) ) );
		$this->add_control( 'icon_type', array( 'label' => $fields['icon_type']['label'], 'type' => Controls::SELECT, 'options' => $fields['icon_type']['options'], 'default' => 'icon' ) );
		$this->add_control( 'icon', array( 'label' => __( 'Icon', 'radmanct-components' ), 'type' => 'rdct-icon-picker', 'condition' => array( 'icon_type' => 'icon' ), 'default' => 'check' ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'radmanct-components' ), 'type' => Controls::MEDIA, 'condition' => array( 'icon_type' => 'image' ) ) );
		$this->add_control( 'layout', array( 'type' => Controls::HIDDEN, 'default' => 'top' ) );
		\RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'icon-box', 'layout', $fields['layout'] );
		$this->end_controls_section();
		$this->start_controls_section( 'title_section', array( 'label' => __( 'Title', 'radmanct-components' ) ) );
		$this->add_control( 'title', array( 'label' => $fields['title']['label'], 'type' => Controls::TEXTAREA, 'rows' => 2, 'default' => $fields['title']['default'] ) );
		$this->add_control( 'title_tag', array( 'label' => $fields['title_tag']['label'], 'type' => Controls::SELECT, 'options' => $fields['title_tag']['options'], 'default' => 'h3' ) );
		$this->end_controls_section();
		$this->start_controls_section( 'description_section', array( 'label' => __( 'Description', 'radmanct-components' ) ) );
		$this->add_control( 'text', array( 'label' => $fields['text']['label'], 'type' => Controls::WYSIWYG ) );
		$this->add_control( 'url', array( 'type' => Controls::HIDDEN, 'default' => '' ) );
		$this->add_control( 'link', array( 'label' => __( 'Link', 'radmanct-components' ), 'type' => Controls::URL, 'options' => array( 'url', 'is_external', 'nofollow' ) ) );
		$this->add_control( 'link_text', array( 'label' => $fields['link_text']['label'], 'type' => Controls::TEXT ) );
		$this->end_controls_section();
		$this->start_controls_section( 'style', array( 'label' => __( 'Icon style', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		$this->add_control( 'shape', array( 'label' => $fields['shape']['label'], 'type' => Controls::SELECT, 'options' => $fields['shape']['options'], 'default' => 'none' ) );
		foreach ( array( 'icon_size', 'icon_padding', 'border_width', 'gap', 'title_gap', 'icon_side', 'vertical_align', 'align' ) as $key ) {
			$this->add_control( $key, array( 'type' => Controls::HIDDEN, 'default' => $fields[ $key ]['default'] ) );
			\RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'icon-box', $key, $fields[ $key ], in_array( $key, array( 'icon_padding', 'border_width' ), true ) ? array( 'condition' => array( 'shape!' => 'none' ) ) : array() );
		}
		$this->start_controls_tabs( 'icon_colors' );
		foreach ( array( 'normal' => array( 'icon_color', 'icon_background', 'border_color' ), 'hover' => array( 'hover_color', 'hover_background' ) ) as $state => $keys ) {
			$this->start_controls_tab( $state, array( 'label' => 'normal' === $state ? __( 'Normal', 'radmanct-components' ) : __( 'Hover', 'radmanct-components' ) ) );
			foreach ( $keys as $key ) { $this->add_control( $key, array( 'label' => $fields[ $key ]['label'], 'type' => Controls::COLOR, 'alpha' => false, 'default' => $fields[ $key ]['default'] ) ); }
			$this->end_controls_tab();
		}
		$this->end_controls_tabs(); $this->end_controls_section();
		$this->start_controls_section( 'typography', array( 'label' => __( 'Title and description', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( array( 'title' => '.rdct-ib__title', 'text' => '.rdct-ib__content' ) as $part => $selector ) { $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => $part . '_typography', 'selector' => '{{WRAPPER}} ' . $selector, 'exclude' => array( 'font_family' ) ) ); }
		$this->end_controls_section();
		$this->start_controls_section( 'animation_section', array( 'label' => __( 'SVG animation', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( array( 'animation', 'animation_target', 'animation_trigger', 'animation_duration' ) as $key ) {
			$control = array( 'label' => $fields[ $key ]['label'], 'default' => $fields[ $key ]['default'], 'type' => 'animation_duration' === $key ? Controls::NUMBER : Controls::SELECT );
			if ( isset( $fields[ $key ]['options'] ) ) { $control['options'] = $fields[ $key ]['options']; }
			if ( 'animation' !== $key ) { $control['condition'] = array( 'animation!' => 'none' ); }
			if ( 'animation_duration' === $key ) { $control += array( 'min' => .2, 'max' => 20, 'step' => .1 ); }
			$this->add_control( $key, $control );
		}
		$this->end_controls_section();
	}
	protected function render() { $s = $this->get_settings_for_display(); $s['image_id'] = (int) ( $s['image']['id'] ?? 0 ); if ( array_key_exists( 'link', $this->get_data( 'settings' ) ?? array() ) && is_array( $s['link'] ?? null ) && isset( $s['link']['url'] ) ) { $s['url'] = $s['link']['url']; $s['new_tab'] = ! empty( $s['link']['is_external'] ); $s['nofollow'] = ! empty( $s['link']['nofollow'] ); } \rdct_icon_box( $s ); }
}
