<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function ld_text_reveal_v2_enabled() {
	return function_exists( 'liquid_helper' ) && liquid_helper()->get_theme_option( 'enable_text_reveal_v2' ) === 'on';
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class LD_Text_Reveal extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ld_text_reveal';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Liquid Text Reveal', 'hub-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter lqd-element';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'hub-core' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'text', 'reveal', 'animation' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'Text Reveal', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		ld_el_advanced_text( $this );

		$this->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 6,
				'default' => esc_html__( 'Add Your Text Here', 'hub-elementor-addons' ),
				'placeholder' => esc_html__( 'Add Your Text Here', 'hub-elementor-addons' ),
				'condition' => [
					'advanced_text_enable!' => 'yes',
				]
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => esc_html__( 'HTML Tag', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'initialOpacity',
			[
				'label' => esc_html__( 'Initial opacity', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0.2,
				],
                'render_type' => 'template',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .lqd-text-reveal-el:not(.split-text-applied), {{WRAPPER}} .lqd-text-reveal-el figure, {{WRAPPER}} .lqd-text-reveal-el .lqd-chars' => 'opacity: {{SIZE}};',
				]
			]
		);

		$this->add_control(
			'finalOpacity',
			[
				'label' => esc_html__( 'Final opacity', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
                'render_type' => 'template',
			]
		);

		$this->add_control(
			'startTrigger',
			[
				'label' => esc_html__( 'Start trigger', 'hub-elementor-addons' ),
				'description' => __( 'Default is <b>top 70%</b> which means when top of the element hits <b>70% down</b> from <b>top</b> of the viewport.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'top 70%', 'hub-elementor-addons' ),
				'placeholder' => esc_html__( 'top 70%', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'endTrigger',
			[
				'label' => esc_html__( 'End trigger', 'hub-elementor-addons' ),
				'description' => __( 'Default is <b>bottom bottom</b> which means when <b>bottom</b> of the element hits the <b>bottom</b> of the viewport.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'bottom bottom', 'hub-elementor-addons' ),
				'placeholder' => esc_html__( 'bottom bottom', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'startFrom',
			[
				'label' => esc_html__( 'Start from', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'start',
				'options' => [
					'start' => esc_html__( 'Start', 'hub-elementor-addons' ),
					'end' => esc_html__( 'End', 'hub-elementor-addons' ),
					'center' => esc_html__( 'Center', 'hub-elementor-addons' ),
					'edges' => esc_html__( 'Edges', 'hub-elementor-addons' ),
					'random' => esc_html__( 'Random', 'hub-elementor-addons' ),
				],
			]
		);

		if ( ld_text_reveal_v2_enabled() ) {
			$v2_args = array(
				'prefix' => 'lqd_text_reveal',
				'condition' => array(),
				'render_type' => 'template',
			);
			$this->add_control(
				'lqd_text_reveal_split',
				array(
					'label' => __( 'Split', 'hub-elementor-addons' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'chars',
					'options' => array(
						'chars' => __( 'Characters', 'hub-elementor-addons' ),
						'words' => __( 'Words', 'hub-elementor-addons' ),
					),
					'render_type' => 'template',
				)
			);
			ld_el_parallax_register_steps_switch( $this, $v2_args );
			ld_el_parallax_register_starting_style( $this, array_merge( $v2_args, array(
				'color' => true,
				'condition' => array(
					'lqd_text_reveal_steps_enable' => 'yes',
				),
			) ) );
			ld_el_parallax_register_steps_fields( $this, array_merge( $v2_args, array(
				'color' => true,
				'condition' => array(
					'lqd_text_reveal_steps_enable' => 'yes',
				),
			) ) );
			ld_el_parallax_register_apply_on( $this, array_merge( $v2_args, array(
				'description' => __( '"On" uses the "Apply on" breakpoints in Theme Options → Features → Text Reveal. Turn off to choose breakpoints for this element.', 'hub-elementor-addons' ),
			) ) );
			$this->update_control( 'startFrom', array(
				'render_type' => 'template',
			) );
			foreach ( array( 'initialOpacity', 'finalOpacity', 'startTrigger', 'endTrigger' ) as $control_id ) {
				$this->update_control( $control_id, array(
					'render_type' => 'template',
					'condition' => array(
						'lqd_text_reveal_steps_enable!' => 'yes',
					),
				) );
			}
		}

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .lqd-text-reveal-el',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-text-reveal-el' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'hub-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'hub-elementor-addons' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'hub-elementor-addons' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'hub-elementor-addons' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justify', 'hub-elementor-addons' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-text-reveal-el' => 'text-align: {{VALUE}}',
				],
				'toggle' => true,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$tag = Utils::validate_html_tag( $settings['tag'] );
		$attributes = $this->text_reveal_attributes( $settings );

		$this->add_render_attribute( 'wrapper', $attributes );
		$this->print_step_opacity_fallback( $attributes );

		printf( '<%1$s %2$s>%3$s</%1$s>', $tag, $this->get_render_attribute_string( 'wrapper' ), $settings['advanced_text_enable'] === 'yes' ? ld_el_get_advanced_text( $this ) : wp_kses_post( $settings['text'] ) );

	}

	protected function print_step_opacity_fallback( $attributes ) {
		$opacity = null;
		if ( ! empty( $attributes['data-parallax-from'] ) ) {
			$from = json_decode( $attributes['data-parallax-from'], true );
			if ( is_array( $from ) && isset( $from['opacity'] ) && is_numeric( $from['opacity'] ) ) {
				$opacity = $from['opacity'];
			}
		}
		if ( null === $opacity && ! empty( $attributes['data-parallax-steps'] ) ) {
			$steps = json_decode( $attributes['data-parallax-steps'], true );
			if ( is_array( $steps ) && isset( $steps[0]['values']['opacity'] ) && is_numeric( $steps[0]['values']['opacity'] ) ) {
				$opacity = $steps[0]['values']['opacity'];
			}
		} elseif ( null === $opacity && 'lqd-words' === $this->text_reveal_split_class( $attributes ) && ! empty( $attributes['data-parallax-from'] ) ) {
			$from = json_decode( $attributes['data-parallax-from'], true );
			if ( is_array( $from ) && isset( $from['opacity'] ) && is_numeric( $from['opacity'] ) ) {
				$opacity = $from['opacity'];
			}
		}
		if ( null === $opacity ) {
			return;
		}
		$scope = '.elementor-element-' . $this->get_id() . ' .lqd-text-reveal-el';
		printf(
			'<style>%1$s:not(.split-text-applied),%1$s figure,%1$s .%2$s{opacity:%3$s}</style>',
			esc_attr( $scope ),
			esc_attr( $this->text_reveal_split_class( $attributes ) ),
			esc_attr( (string) (float) $opacity )
		);
	}

	protected function text_reveal_attributes( $settings ) {
		$attributes = array(
			'class' => 'lqd-text-reveal-el mt-0 mb-0',
			'data-split-text' => 'true',
			'data-parallax' => 'true',
		);
		$v2 = ld_text_reveal_v2_enabled();
		$steps_on = $v2 && isset( $settings['lqd_text_reveal_steps_enable'] ) && 'yes' === $settings['lqd_text_reveal_steps_enable'];
		$split_words = $v2 && isset( $settings['lqd_text_reveal_split'] ) && 'words' === $settings['lqd_text_reveal_split'];
		$stagger = array(
			'from' => isset( $settings['startFrom'] ) ? $settings['startFrom'] : 'start',
			'each' => 1,
		);
		$attributes['data-split-options'] = wp_json_encode( array(
			'type' => $split_words ? 'words' : 'words, chars',
		) );
		$options = array(
			'disableOnMobile' => false,
			'parallaxTargets' => ( $split_words ? '.lqd-words' : '.lqd-chars' ) . ', figure',
			'start' => ! empty( $settings['startTrigger'] ) ? $settings['startTrigger'] : 'top 70%',
			'end' => ! empty( $settings['endTrigger'] ) ? $settings['endTrigger'] : 'bottom bottom',
			'scrub' => 3,
			'skipWillChange' => true,
			'stagger' => $stagger,
		);

		if ( $steps_on ) {
			unset( $options['start'], $options['end'] );
			$attributes['data-parallax-from'] = wp_json_encode( ld_parallax_transform_map( $settings, 'lqd_text_reveal_from_' ) );
			$items = ( isset( $settings['lqd_text_reveal_steps'] ) && is_array( $settings['lqd_text_reveal_steps'] ) ) ? $settings['lqd_text_reveal_steps'] : array();
			$steps = array();
			foreach ( $items as $item ) {
				$steps[] = ld_parallax_step_payload( $item, 'lqd_text_reveal_step_' );
			}
			$attributes['data-parallax-options'] = wp_json_encode( $options );
			$attributes['data-parallax-steps'] = wp_json_encode( $steps );
		} else {
			unset( $options['stagger'] );
			$initial_opacity = isset( $settings['initialOpacity']['size'] ) ? $settings['initialOpacity']['size'] : 0.2;
			$final_opacity = isset( $settings['finalOpacity']['size'] ) ? $settings['finalOpacity']['size'] : 1;
			$attributes['data-parallax-options'] = wp_json_encode( $options );
			$attributes['data-parallax-from'] = wp_json_encode( array( 'opacity' => $initial_opacity ) );
			$attributes['data-parallax-to'] = wp_json_encode( array(
				'opacity' => $final_opacity,
				'stagger' => $stagger,
			) );
		}

		if ( $v2 ) {
			$breakpoints = ld_parallax_v2_breakpoints( $this, 'lqd_text_reveal' );
			if ( ! empty( $breakpoints ) ) {
				$attributes['data-parallax-breakpoints'] = wp_json_encode( $breakpoints );
			}
			if ( ! ld_parallax_follows_global( $this, 'lqd_text_reveal' ) ) {
				$attributes['data-parallax-apply'] = wp_json_encode( ld_parallax_apply_devices( $this, 'lqd_text_reveal' ) );
			}
		}

		return $attributes;
	}

	protected function text_reveal_split_class( $attributes ) {
		$split = json_decode( isset( $attributes['data-split-options'] ) ? $attributes['data-split-options'] : '', true );
		if ( is_array( $split ) && isset( $split['type'] ) && 'words' === $split['type'] ) {
			return 'lqd-words';
		}
		return 'lqd-chars';
	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Text_Reveal() );
