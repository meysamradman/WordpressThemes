<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/image.php';

class Image extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-image'; }
	public function get_title() { return ( __( 'Image', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-image'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'image', $this->get_settings_for_display() ); }
	public function get_script_depends() { return \RadmanCTComponents\Components\script_depends( 'image', $this->is_type_instance() ? array() : $this->get_settings_for_display(), $this->is_type_instance() ); }
	protected function register_controls() {
		foreach ( \RadmanCTComponents\Components\Image\groups() as $group => $label ) {
			if ( in_array( $group, array( 'laptop', 'tablet', 'mobile' ), true ) ) { continue; }
			$this->start_controls_section( 'rdct_image_' . $group, array( 'label' => $label, 'tab' => in_array( $group, array( 'general', 'lightbox' ), true ) ? Controls::TAB_CONTENT : Controls::TAB_STYLE ) );
			foreach ( \RadmanCTComponents\Components\Image\fields() as $key => $field ) {
				if ( $field['group'] !== $group ) { continue; }
				if ( in_array( $key, array( 'new_tab', 'nofollow', 'label_font', 'label_size', 'label_weight', 'label_spacing', 'label_line_height' ), true ) ) { continue; }
				$control = array( 'label' => $field['label'], 'default' => $field['default'], 'type' => Controls::TEXT );
				if ( isset( $field['description'] ) ) { $control['description'] = $field['description']; }
				if ( isset( $field['condition'] ) ) { foreach ( $field['condition'] as $name => $value ) { $control['condition'][ $name ] = true === $value ? 'yes' : $value; } }
				if ( ! empty( $field['length'] ) ) {
					$control['type'] = Controls::SLIDER; $control['size_units'] = array( '%', 'px', 'vw', 'vh', 'em', 'rem' );
					$control['range'] = array( '%' => array( 'min' => 0, 'max' => 100 ), 'px' => array( 'min' => 0, 'max' => 4096 ), 'vw' => array( 'min' => 0, 'max' => 100 ), 'vh' => array( 'min' => 0, 'max' => 100 ), 'em' => array( 'min' => 0, 'max' => 100 ), 'rem' => array( 'min' => 0, 'max' => 100 ) );
					$control['default'] = preg_match( '/^([0-9.]+)(.*)$/', $field['default'], $match ) ? array( 'size' => (float) $match[1], 'unit' => $match[2] ) : array( 'unit' => 'px' );
					$variable = array( 'width' => 'w', 'max_width' => 'mw', 'height' => 'h', 'container_width' => 'cw' )[ $key ];
					$control['selectors'] = array( '{{WRAPPER}} .rdct-im' => '--rdct-im-' . $variable . ':{{SIZE}}{{UNIT}};' );
					$this->add_responsive_control( $key, $control ); continue;
				}
				if ( 'url' === $key ) { $control['type'] = Controls::URL; $control['default'] = array( 'url' => '' ); }
				elseif ( ! empty( $field['media'] ) ) { $control['type'] = Controls::MEDIA; unset( $control['default'] ); $key = 'image_id' === $key ? 'image' : 'overlay_image'; }
				elseif ( isset( $field['options'] ) ) { $control['type'] = Controls::SELECT; $control['options'] = $field['options']; }
				elseif ( ! empty( $field['color'] ) ) { $control['type'] = Controls::COLOR; }
				elseif ( is_bool( $field['default'] ) ) { $control['type'] = Controls::SWITCHER; $control['default'] = ''; $control['return_value'] = 'yes'; }
				elseif ( isset( $field['min'] ) ) { $control['type'] = Controls::NUMBER; $control['min'] = $field['min']; $control['max'] = $field['max']; $control['step'] = 0.01; }
				elseif ( ! empty( $field['multiline'] ) ) { $control['type'] = Controls::TEXTAREA; }
				if ( in_array( $key, array( 'align', 'fit' ), true ) ) { $control['selectors'] = array( '{{WRAPPER}} .rdct-im' => '--rdct-im-' . ( 'align' === $key ? 'a' : 'f' ) . ':{{VALUE}};' ); $this->add_responsive_control( $key, $control ); }
				else { $this->add_control( $key, $control ); }
			}
			if ( 'label' === $group ) { $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'label_typography', 'selector' => '{{WRAPPER}} .rdct-im__label' ) ); }
			$this->end_controls_section();
		}
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		foreach ( array( '', '_laptop', '_tablet', '_mobile' ) as $suffix ) { foreach ( array( 'width', 'max_width', 'height', 'container_width' ) as $key ) { $value = $s[ $key . $suffix ] ?? null; if ( is_array( $value ) ) { $s[ $key . $suffix ] = isset( $value['size'] ) && '' !== $value['size'] ? $value['size'] . ( $value['unit'] ?? 'px' ) : ''; } } }
		$link = $s['url'] ?? array(); if ( is_array( $link ) ) { $s['url'] = $link['url'] ?? ''; $s['new_tab'] = ! empty( $link['is_external'] ); $s['nofollow'] = ! empty( $link['nofollow'] ); }
		$s['image_id'] = (int) ( $s['image']['id'] ?? 0 ); $s['overlay_image_id'] = (int) ( $s['overlay_image']['id'] ?? 0 );
		\rdct_image( $s );
	}
}
