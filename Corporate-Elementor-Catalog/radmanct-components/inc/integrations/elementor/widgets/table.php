<?php

namespace RadmanCTComponents\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__ ) . '/presentation.php';
require_once dirname( __DIR__, 3 ) . '/components/table.php';

class Table extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-table'; }
	public function get_title() { return ( __( 'Table', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-table'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'table', $this->get_settings_for_display() ); }
	public function get_script_depends() { return array(); }
	protected function register_controls() {
		$this->start_controls_section( 'table', array( 'label' => __( 'Table content', 'radmanct-components' ) ) );
		$this->add_control( 'data', array( 'type' => 'rdct-table-grid', 'label_block' => true, 'default' => \RadmanCTComponents\Components\Table\sample() ) );
		foreach ( \RadmanCTComponents\Components\Table\fields() as $key => $field ) {
			if ( isset( \RadmanCTComponents\Components\Presentation\controls( 'table' )[ $key ] ) ) { $this->add_control( $key, array( 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => $field['default'] ) ); continue; }
			$type = isset( $field['options'] ) ? 'select' : ( is_bool( $field['default'] ) ? 'switcher' : ( isset( $field['min'] ) ? 'number' : ( ! empty( $field['color'] ) ? 'color' : 'text' ) ) );
			$control = array( 'type' => $type, 'label' => $field['label'], 'default' => is_bool( $field['default'] ) ? ( $field['default'] ? 'yes' : '' ) : $field['default'] );
			foreach ( array( 'options', 'min', 'max' ) as $property ) { if ( isset( $field[ $property ] ) ) { $control[ $property ] = $field[ $property ]; } }
			if ( ! empty( $field['color'] ) ) { $control['alpha'] = false; }
			$this->add_control( $key, $control );
		}
		$this->end_controls_section();
		$this->start_controls_section( 'typography', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		foreach ( \RadmanCTComponents\Components\Presentation\controls( 'table' ) as $key => $spec ) { \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'table', $key, \RadmanCTComponents\Components\Table\fields()[ $key ] ); }
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'text_type', 'selector' => '{{WRAPPER}} .rdct-tb table', 'exclude' => array( 'font_family' ) ) );
		$this->end_controls_section();
	}
	protected function render() { $s = $this->get_settings_for_display(); rdct_table( is_array( $s['data'] ?? null ) ? $s['data'] : array(), $s ); }
}
