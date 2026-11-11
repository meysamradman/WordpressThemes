<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/testimonial/fields.php';
require_once __DIR__ . '/repeater-controls.php';
require_once dirname( __DIR__ ) . '/presentation.php';

class Testimonial extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-testimonial'; }
	public function get_title() { return ( __( 'Customer review', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-testimonial'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'testimonial', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array(); }
	protected function register_controls() {
		$fields = \RadmanCTComponents\Components\Testimonial\widget_fields();
		$this->start_controls_section( 'review', array( 'label' => __( 'Customer review', 'radmanct-components' ) ) );
		foreach ( \RadmanCTComponents\Components\Testimonial\item_fields() as $key => $field ) {
			$control = field_control( $key, $fields[ $key ] );
			if ( 'link' === $key ) { $control['type'] = Controls::URL; $control['default'] = array( 'url' => '' ); $control['options'] = array( 'url', 'is_external' ); }
			$this->add_control( $key, $control );
		}
		$this->end_controls_section();
		$this->start_controls_section( 'appearance', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Testimonial\fields() as $key => $field ) {
			if ( isset( \RadmanCTComponents\Components\Testimonial\presentation()[ $key ] ) ) { \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'testimonial', $key, $field ); }
			else { $this->add_control( $key, field_control( $key, $field ) ); }
		}
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		\rdct_testimonial( $settings, $settings );
	}
}
