<?php
namespace RadmanCTComponents\Elementor\Widgets;
use Elementor\Controls_Manager as Controls;
defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/accordion.php';
require_once __DIR__ . '/repeater-controls.php';
class Accordion extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-accordion'; }
	public function get_title() { return ( __( 'Accordion', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-accordion'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'accordion', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array(); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'radmanct-components' ) ) );
		$repeater = new \Elementor\Repeater();
		add_repeater_fields( $repeater, \RadmanCTComponents\Components\Accordion\item_fields() );
		$this->add_control( 'items', array( 'label' => __( 'Items', 'radmanct-components' ), 'type' => Controls::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'default' => array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ) ) ) );
		$this->end_controls_section();
		$this->start_controls_section( 'appearance', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Accordion\fields() as $key => $field ) { $this->add_control( $key, field_control( $key, $field ) ); }
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'title_type', 'selector' => '{{WRAPPER}} .rdct-accordion__title', 'exclude' => array( 'font_family' ) ) );
		$this->end_controls_section();
	}
	protected function render() { $s = $this->get_settings_for_display(); rdct_accordion( is_array( $s['items'] ?? null ) ? $s['items'] : array(), $s ); }
}
