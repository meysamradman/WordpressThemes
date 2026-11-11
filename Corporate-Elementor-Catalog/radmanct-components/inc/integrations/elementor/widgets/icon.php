<?php

namespace RadmanCTComponents\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/presentation.php';

class Icon extends \Elementor\Widget_Base {
	public function get_name() { return 'radman-icon'; }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! classic_in_panel(); }
	public function get_title() { return ( __( 'Icon', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-favorite'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function get_style_depends() { return \RadmanCTComponents\Components\style_depends( 'icons' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Icon', 'radmanct-components' ) ) );
		$this->add_control( 'name', array( 'label' => __( 'Icon', 'radmanct-components' ), 'type' => 'rdct-icon-picker', 'default' => 'phone' ) );
		$this->add_control( 'size', array( 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => 24 ) );
		$this->add_control( 'stroke', array( 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => 1 ) );
		$this->add_control( 'label', array( 'label' => __( 'Accessible label', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control( 'mirror', array( 'label' => __( 'Mirror directional icons in RTL', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->end_controls_section();
		$this->start_controls_section( 'icon_style', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		foreach ( array( 'size' => __( 'Icon size', 'radmanct-components' ), 'stroke' => __( 'Stroke width', 'radmanct-components' ), 'icon_align' => __( 'Alignment', 'radmanct-components' ) ) as $key => $label ) { \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'icon', $key, array( 'label' => $label ) ); }
		$this->add_control( 'color', array( 'label' => __( 'Icon color', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .rdct-ico' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings['mirror'] = 'yes' === ( $settings['mirror'] ?? 'yes' );
		rdct_icon( $settings['name'], $settings );
	}
}
