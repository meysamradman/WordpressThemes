<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/presentation.php';
require_once dirname( __DIR__, 3 ) . '/components/flipbox.php';

class FlipBox extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-flipbox'; }
	public function get_title() { return ( __( 'Flip Box', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-flip-box'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'flipbox', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array( 'rdct-elementor-components' ); }
	protected function register_controls() {
		$fields = \RadmanCTComponents\Components\FlipBox\fields();
		$this->start_controls_section( 'general', array( 'label' => __( 'General', 'radmanct-components' ) ) );
		foreach ( array( 'direction', 'shadow', 'radius' ) as $key ) { $this->add_control( $key, array( 'label' => $fields[ $key ]['label'], 'type' => Controls::SELECT, 'options' => $fields[ $key ]['options'], 'default' => $fields[ $key ]['default'] ) ); }
		$this->add_control( 'height', array( 'type' => Controls::HIDDEN, 'default' => 270 ) );
		\RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'flipbox', 'height', $fields['height'] );
		$this->add_control( 'duration', array( 'label' => $fields['duration']['label'], 'type' => Controls::NUMBER, 'min' => 0, 'max' => 2000, 'default' => 600 ) );
		$this->end_controls_section();
		foreach ( array( 'front', 'back' ) as $side ) {
			$this->start_controls_section( $side, array( 'label' => 'front' === $side ? __( 'Front side', 'radmanct-components' ) : __( 'Back side', 'radmanct-components' ) ) );
			$this->add_control( $side . '_type', array( 'label' => $fields[ $side . '_type' ]['label'], 'type' => Controls::SELECT, 'options' => $fields[ $side . '_type' ]['options'], 'default' => 'html' ) );
			$this->add_control( $side . '_content', array( 'label' => __( 'Content', 'radmanct-components' ), 'type' => Controls::WYSIWYG, 'default' => $fields[ $side . '_content' ]['default'], 'condition' => array( $side . '_type' => 'html' ) ) );
			$this->add_control( $side . '_template', array( 'label' => __( 'Select template', 'radmanct-components' ), 'type' => Controls::SELECT2, 'options' => \RadmanCTComponents\Components\FlipBox\templates(), 'default' => '0', 'condition' => array( $side . '_type' => 'template' ) ) );
			$selector = '{{WRAPPER}} .rdct-fb__' . $side;
			$this->add_group_control( \Elementor\Group_Control_Background::get_type(), array( 'name' => $side . '_bg', 'types' => array( 'classic', 'gradient' ), 'selector' => $selector ) );
			$this->add_control( $side . '_text_color', array( 'label' => __( 'Text color', 'radmanct-components' ), 'type' => Controls::COLOR, 'default' => '#ffffff', 'selectors' => array( $selector => 'color:{{VALUE}};' ) ) );
			$this->add_control( $side . '_overlay_heading', array( 'type' => Controls::HEADING, 'label' => __( 'Background overlay', 'radmanct-components' ) ) );
			$this->add_group_control( \Elementor\Group_Control_Background::get_type(), array( 'name' => $side . '_overlay', 'types' => array( 'classic', 'gradient' ), 'exclude' => array( 'image' ), 'selector' => $selector . ' .rdct-fb__overlay' ) );
			$this->add_control( $side . '_overlay_opacity', array( 'label' => __( 'Overlay opacity', 'radmanct-components' ), 'type' => Controls::NUMBER, 'min' => 0, 'max' => 1, 'step' => .05, 'default' => .4, 'selectors' => array( $selector . ' .rdct-fb__overlay' => 'opacity:{{VALUE}};' ) ) );
			$this->add_responsive_control( $side . '_padding', array( 'label' => __( 'Padding', 'radmanct-components' ), 'type' => Controls::DIMENSIONS, 'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh' ), 'default' => array( 'top' => 40, 'right' => 50, 'bottom' => 40, 'left' => 50, 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( $selector . ' .rdct-fb__inner' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
			$this->end_controls_section();
		}
	}
	protected function render() { \rdct_flipbox( $this->get_settings_for_display() ); }
}
