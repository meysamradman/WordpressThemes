<?php

namespace RadmanCTComponents\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/repeater-controls.php';
require_once dirname( __DIR__, 3 ) . '/components/taxonomy-list.php';
require_once dirname( __DIR__, 3 ) . '/components/archive-search.php';

abstract class ContentUI extends \Elementor\Widget_Base {
	abstract protected function component(): string;
	abstract protected function fields(): array;
	public function get_name() { return 'rdct-' . $this->component(); }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	protected function is_dynamic_content(): bool { return true; }
	public function get_script_depends() { return array(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( $this->component(), $this->get_settings_for_display() ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'radmanct-components' ) ) );
		foreach ( $this->fields() as $key => $field ) { $this->add_control( $key, field_control( $key, $field ) ); }
		$this->end_controls_section();
		$this->start_controls_section( 'appearance', array( 'label' => __( 'Style', 'radmanct-components' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typography', 'exclude' => array( 'font_family' ), 'selector' => '{{WRAPPER}} .' . ( 'taxonomy-list' === $this->component() ? 'rdct-taxonomy' : 'rdct-component-search' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		if ( 'taxonomy-list' === $this->component() ) { rdct_taxonomy_list( $this->get_settings_for_display() ); }
		else { rdct_archive_search( $this->get_settings_for_display() ); }
	}
}

class TaxonomyList extends ContentUI {
	protected function component(): string { return 'taxonomy-list'; }
	protected function fields(): array { return \RadmanCTComponents\Components\TaxonomyList\fields(); }
	public function get_title() { return __( 'Taxonomy list', 'radmanct-components' ); }
	public function get_icon() { return 'eicon-post-list'; }
}

class ArchiveSearch extends ContentUI {
	protected function component(): string { return 'archive-search'; }
	protected function fields(): array { return \RadmanCTComponents\Components\ArchiveSearch\fields(); }
	public function get_title() { return __( 'Content search', 'radmanct-components' ); }
	public function get_icon() { return 'eicon-search'; }
}
