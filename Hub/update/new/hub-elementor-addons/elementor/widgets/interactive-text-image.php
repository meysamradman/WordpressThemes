<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Schemes\Color;
use Elementor\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 2.0.0
 */
class LD_Interactive_Text_Image extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ld_interactive_text_image';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Liquid Interactive Text Image', 'archub-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-featured-image lqd-element';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'interactive', 'menu', 'image', 'text' ];
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		if ( liquid_helper()->liquid_elementor_script_depends() ){
			return [ 'gsap', 'liquid-mouse-pos' ];
		} else {
			return [''];
		}
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

    $repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'archub-elementor-addons' ) . apply_filters( 'hub/elementor/widget/ai_button', 'elementor-input' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Item title', 'archub-elementor-addons' ),
				'dynamic' => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

    $repeater->add_control(
			'description',
			array(
				'label' => __( 'Description', 'archub-elementor-addons' ) . apply_filters( 'hub/elementor/widget/ai_button', 'elementor-input' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'dynamic' => array(
					'active' => true,
				),
				'label_block' => true,
			)
		);

    $repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'archub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
        'dynamic' => array(
					'active' => true,
				),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_responsive_control(
			'item_images_width',
			[
				'label' => esc_html__( 'Images width', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
        'selectors' => [
          '{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'separator' => true
			]
		);

    $repeater->add_responsive_control(
			'item_images_height',
			[
				'label' => esc_html__( 'Images height', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
        'selectors' => [
          '{{WRAPPER}} {{CURRENT_ITEM}}' => 'height: {{SIZE}}{{UNIT}};',
        ]
			]
		);

    $repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'archub-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'archub-elementor-addons' ),
				'show_external' => true,
        'dynamic' => array(
					'active' => true,
				),
				'default' => [
					'url' => '#',
				],
			]
		);

    $this->add_control(
			'items',
			array(
				'label' => __( 'Items', 'archub-elementor-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			)
		);

    $this->add_control(
			'title_tag',
			array(
				'label' => esc_html__( 'Title HTML tag', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'p' => 'p',
					'span' => 'span',
				],
				'default' => 'span',
				'separator' => 'before',
			)
		);

    $this->add_control(
			'desc_tag',
			array(
				'label' => esc_html__( 'Description HTML tag', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'p' => 'p',
					'span' => 'span',
				],
				'default' => 'p',
			)
		);

    $this->add_control(
			'show_counter',
			array(
				'label' => esc_html__( 'Show counter', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'archub-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'archub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
			)
		);

    $this->add_control(
			'counter_leading_zero',
			array(
				'label' => esc_html__( 'Counter leading zero', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'archub-elementor-addons' ),
				'label_off' => esc_html__( 'No', 'archub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'show_counter' => 'yes',
				],
			)
		);

    $this->add_control(
			'separator_style',
			array(
				'label' => esc_html__( 'Separator', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'dot' => esc_html__( 'Dot', 'archub-elementor-addons' ),
					'border' => esc_html__( 'Border', 'archub-elementor-addons' ),
					'none' => esc_html__( 'None', 'archub-elementor-addons' ),
				],
				'default' => 'dot',
				'separator' => 'before',
			)
		);

    $this->add_responsive_control(
			'images_width',
			[
				'label' => esc_html__( 'Images width', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 350,
				],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-items' => '--iti-img-width: {{SIZE}}{{UNIT}};',
        ],
        'separator' => true
			]
		);

    $this->add_responsive_control(
			'images_height',
			[
				'label' => esc_html__( 'Images height', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 350,
				],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-items' => '--iti-img-height: {{SIZE}}{{UNIT}};',
        ]
			]
		);

    $this->add_responsive_control(
			'images_border_radius',
			[
				'label' => esc_html__( 'Images border radius', 'archub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-img-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
			]
		);

    $this->add_responsive_control(
			'items_h_gap',
			[
				'label' => esc_html__( 'Items horizontal gap', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'em',
					'size' => 1,
				],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-items' => '--iti-h-gap: {{SIZE}}{{UNIT}};',
        ]
			]
		);

    $this->add_responsive_control(
			'counter_width',
			[
				'label' => esc_html__( 'Counter width', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 20,
						'step' => 0.1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-items' => '--iti-counter-width: {{SIZE}}{{UNIT}};',
        ],
				'condition' => [
					'show_counter' => 'yes',
					'separator_style' => 'border',
				],
			]
		);

    $this->add_responsive_control(
			'desc_width',
			[
				'label' => esc_html__( 'Description width', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 60,
						'step' => 0.1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
        'selectors' => [
          '{{WRAPPER}} .lqd-iti-items' => '--iti-desc-width: {{SIZE}}{{UNIT}};',
        ],
				'condition' => [
					'separator_style' => 'border',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'archub-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'archub-elementor-addons' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'archub-elementor-addons' ),
						'icon' => 'fa fa-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'archub-elementor-addons' ),
						'icon' => 'fa fa-align-right',
					],
					'space-between' => [
						'title' => __( 'Justify', 'archub-elementor-addons' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-links' => 'justify-content: {{VALUE}}',
				],
				'toggle' => true,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Links', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

    $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mennu_typography',
				'label' => __( 'Links typography', 'archub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .lqd-iti-link',
			]
		);

		$this->add_control(
			'link_color',
			[
				'label' => esc_html__( 'Link color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link' => 'color: {{VALUE}}',
				],
        'separator' => 'before'
			]
		);

		$this->add_control(
			'link_color_hover',
			[
				'label' => esc_html__( 'Link hover color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'counter_style_section',
			[
				'label' => esc_html__( 'Counter', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_counter' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'counter_typography',
				'selector' => '{{WRAPPER}} .lqd-iti-counter',
			]
		);

		$this->start_controls_tabs( 'counter_colors_tabs' );

		$this->start_controls_tab(
			'counter_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'counter_color',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-counter' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'counter_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'counter_color_hover',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item:hover .lqd-iti-counter, {{WRAPPER}} .lqd-iti-link-item.lqd-is-active .lqd-iti-counter' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .lqd-iti-title',
			]
		);

		$this->start_controls_tabs( 'title_colors_tabs' );

		$this->start_controls_tab(
			'title_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item:hover .lqd-iti-title, {{WRAPPER}} .lqd-iti-link-item.lqd-is-active .lqd-iti-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'desc_style_section',
			[
				'label' => esc_html__( 'Description', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .lqd-iti-desc',
			]
		);

		$this->start_controls_tabs( 'desc_colors_tabs' );

		$this->start_controls_tab(
			'desc_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'desc_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'desc_color_hover',
			[
				'label' => esc_html__( 'Color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item:hover .lqd-iti-desc, {{WRAPPER}} .lqd-iti-link-item.lqd-is-active .lqd-iti-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'items_style_section',
			[
				'label' => esc_html__( 'Items', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => esc_html__( 'Padding', 'archub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'item_bg_tabs' );

		$this->start_controls_tab(
			'item_bg_normal',
			[
				'label' => esc_html__( 'Normal', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'item_bg',
			[
				'label' => esc_html__( 'Background color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_bg_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'archub-elementor-addons' ),
			]
		);

		$this->add_control(
			'item_bg_hover',
			[
				'label' => esc_html__( 'Background color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-link-item:hover, {{WRAPPER}} .lqd-iti-link-item.lqd-is-active' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'separator_style_section',
			[
				'label' => esc_html__( 'Separator', 'archub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'separator_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'label' => esc_html__( 'Dot color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-items' => '--iti-dot-color: {{VALUE}};',
				],
				'condition' => [
					'separator_style' => 'dot',
				],
			]
		);

		$this->add_responsive_control(
			'dot_size',
			[
				'label' => esc_html__( 'Dot size', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-items' => '--iti-dot-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'separator_style' => 'dot',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border color', 'archub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-items' => '--iti-border-color: {{VALUE}};',
				],
				'condition' => [
					'separator_style' => 'border',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => esc_html__( 'Border width', 'archub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-iti-items' => '--iti-border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'separator_style' => 'border',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span' );
		$title_tag = isset( $settings['title_tag'] ) && in_array( $settings['title_tag'], $allowed_tags, true ) ? $settings['title_tag'] : 'span';
		$desc_tag = isset( $settings['desc_tag'] ) && in_array( $settings['desc_tag'], $allowed_tags, true ) ? $settings['desc_tag'] : 'p';
		$separator_style = isset( $settings['separator_style'] ) && in_array( $settings['separator_style'], array( 'dot', 'border', 'none' ), true ) ? $settings['separator_style'] : 'dot';
		$show_counter = isset( $settings['show_counter'] ) && 'yes' === $settings['show_counter'];
		$leading_zero = ! isset( $settings['counter_leading_zero'] ) || 'yes' === $settings['counter_leading_zero'];

		$this->add_render_attribute(
			'wrapper',
			[
				'id' => 'lqd-iti-' . $this->get_ID(),
				'class' => [
					'lqd-iti-items',
					'pos-rel',
				 ],
				 'data-iti-sep' => $separator_style,
				 'data-lqd-mouse-pos' => 'true',
				 'data-active-onhover' => 'true',
				 'data-active-onhover-options' => wp_json_encode(array(
          "triggers" => ".lqd-iti-links li",
          "targets" => ".lqd-iti-imgs .lqd-iti-img-wrap"
         )),
			]
		);

    $menu_i = 1;

		?>

    <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
      <ol class="lqd-iti-links d-flex flex-wrap reset-ul">
        <?php
          foreach( $settings['items'] as $item ) {
            $this->add_link_attributes( 'link-' . $menu_i, $item['link'] );
        ?>
          <li class="lqd-iti-link-item d-flex pos-rel">
            <a class="lqd-iti-link" <?php echo $this->get_render_attribute_string( 'link-' . $menu_i ); ?>>
              <?php if ( $show_counter ) {
                printf(
                  '<span class="lqd-iti-counter">%s</span>',
                  esc_html( $leading_zero ? sprintf( '%02d', $menu_i ) : $menu_i )
                );
              } ?>
              <?php printf( '<%1$s class="lqd-iti-title">%2$s</%1$s>', esc_attr( $title_tag ), esc_html( $item['title'] ) ); ?>
              <?php if ( ! empty( $item['description'] ) ) {
                printf( '<%1$s class="lqd-iti-desc">%2$s</%1$s>', esc_attr( $desc_tag ), wp_kses_post( $item['description'] ) );
              } ?>
            </a>
          </li>
        <?php $menu_i++; } ?>
      </ol>
      <div class="lqd-iti-imgs pos-abs pos-tl z-index-1 pointer-events-none">
        <?php foreach( $settings['items'] as $item ) { ?>
          <div class="lqd-iti-img-wrap lqd-overlay overflow-hidden <?php echo 'elementor-repeater-item-' . $item['_id'] ?>">
            <div class="lqd-iti-img-inner lqd-overlay overflow-hidden">
			<?php
				echo wp_get_attachment_image( $item['image']['id'], 'full', false, array( 'class' => 'lqd-iti-img w-100 h-100 objfit-cover objfit-center' ) );
			?>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

	<?php

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Interactive_Text_Image() );