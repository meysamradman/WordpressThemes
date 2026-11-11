<?php
namespace RadmanCTComponents\Elementor\Widgets;
use Elementor\Controls_Manager as Controls;
defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/timeline.php';
require_once __DIR__ . '/repeater-controls.php';
class Timeline extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-timeline'; }
	public function get_title() { return ( __( 'Timeline', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-time-line'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'timeline', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array(); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'radmanct-components' ) ) );
		$repeater = new \Elementor\Repeater();
		add_repeater_fields( $repeater, \RadmanCTComponents\Components\Timeline\item_fields() );
		$this->add_control( 'items', array( 'label' => __( 'Items', 'radmanct-components' ), 'type' => Controls::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'default' => array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ) ) ) );
		$this->end_controls_section();
		$this->start_controls_section( 'appearance', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => Controls::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Timeline\fields() as $key => $field ) { $this->add_control( $key, field_control( $key, $field ) ); }
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'title_type', 'selector' => '{{WRAPPER}} .rdct-timeline__title', 'exclude' => array( 'font_family' ) ) );
		$this->end_controls_section();
	}
	protected function render() { $s = $this->get_settings_for_display(); rdct_timeline( is_array( $s['items'] ?? null ) ? $s['items'] : array(), $s ); }
}
