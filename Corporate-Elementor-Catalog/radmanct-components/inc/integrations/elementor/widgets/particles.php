<?php

namespace RadmanCTComponents\Elementor\Widgets;

use Elementor\Controls_Manager as Controls;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/particles.php';

class Particles extends \Elementor\Widget_Base {
	public function get_name() { return 'rdct-particles'; }
	public function get_title() { return ( __( 'Particles', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-bullet-list'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! $this->show_in_panel(); }
	public function get_keywords() { return array( 'particles', 'background', 'animation', 'canvas' ); }
	public function get_style_depends() { return array( 'rdct-particles' ); }
	public function get_script_depends() { return array( 'rdct-elementor-components' ); }
	protected function register_controls() {
		$fields = \RadmanCTComponents\Components\Particles\fields();
		foreach ( \RadmanCTComponents\Components\Particles\groups() as $group => $label ) {
			$this->start_controls_section( 'rdct_particles_' . $group, array( 'label' => $label ) );
			foreach ( $fields as $key => $field ) {
				if ( $field['group'] !== $group || ! empty( $field['shape'] ) ) { continue; }
				$args = array( 'label' => $field['label'], 'default' => $field['default'], 'type' => Controls::TEXT );
				if ( is_bool( $field['default'] ) ) { $args['type'] = Controls::SWITCHER; $args['return_value'] = 'yes'; $args['default'] = $field['default'] ? 'yes' : ''; }
				elseif ( ! empty( $field['media'] ) ) { $args['type'] = Controls::MEDIA; $args['default'] = array( 'id' => 0, 'url' => '' ); }
				elseif ( ! empty( $field['color'] ) ) { $args['type'] = Controls::COLOR; $args['alpha'] = false; }
				elseif ( isset( $field['options'] ) ) { $args['type'] = Controls::SELECT; $args['options'] = $field['options']; }
				elseif ( isset( $field['min'] ) ) {
					if ( in_array( $key, array( 'opacity', 'anim_opacity_min', 'anim_size_min', 'line_opacity', 'modes_grab_opacity' ), true ) ) {
						$args['type'] = Controls::SLIDER;
						$args['size_units'] = array( 'px' );
						$args['range'] = array( 'px' => array( 'min' => $field['min'], 'max' => $field['max'], 'step' => $field['step'] ?? 1 ) );
						$args['default'] = array( 'unit' => 'px', 'size' => $field['default'] );
					} else { $args['type'] = Controls::NUMBER; $args += array_intersect_key( $field, array_flip( array( 'min', 'max', 'step' ) ) ); }
				}
				foreach ( $field['condition'] ?? array() as $name => $value ) { if ( str_starts_with( $name, 'shape_' ) ) { $args['condition']['shape_type'] = substr( $name, 6 ); } else { $args['condition'][ $name ] = is_bool( $value ) ? ( $value ? 'yes' : '' ) : $value; } }
				$this->add_control( $key, $args );
				if ( 'color_type' === $key ) {
					$repeater = new \Elementor\Repeater();
					$repeater->add_control( 'scolor', array( 'label' => __( 'Color', 'radmanct-components' ), 'type' => Controls::COLOR, 'alpha' => false, 'default' => '#ffffff' ) );
					$this->add_control( 'multi_color_values', array( 'label' => __( 'Multiple colors', 'radmanct-components' ), 'type' => Controls::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ scolor }}}', 'condition' => array( 'color_type' => 'multi_color' ) ) );
					$shapes = array(); foreach ( $fields as $name => $spec ) { if ( ! empty( $spec['shape'] ) ) { $shapes[ substr( $name, 6 ) ] = $spec['label']; } }
					$this->add_control( 'shape_type', array( 'label' => __( 'Shapes', 'radmanct-components' ), 'type' => Controls::SELECT2, 'multiple' => true, 'options' => $shapes, 'default' => array( 'circle' ) ) );
				}
			}
			$this->end_controls_section();
		}
	}
	protected function render() { \rdct_particles( $this->get_settings_for_display() ); }
}
