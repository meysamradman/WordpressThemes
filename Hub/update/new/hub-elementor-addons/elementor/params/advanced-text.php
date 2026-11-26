<?php

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

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

defined( 'ABSPATH' ) || die();

function ld_el_advanced_text( $prefix, $condition = '' ){

    $prefix->add_control(
        'advanced_text_enable',
        [
            'label' => esc_html__( 'Enable the advanced text?', 'hub-elementor-addons' ),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__( 'On', 'hub-elementor-addons' ),
            'label_off' => esc_html__( 'Off', 'hub-elementor-addons' ),
            'return_value' => 'yes',
            'default' => '',
        ]
    );

    $repeater_advanced_text = new Repeater();

    $repeater_advanced_text->add_control(
        'text', [
            'label' => esc_html__( 'Title', 'hub-elementor-addons' ) . apply_filters( 'hub/elementor/widget/ai_button', 'elementor-input' ),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__( 'Title' , 'hub-elementor-addons' ),
            'label_block' => true,
        ]
    );

    $repeater_advanced_text->add_control(
        'image',
        [
            'label' => esc_html__( 'Choose Image', 'hub-elementor-addons' ),
            'type' => Controls_Manager::MEDIA,
        ]
    );

    $repeater_advanced_text->add_responsive_control(
        'img_width',
        [
            'label' => esc_html__( 'Width', 'hub-elementor-addons' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vw' ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} img' => 'width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_control(
        'image_align',
        [
            'label' => esc_html__( 'Image placement', 'hub-elementor-addons' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__( 'Left', 'hub-elementor-addons' ),
                    'icon' => 'eicon-h-align-left',
                ],
                'right' => [
                    'title' => esc_html__( 'Right', 'hub-elementor-addons' ),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => 'left',
            'toggle' => false,
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_control(
        'image_v_align',
        [
            'label' => esc_html__( 'Vertical align', 'hub-elementor-addons' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'baseline' => 'Baseline',
                'sub' => 'Subscript',
                'sup' => 'Superscript',
                'top' => 'Top',
                'text-top' => 'Text top',
                'middle' => 'Middle',
                'bottom' => 'Bottom',
                'text-bottom' => 'Text bottom',
            ],
            'default' => 'bottom',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .lqd-adv-txt-fig' => 'vertical-align: {{VALUE}};',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );


    $repeater_advanced_text->add_responsive_control(
        'border',
        [
            'label' => esc_html__( 'Border', 'hub-elementor-addons' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} img, {{WRAPPER}} {{CURRENT_ITEM}} svg' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};border-style: solid;',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_responsive_control(
        'border_radius',
        [
            'label' => esc_html__( 'Border Radius', 'hub-elementor-addons' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} img, {{WRAPPER}} {{CURRENT_ITEM}} svg, {{WRAPPER}} {{CURRENT_ITEM}} .lqd-adv-txt-fig' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_control(
        'border_color',
        [
            'label' => esc_html__( 'Border Color', 'textdomain' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} img, {{WRAPPER}} {{CURRENT_ITEM}} svg' => 'border-color: {{VALUE}}',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_responsive_control(
        'margin',
        [
            'label' => esc_html__( 'Margin', 'hub-elementor-addons' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .lqd-adv-txt-fig' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );


    $repeater_advanced_text->add_responsive_control(
        'item_z_index',
        [
            'label' => esc_html__( 'Z-Index', 'elementor' ),
            'type' => Controls_Manager::NUMBER,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'position: relative; z-index: {{VALUE}};',
            ],
            'condition' => [
                'image[url]!' => ''
            ]
        ]
    );

    $repeater_advanced_text->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'advanced_text_typography',
            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
        ]
    );

    $repeater_advanced_text->add_control(
        'advanced_text_color',
        [
            'label' => esc_html__( 'Color', 'hub-elementor-addons' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
            ],
			'condition' => [
				'add_gradient' => '',
			]
        ]
    );

	$repeater_advanced_text->add_control(
		'add_gradient',
		[
			'label' => __( 'Gradient And Mask Image?', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SWITCHER,
		]
	);

	$repeater_advanced_text->add_group_control(
		Group_Control_Background::get_type(),
		[
			'name' => 'text_mask_image',
			'label' => __( 'Background', 'hub-elementor-addons' ),
			'types' => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			'fields_options' => [
				'background' => [
					'default' => 'classic',
				],
			],
			'condition' => array(
				'add_gradient' => 'yes',
			)
		]
	);

	$repeater_advanced_text->add_control(
		'text_mask_image_extra_css',
		[
			'type' => Controls_Manager::HIDDEN,
			'default' => 'yes',
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-fill-color: transparent;',
			],
			'condition' => [
				'add_gradient' => 'yes',
			]
		]
	);

	$repeater_advanced_text->add_responsive_control(
		'advanced_text_opacity',
		[
			'label' => esc_html__( 'Opacity', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'opacity: {{SIZE}};',
			],
		]
	);

	$repeater_advanced_text->add_responsive_control(
		'advanced_text_rotate',
		[
			'label' => esc_html__( 'Rotate', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'px' => [
				'deg' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'display: inline-flex; transform: rotate({{SIZE}}deg); transform-origin: center;',
			],
		]
	);

    $prefix->add_control(
        'advanced_text_content',
        [
            'label' => esc_html__( 'Items', 'hub-elementor-addons' ),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater_advanced_text->get_controls(),
            'default' => [
                [
                    'text' => esc_html__( 'Title #1', 'hub-elementor-addons' ),
                ],
                [
                    'text' => esc_html__( 'Title #2', 'hub-elementor-addons' ),
                ],
            ],
            'title_field' => '{{{ text }}}',
            'condition' => [
                'advanced_text_enable' => 'yes'
            ]
        ]
    );

}

/**
 * Allowed HTML for advanced text output.
 *
 * `wp_kses_post()` strips `<svg>` and every SVG child tag, which would wipe out the
 * inline SVG markup returned by Elementor's SVG handler. This extends the standard
 * post allowlist with the SVG tags/attributes Elementor's sanitizer can emit.
 */
function ld_el_advanced_text_allowed_html(){

    $allowed = wp_kses_allowed_html( 'post' );

    // Attributes shared by every SVG element. Note: kses lowercases tag and attribute
    // names, which is fine because the HTML parser re-maps them for SVG (viewbox ->
    // viewBox, clippath -> clipPath, and so on).
    $shared_attrs = array(
        'class' => true,
        'id' => true,
        'style' => true,
        'role' => true,
        'aria-hidden' => true,
        'aria-label' => true,
        'data-*' => true,
        'fill' => true,
        'fill-rule' => true,
        'fill-opacity' => true,
        'stroke' => true,
        'stroke-width' => true,
        'stroke-linecap' => true,
        'stroke-linejoin' => true,
        'stroke-dasharray' => true,
        'stroke-dashoffset' => true,
        'stroke-miterlimit' => true,
        'stroke-opacity' => true,
        'opacity' => true,
        'transform' => true,
        'clip-path' => true,
        'clip-rule' => true,
        'mask' => true,
        'filter' => true,
    );

    $svg_tags = array(
        'svg' => array( 'xmlns' => true, 'xmlns:xlink' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'preserveaspectratio' => true, 'x' => true, 'y' => true, 'version' => true, 'xml:space' => true ),
        'g' => array(),
        'path' => array( 'd' => true, 'pathlength' => true ),
        'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
        'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ),
        'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ),
        'line' => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
        'polyline' => array( 'points' => true ),
        'polygon' => array( 'points' => true ),
        'defs' => array(),
        'symbol' => array( 'viewbox' => true ),
        'use' => array( 'href' => true, 'xlink:href' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true ),
        'clippath' => array( 'clippathunits' => true ),
        'lineargradient' => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true, 'gradienttransform' => true ),
        'radialgradient' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fx' => true, 'fy' => true, 'gradientunits' => true, 'gradienttransform' => true ),
        'stop' => array( 'offset' => true, 'stop-color' => true, 'stop-opacity' => true ),
        'pattern' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'patternunits' => true, 'patterncontentunits' => true, 'patterntransform' => true, 'viewbox' => true ),
        'text' => array( 'x' => true, 'y' => true, 'dx' => true, 'dy' => true, 'text-anchor' => true, 'font-size' => true, 'font-family' => true, 'font-weight' => true ),
        'tspan' => array( 'x' => true, 'y' => true, 'dx' => true, 'dy' => true ),
        'title' => array(),
        'desc' => array(),
    );

    foreach ( $svg_tags as $tag => $attrs ) {
        $allowed[ $tag ] = array_merge( $shared_attrs, $attrs );
    }

    return $allowed;

}

function ld_el_get_advanced_text( $widget ){

    $items = $widget->get_settings_for_display('advanced_text_content');
    $content = '';

    if ( $items ){
        foreach( $items as $item ){

            $content .= sprintf( '<span class="lqd-adv-txt-item elementor-repeater-item-%s">', $item['_id'] );

            if ( !empty($item['image']['url']) ){
                $image_id = ! empty( $item['image']['id'] ) ? $item['image']['id'] : 0;
                $is_svg = $image_id
                    ? get_post_mime_type( $image_id ) === 'image/svg+xml'
                    : 'svg' === strtolower( pathinfo( $item['image']['url'], PATHINFO_EXTENSION ) );

                $image_markup = '';

                if ( $is_svg && $image_id && class_exists( '\Elementor\Core\Files\File_Types\Svg' ) ) {
                    $image_markup = \Elementor\Core\Files\File_Types\Svg::get_inline_svg( $image_id );
                }

                if ( empty( $image_markup ) ) {
                    $image_markup = wp_get_attachment_image( $image_id, 'full', false );
                }

                $img_html = '<span class="lqd-adv-txt-fig pos-rel d-inline-flex">' . $image_markup . '</span>';
                $content = $item['image_align'] === 'left' ? $content . $img_html . $item['text'] : $content . $item['text'] . $img_html;
            } else {
                $content .= $item['text'];
            }

            $content .= "</span>";

        }
    }

    return $content;

}
