<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/sticky-sections.php';
require_once __DIR__ . '/repeater-controls.php';

class StickySections extends \Elementor\Widget_Base {
	protected const MODE = '';
	public function get_name() { return static::MODE ? 'rdct-sticky-' . static::MODE : 'rdct-sticky-sections'; }
	public function get_title() { return ( static::MODE ? \RadmanCTComponents\Components\StickySections\fields()['mode']['options'][ static::MODE ] : __( 'Sticky Sections', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-slider-vertical'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return '' !== static::MODE; }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { if ( $this->is_type_instance() ) { return array(); } $settings = $this->get_settings_for_display(); if ( static::MODE ) { $settings['mode'] = static::MODE; } return \RadmanCTComponents\Components\style_depends( 'sticky-sections', $settings ); }
	public function get_script_depends() { return array( 'rdct-elementor-components' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'radmanct-components' ) ) );
		$repeater = new \Elementor\Repeater(); add_repeater_fields( $repeater, \RadmanCTComponents\Components\StickySections\item_fields() );
		$this->add_control( 'items', array( 'label' => __( 'Items', 'radmanct-components' ), 'type' => Controls::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'default' => array( array( 'title' => __( 'First item', 'radmanct-components' ) ), array( 'title' => __( 'Second item', 'radmanct-components' ) ), array( 'title' => __( 'Third item', 'radmanct-components' ) ) ) ) );
		foreach ( \RadmanCTComponents\Components\StickySections\fields() as $key => $field ) {
			if ( 'mode' === $key && static::MODE ) { continue; }
			if ( static::MODE && isset( $field['condition']['mode'] ) ) { if ( ! in_array( static::MODE, $field['condition']['mode'], true ) ) { continue; } unset( $field['condition']['mode'] ); }
			$this->add_control( $key, field_control( $key, $field ) );
		}
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display(); if ( static::MODE ) { $s['mode'] = static::MODE; } $rows = is_array( $s['items'] ?? null ) ? $s['items'] : array();
		foreach ( $rows as &$row ) { $row['image_id'] = is_array( $row['image_id'] ?? null ) ? (int) ( $row['image_id']['id'] ?? 0 ) : 0; } unset( $row );
		\rdct_sticky_sections( $rows, $s );
	}
}

class StickyStack extends StickySections { protected const MODE = 'stack'; }
class StickyOverlap extends StickySections { protected const MODE = 'overlap'; }
class ServicePanels extends StickySections { protected const MODE = 'services'; }
class ZoomCards extends StickySections { protected const MODE = 'zoom'; }
class StickySplit extends StickySections { protected const MODE = 'split'; }
