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

defined( 'ABSPATH' ) || die();

function ld_parallax_v2_enabled() {
	return function_exists( 'liquid_helper' ) && liquid_helper()->get_theme_option( 'enable_parallax_v2' ) === 'on';
}

function ld_el_parallax_ease_options() {
	return array(
		'linear' => 'linear',
		'power1.in' => 'power1.in',
		'power2.in' => 'power2.in',
		'power3.in' => 'power3.in',
		'power4.in' => 'power4.in',
		'sine.in' => 'sine.in',
		'expo.in' => 'expo.in',
		'circ.in' => 'circ.in',
		'back.in' => 'back.in',
		'bounce.in' => 'bounce.in',
		'elastic.in(1,0.2)' => 'elastic.in(1,0.2)',
		'power1.out' => 'power1.out',
		'power2.out' => 'power2.out',
		'power3.out' => 'power3.out',
		'power4.out' => 'power4.out',
		'sine.out' => 'sine.out',
		'expo.out' => 'expo.out',
		'circ.out' => 'circ.out',
		'back.out' => 'back.out',
		'bounce.out' => 'bounce.out',
		'elastic.out(1,0.2)' => 'elastic.out(1,0.2)',
		'power1.inOut' => 'power1.inOut',
		'power2.inOut' => 'power2.inOut',
		'power3.inOut' => 'power3.inOut',
		'power4.inOut' => 'power4.inOut',
		'sine.inOut' => 'sine.inOut',
		'expo.inOut' => 'expo.inOut',
		'circ.inOut' => 'circ.inOut',
		'back.inOut' => 'back.inOut',
		'bounce.inOut' => 'bounce.inOut',
		'elastic.inOut(1,0.2)' => 'elastic.inOut(1,0.2)',
	);
}

function ld_parallax_v2_is_responsive_control( $id ) {
	if ( 'lqd_parallax_settings_trigger_start' === $id || 'lqd_parallax_settings_trigger_end' === $id ) {
		return true;
	}
	if ( 0 === strpos( $id, 'lqd_parallax_from_' ) && 'lqd_parallax_from_options' !== $id ) {
		return true;
	}
	if ( 0 === strpos( $id, 'lqd_parallax_to_' ) && 'lqd_parallax_to_options' !== $id ) {
		return true;
	}
	return false;
}

function ld_parallax_v2_hide_when_steps( $id ) {
	$ids = array(
		'lqd_parallax_settings_trigger',
		'lqd_parallax_trigger_desc',
		'lqd_parallax_settings_trigger_start',
		'lqd_parallax_settings_trigger_end',
		'lqd_parallax_settings_duration',
		'lqd_parallax_to_options',
	);
	if ( in_array( $id, $ids, true ) ) {
		return true;
	}
	return 0 === strpos( $id, 'lqd_parallax_to_' );
}

function ld_el_parallax_add_control( $stack, $id, $args ) {
	if ( ld_parallax_v2_enabled() ) {
		if ( ld_parallax_v2_hide_when_steps( $id ) ) {
			if ( ! isset( $args['condition'] ) || ! is_array( $args['condition'] ) ) {
				$args['condition'] = array();
			}
			$args['condition']['lqd_parallax_steps_enable!'] = 'yes';
		}
		if ( ld_parallax_v2_is_responsive_control( $id ) ) {
			$stack->add_responsive_control( $id, $args );
			return;
		}
	}
	$stack->add_control( $id, $args );
}

function ld_el_parallax($pf){

	ld_el_parallax_add_control( $pf,
		'lqd_parallax',
		[
			'label' => __( 'Parallax', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'hub-elementor-addons' ),
			'label_off' => __( 'Off', 'hub-elementor-addons' ),
			'return_value' => 'yes',
			'default' => '',
			'render_type' => 'none',
		]
	);

	if ( ld_parallax_v2_enabled() ) {
		ld_el_parallax_register_steps_switch( $pf );
	}

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_settings_popover',
		[
			'label' => __( 'Settings', 'hub-elementor-addons' ),
			'type' => Controls_Manager::POPOVER_TOGGLE,
			'label_off' => __( 'Default', 'hub-elementor-addons' ),
			'label_on' => __( 'Custom', 'hub-elementor-addons' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition' => [
				'lqd_parallax' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	// Parallax Settings
	$pf->start_popover();
		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_trigger',
			[
				'label' => __( 'Trigger', 'hub-elementor-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'top bottom',
				'options' => [
					'top bottom'  => __( 'On Enter', 'hub-elementor-addons' ),
					'top top' => __( 'On Leave', 'hub-elementor-addons' ),
					'center center' => __( 'On Center', 'hub-elementor-addons' ),
					'custom' => __( 'Custom', 'hub-elementor-addons' ),
				],
				'condition' => [
					'lqd_parallax_settings_popover' => 'yes',
				],
				'render_type' => 'none',
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_trigger_desc',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( '<div style="font-style: normal;">For custom start and end values you can use keywords like <b>top, bottom, left, right and center</b>. You can also use numbers. E.g. <b>50&#x25; 50&#x25;</b>. Or you can use relative values e.g. <b>top bottom-=25&#x25;</b>.</div>', 'hub-elementor-addons' ),
				'separator' => 'before',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'lqd_parallax_settings_trigger' => 'custom',
					'lqd_parallax_settings_popover' => 'yes',
				),
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_trigger_start',
			[
				'label' => __( 'Trigger Start', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Default: top bottom', 'hub-elementor-addons' ),
				'default' => 'top bottom',
				'description' => __( 'Define when parallax starts. Default is <b>top bottom</b> which means starts when <b>top</b> of the element hits the <b>bottom</b> of the viewport.', 'hub-elementor-addons' ),
				'condition' => array(
					'lqd_parallax_settings_trigger' => 'custom',
					'lqd_parallax_settings_popover' => 'yes',
				),
				'render_type' => 'none',
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_trigger_end',
			[
				'label' => __( 'Trigger End', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Default: bottom top', 'hub-elementor-addons' ),
				'default' => 'bottom top',
				'description' => __( 'Define when parallax ends. Default is <b>bottom top</b> which means ends when <b>bottom</b> of the element hits the <b>top</b> of the viewport.', 'hub-elementor-addons' ),
				'condition' => array(
					'lqd_parallax_settings_trigger' => 'custom',
					'lqd_parallax_settings_popover' => 'yes',
				),
				'separator' => 'after',
				'render_type' => 'none',
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_ease',
			[
				'label' => __( 'Easing', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => [ 'linear' ],
				'options' => ld_el_parallax_ease_options(),
				'condition' => [
					'lqd_parallax_settings_popover' => 'yes',
				],
				'render_type' => 'none',
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_duration',
			[
				'label' => __( 'Increase / Decrease Duration', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
							'min' => -500,
							'max' => 500,
							'step' => 1,
						],
						'%' => [
							'min' => -500,
							'max' => 500,
							'step' => 0.1,
						],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'lqd_parallax_settings_popover' => 'yes',
					'lqd_parallax_settings_trigger!' => 'custom',
				],
				'render_type' => 'none',
			]
		);

		ld_el_parallax_add_control( $pf,
			'lqd_parallax_settings_perspective',
			[
				'label' => __( 'Perspective', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'size' => '',
				],
				'condition' => [
					'lqd_parallax_settings_popover' => 'yes',
				],
				'render_type' => 'none',
			]
		);
	$pf->end_popover();

	// Parallax From Options
	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_options',
		[
			'label' => __( 'Starting Style', 'hub-elementor-addons' ),
			'type' => Controls_Manager::POPOVER_TOGGLE,
			'label_off' => __( 'Default', 'hub-elementor-addons' ),
			'label_on' => __( 'Custom', 'hub-elementor-addons' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition' => [
				'lqd_parallax' => 'yes'
			],
			'render_type' => 'none',
		]
	);
	$pf->start_popover();
	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_x',
		[
			'label' => __( 'Translate X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw', 'vh' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vh' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_y',
		[
			'label' => __( 'Translate Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw', 'vh' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vh' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_z',
		[
			'label' => __( 'Translate Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1
				]
			],
			'default' => [
				'size' => 0,
			],
			'separator' => 'after',
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_scaleX',
		[
			'label' => __( 'Scale X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_scaleY',
		[
			'label' => __( 'Scale Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'separator' => 'after',
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_rotationX',
		[
			'label' => __( 'Rotate X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_rotationY',
		[
			'label' => __( 'Rotate Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_rotationZ',
		[
			'label' => __( 'Rotate Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_opacity',
		[
			'label' => __( 'Opacity', 'hub-elementor-addons' ),
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
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'separator' => 'before',
			'render_type' => 'none',
		]
	);

	if ( ld_parallax_v2_enabled() ) {
		ld_el_parallax_register_filter_controls( $pf, 'lqd_parallax_from_', 'lqd_parallax_from_options' );
	}

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_transformOriginX',
		[
			'label' => __( 'Transform origin X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 50,
				'unit' => '%',
			],
			'separator' => 'before',
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_transformOriginY',
		[
			'label' => __( 'Transform origin Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 50,
				'unit' => '%',
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_from_transformOriginZ',
		[
			'label' => __( 'Transform origin Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_from_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	$pf->end_popover(); // parallax from

	if ( ld_parallax_v2_enabled() ) {
		ld_el_parallax_register_steps_fields( $pf );
	}

	// Parallax To Options
	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_options',
		[
			'label' => __( 'Ending Style', 'hub-elementor-addons' ),
			'type' => Controls_Manager::POPOVER_TOGGLE,
			'label_off' => __( 'Default', 'hub-elementor-addons' ),
			'label_on' => __( 'Custom', 'hub-elementor-addons' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition' => [
				'lqd_parallax' => 'yes'
			],
			'render_type' => 'none',
		]
	);
	$pf->start_popover();
	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_x',
		[
			'label' => __( 'Translate X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw', 'vh' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vh' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_y',
		[
			'label' => __( 'Translate Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw', 'vh' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
					'vh' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_z',
		[
			'label' => __( 'Translate Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'separator' => 'after',
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_scaleX',
		[
			'label' => __( 'Scale X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_scaleY',
		[
			'label' => __( 'Scale Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'separator' => 'after',
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_rotationX',
		[
			'label' => __( 'Rotate X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_rotationY',
		[
			'label' => __( 'Rotate Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_rotationZ',
		[
			'label' => __( 'Rotate Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => -360,
					'max' => 360,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_opacity',
		[
			'label' => __( 'Opacity', 'hub-elementor-addons' ),
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
				'size' => 1,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'separator' => 'before',
			'render_type' => 'none',
		]
	);

	if ( ld_parallax_v2_enabled() ) {
		ld_el_parallax_register_filter_controls( $pf, 'lqd_parallax_to_', 'lqd_parallax_to_options' );
	}

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_transformOriginX',
		[
			'label' => __( 'Transform origin X', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 50,
				'unit' => '%',
			],
			'separator' => 'before',
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_transformOriginY',
		[
			'label' => __( 'Transform origin Y', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
			],
			'default' => [
				'size' => 50,
				'unit' => '%',
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	ld_el_parallax_add_control( $pf,
		'lqd_parallax_to_transformOriginZ',
		[
			'label' => __( 'Transform origin Z', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
			],
			'default' => [
				'size' => 0,
			],
			'condition' => [
				'lqd_parallax_to_options' => 'yes'
			],
			'render_type' => 'none',
		]
	);

	$pf->end_popover(); // parallax to

	if ( ld_parallax_v2_enabled() ) {
		ld_el_parallax_register_apply_on( $pf );
	}
}
function ld_el_parallax_filter_slider_defs() {
	return array(
		array(
			'key' => 'blur',
			'label' => __( 'Blur', 'hub-elementor-addons' ),
			'units' => array( 'px' ),
			'range' => array(
				'px' => array(
					'min' => 0,
					'max' => 50,
					'step' => 1,
				),
			),
			'default' => array(
				'size' => 0,
				'unit' => 'px',
			),
			'separator' => 'before',
		),
		array(
			'key' => 'brightness',
			'label' => __( 'Brightness', 'hub-elementor-addons' ),
			'units' => array( '%' ),
			'range' => array(
				'%' => array(
					'min' => 0,
					'max' => 200,
					'step' => 1,
				),
			),
			'default' => array(
				'size' => 100,
				'unit' => '%',
			),
			'separator' => '',
		),
		array(
			'key' => 'contrast',
			'label' => __( 'Contrast', 'hub-elementor-addons' ),
			'units' => array( '%' ),
			'range' => array(
				'%' => array(
					'min' => 0,
					'max' => 200,
					'step' => 1,
				),
			),
			'default' => array(
				'size' => 100,
				'unit' => '%',
			),
			'separator' => '',
		),
	);
}

function ld_el_parallax_register_filter_controls( $stack, $prefix, $popover_key ) {
	foreach ( ld_el_parallax_filter_slider_defs() as $def ) {
		$args = array(
			'label' => $def['label'],
			'type' => Controls_Manager::SLIDER,
			'size_units' => $def['units'],
			'range' => $def['range'],
			'default' => $def['default'],
			'condition' => array(
				$popover_key => 'yes',
			),
			'render_type' => 'none',
		);
		if ( '' !== $def['separator'] ) {
			$args['separator'] = $def['separator'];
		}
		ld_el_parallax_add_control( $stack, $prefix . $def['key'], $args );
	}
}

function ld_el_parallax_register_starting_style( $pf, $args = array() ) {
	$prefix = ( isset( $args['prefix'] ) && is_string( $args['prefix'] ) && '' !== $args['prefix'] ) ? $args['prefix'] : 'lqd_parallax';
	$render_type = isset( $args['render_type'] ) ? $args['render_type'] : 'none';
	$condition = isset( $args['condition'] ) ? $args['condition'] : array();
	$toggle = array(
		'label' => __( 'Starting Style', 'hub-elementor-addons' ),
		'type' => Controls_Manager::POPOVER_TOGGLE,
		'label_off' => __( 'Default', 'hub-elementor-addons' ),
		'label_on' => __( 'Custom', 'hub-elementor-addons' ),
		'return_value' => 'yes',
		'default' => 'yes',
		'render_type' => $render_type,
	);
	if ( ! empty( $condition ) ) {
		$toggle['condition'] = $condition;
	}
	$pf->add_control( $prefix . '_from_options', $toggle );
	$pf->start_popover();
	ld_el_parallax_add_step_sliders( $pf, $prefix . '_from_', ! empty( $args['color'] ), $render_type );
	$pf->end_popover();
}

function ld_el_parallax_register_steps_switch( $pf, $args = array() ) {
	$prefix = ( isset( $args['prefix'] ) && is_string( $args['prefix'] ) && '' !== $args['prefix'] ) ? $args['prefix'] : 'lqd_parallax';
	$control = array(
		'label' => __( 'Steps', 'hub-elementor-addons' ),
		'description' => __( 'Each step animates from Starting Style, or from the previous step, across its viewport range.', 'hub-elementor-addons' ),
		'type' => Controls_Manager::SWITCHER,
		'label_on' => __( 'On', 'hub-elementor-addons' ),
		'label_off' => __( 'Off', 'hub-elementor-addons' ),
		'return_value' => 'yes',
		'default' => '',
		'render_type' => isset( $args['render_type'] ) ? $args['render_type'] : 'none',
	);
	if ( ! isset( $args['condition'] ) ) {
		$control['condition'] = array(
			$prefix => 'yes',
		);
	} elseif ( ! empty( $args['condition'] ) ) {
		$control['condition'] = $args['condition'];
	}
	$pf->add_control( $prefix . '_steps_enable', $control );
}

function ld_el_parallax_register_steps_fields( $pf, $args = array() ) {
	$prefix = ( isset( $args['prefix'] ) && is_string( $args['prefix'] ) && '' !== $args['prefix'] ) ? $args['prefix'] : 'lqd_parallax';
	$render_type = isset( $args['render_type'] ) ? $args['render_type'] : 'none';
	$with_color = ! empty( $args['color'] );
	$step = $prefix . '_step_';
	$range_key = $step . 'range';
	$repeater = new Repeater();

	$repeater->add_responsive_control(
		$range_key,
		array(
			'label' => __( 'Viewport', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range' => array(
				'%' => array(
					'min' => 0,
					'max' => 100,
					'step' => 1,
				),
			),
			'default' => array(
				'sizes' => array(
					'start' => 0,
					'end' => 100,
				),
				'unit' => '%',
			),
			'labels' => array(
				__( 'Bottom', 'hub-elementor-addons' ),
				__( 'Top', 'hub-elementor-addons' ),
			),
			'scales' => 1,
			'handles' => 'range',
			'render_type' => $render_type,
		)
	);

	$repeater->add_control(
		$step . 'ease',
		array(
			'label' => __( 'Easing', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => array( '' => __( 'Inherit', 'hub-elementor-addons' ) ) + ld_el_parallax_ease_options(),
			'render_type' => $render_type,
		)
	);

	ld_el_parallax_add_step_sliders( $repeater, $step, $with_color, $render_type );

	$full_range = array(
		'sizes' => array(
			'start' => 0,
			'end' => 100,
		),
		'unit' => '%',
	);
	$half_range = array(
		'sizes' => array(
			'start' => 0,
			'end' => 50,
		),
		'unit' => '%',
	);
	$condition = isset( $args['condition'] ) ? $args['condition'] : array(
		$prefix => 'yes',
		$prefix . '_steps_enable' => 'yes',
	);

	$pf->add_control(
		$prefix . '_steps',
		array(
			'label' => __( 'Steps', 'hub-elementor-addons' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'title_field' => '<# var sizes = ( typeof ' . $range_key . ' !== "undefined" && ' . $range_key . ' && ' . $range_key . '.sizes ) ? ' . $range_key . '.sizes : {}; var start = ( sizes.start === 0 || sizes.start ) ? sizes.start : 0; var end = ( sizes.end === 0 || sizes.end ) ? sizes.end : 100; print( start + "% – " + end + "%" ); #>',
			'prevent_empty' => false,
			'default' => array(
				array(
					$range_key => $full_range,
					$step . 'opacity' => array(
						'size' => 0,
						'unit' => 'px',
					),
				),
				array(
					$range_key => $half_range,
					$step . 'opacity' => array(
						'size' => 1,
						'unit' => 'px',
					),
				),
			),
			'condition' => $condition,
			'render_type' => $render_type,
		)
	);
}

function ld_el_parallax_add_step_sliders( $repeater, $prefix = 'lqd_parallax_step_', $with_color = false, $render_type = 'none' ) {
	$length_units = array( 'px', '%', 'vw', 'vh' );
	$length_range = array(
		'px' => array( 'min' => -500, 'max' => 500, 'step' => 1 ),
		'%' => array( 'min' => -100, 'max' => 100, 'step' => 0.1 ),
		'vw' => array( 'min' => -100, 'max' => 100, 'step' => 0.1 ),
		'vh' => array( 'min' => -100, 'max' => 100, 'step' => 0.1 ),
	);
	$px_range = array(
		'px' => array( 'min' => -500, 'max' => 500, 'step' => 1 ),
	);
	$scale_range = array(
		'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
	);
	$rotate_range = array(
		'px' => array( 'min' => -360, 'max' => 360, 'step' => 1 ),
	);
	$opacity_range = array(
		'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.1 ),
	);
	$origin_range = array(
		'px' => array( 'min' => -500, 'max' => 500, 'step' => 1 ),
		'%' => array( 'min' => -100, 'max' => 100, 'step' => 0.1 ),
	);
	$sliders = array(
		array( 'x', __( 'Translate X', 'hub-elementor-addons' ), $length_units, $length_range, array( 'size' => 0 ), '' ),
		array( 'y', __( 'Translate Y', 'hub-elementor-addons' ), $length_units, $length_range, array( 'size' => 0 ), '' ),
		array( 'z', __( 'Translate Z', 'hub-elementor-addons' ), array( 'px' ), $px_range, array( 'size' => 0 ), 'after' ),
		array( 'scaleX', __( 'Scale X', 'hub-elementor-addons' ), array( 'px' ), $scale_range, array( 'size' => 1 ), '' ),
		array( 'scaleY', __( 'Scale Y', 'hub-elementor-addons' ), array( 'px' ), $scale_range, array( 'size' => 1 ), 'after' ),
		array( 'rotationX', __( 'Rotate X', 'hub-elementor-addons' ), array( 'px' ), $rotate_range, array( 'size' => 0 ), '' ),
		array( 'rotationY', __( 'Rotate Y', 'hub-elementor-addons' ), array( 'px' ), $rotate_range, array( 'size' => 0 ), '' ),
		array( 'rotationZ', __( 'Rotate Z', 'hub-elementor-addons' ), array( 'px' ), $rotate_range, array( 'size' => 0 ), '' ),
		array( 'opacity', __( 'Opacity', 'hub-elementor-addons' ), array( 'px' ), $opacity_range, array( 'size' => 1 ), 'before' ),
		array( 'blur', __( 'Blur', 'hub-elementor-addons' ), array( 'px' ), array( 'px' => array( 'min' => 0, 'max' => 50, 'step' => 1 ) ), array( 'size' => 0, 'unit' => 'px' ), 'before' ),
		array( 'brightness', __( 'Brightness', 'hub-elementor-addons' ), array( '%' ), array( '%' => array( 'min' => 0, 'max' => 200, 'step' => 1 ) ), array( 'size' => 100, 'unit' => '%' ), '' ),
		array( 'contrast', __( 'Contrast', 'hub-elementor-addons' ), array( '%' ), array( '%' => array( 'min' => 0, 'max' => 200, 'step' => 1 ) ), array( 'size' => 100, 'unit' => '%' ), '' ),
		array( 'transformOriginX', __( 'Transform origin X', 'hub-elementor-addons' ), array( 'px', '%' ), $origin_range, array( 'size' => 50, 'unit' => '%' ), 'before' ),
		array( 'transformOriginY', __( 'Transform origin Y', 'hub-elementor-addons' ), array( 'px', '%' ), $origin_range, array( 'size' => 50, 'unit' => '%' ), '' ),
		array( 'transformOriginZ', __( 'Transform origin Z', 'hub-elementor-addons' ), array( 'px' ), $px_range, array( 'size' => 0 ), '' ),
	);

	foreach ( $sliders as $slider ) {
		$args = array(
			'label' => $slider[1],
			'type' => Controls_Manager::SLIDER,
			'size_units' => $slider[2],
			'range' => $slider[3],
			'default' => $slider[4],
			'render_type' => $render_type,
		);
		if ( '' !== $slider[5] ) {
			$args['separator'] = $slider[5];
		}
		$repeater->add_responsive_control( $prefix . $slider[0], $args );
		if ( $with_color && 'opacity' === $slider[0] ) {
			$repeater->add_responsive_control(
				$prefix . 'color',
				array(
					'label' => __( 'Color', 'hub-elementor-addons' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => $render_type,
				)
			);
		}
	}
}

function ld_el_parallax_register_apply_on( $pf, $args = array() ) {
	$prefix = ( isset( $args['prefix'] ) && is_string( $args['prefix'] ) && '' !== $args['prefix'] ) ? $args['prefix'] : 'lqd_parallax';
	$render_type = isset( $args['render_type'] ) ? $args['render_type'] : 'none';
	$description = isset( $args['description'] ) ? $args['description'] : __( '"On" uses the "Apply on" breakpoints in Theme Options → Features → Parallax. Turn off to choose breakpoints for this element.', 'hub-elementor-addons' );
	$parent = isset( $args['condition'] ) ? $args['condition'] : array( $prefix => 'yes' );
	if ( ! is_array( $parent ) ) {
		$parent = array();
	}
	$options = array();
	foreach ( ld_parallax_v2_device_names() as $device ) {
		$options[ $device ] = ld_parallax_v2_device_label( $device );
	}
	$follow = array(
		'label' => __( 'Breakpoints from Theme Options', 'hub-elementor-addons' ),
		'description' => $description,
		'type' => Controls_Manager::SWITCHER,
		'label_on' => __( 'On', 'hub-elementor-addons' ),
		'label_off' => __( 'Off', 'hub-elementor-addons' ),
		'return_value' => 'yes',
		'default' => 'yes',
		'render_type' => $render_type,
	);
	if ( ! empty( $parent ) ) {
		$follow['condition'] = $parent;
	}
	$apply_condition = $parent;
	$apply_condition[ $prefix . '_apply_follow!' ] = 'yes';
	$pf->add_control( $prefix . '_apply_follow', $follow );
	$pf->add_control(
		$prefix . '_apply_on',
		array(
			'label' => __( 'Apply on', 'hub-elementor-addons' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'label_block' => true,
			'options' => $options,
			'default' => array_keys( $options ),
			'condition' => $apply_condition,
			'render_type' => $render_type,
		)
	);
}

function ld_parallax_v2_device_names() {
	$names = array();
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->breakpoints ) ) {
		$breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
		if ( is_array( $breakpoints ) ) {
			$names = array_keys( $breakpoints );
		}
	}
	if ( empty( $names ) ) {
		$names = array( 'mobile', 'tablet' );
	}
	array_unshift( $names, 'desktop' );
	return array_values( array_unique( $names ) );
}

function ld_parallax_v2_device_label( $device ) {
	if ( 'desktop' === $device ) {
		return __( 'Desktop', 'hub-elementor-addons' );
	}
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->breakpoints ) ) {
		$breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
		if ( isset( $breakpoints[ $device ] ) && is_object( $breakpoints[ $device ] ) && method_exists( $breakpoints[ $device ], 'get_label' ) ) {
			return $breakpoints[ $device ]->get_label();
		}
	}
	return $device;
}

function ld_parallax_transform_props() {
	return array(
		'x' => 'length',
		'y' => 'length',
		'z' => 'length',
		'scaleX' => 'float',
		'scaleY' => 'float',
		'rotationX' => 'int',
		'rotationY' => 'int',
		'rotationZ' => 'int',
		'opacity' => 'float',
	);
}

function ld_parallax_slider_size_is_real( $slider ) {
	return is_array( $slider ) && array_key_exists( 'size', $slider ) && null !== $slider['size'] && '' !== $slider['size'];
}

function ld_parallax_format_prop( $slider, $kind ) {
	if ( ! ld_parallax_slider_size_is_real( $slider ) ) {
		return null;
	}
	$unit = isset( $slider['unit'] ) ? $slider['unit'] : '';
	if ( 'length' === $kind ) {
		return $slider['size'] . $unit;
	}
	if ( 'int' === $kind ) {
		return (int) $slider['size'];
	}
	return (float) $slider['size'];
}

function ld_parallax_filter_defs() {
	return array(
		'blur' => 0,
		'brightness' => 100,
		'contrast' => 100,
	);
}

function ld_parallax_filter_number( $formatted ) {
	if ( null === $formatted || '' === $formatted ) {
		return null;
	}
	if ( is_numeric( $formatted ) ) {
		return (float) $formatted;
	}
	if ( is_string( $formatted ) && preg_match( '/^-?\d+(?:\.\d+)?/', $formatted, $matches ) ) {
		return (float) $matches[0];
	}
	return null;
}

function ld_parallax_filter_is_identity( $key, $formatted ) {
	$number = ld_parallax_filter_number( $formatted );
	if ( null === $number || ! isset( ld_parallax_filter_defs()[ $key ] ) ) {
		return true;
	}
	return abs( $number - (float) ld_parallax_filter_defs()[ $key ] ) < 0.001;
}

function ld_parallax_filter_identity_string( $key ) {
	$identity = ld_parallax_filter_defs()[ $key ];
	if ( 'blur' === $key ) {
		return $identity . 'px';
	}
	return $identity . '%';
}

function ld_parallax_collect_filters( $source, $prefix ) {
	$values = array();
	$any = false;
	foreach ( ld_parallax_filter_defs() as $key => $identity ) {
		$slider = ( is_array( $source ) && isset( $source[ $prefix . $key ] ) ) ? $source[ $prefix . $key ] : null;
		$formatted = ld_parallax_format_prop( $slider, 'length' );
		if ( null !== $formatted && ! ld_parallax_filter_is_identity( $key, $formatted ) ) {
			$any = true;
		}
		$values[ $key ] = ( null !== $formatted ) ? $formatted : ld_parallax_filter_identity_string( $key );
	}
	if ( ! $any ) {
		return array();
	}
	return $values;
}

function ld_parallax_filter_identity_map() {
	$map = array();
	foreach ( array_keys( ld_parallax_filter_defs() ) as $key ) {
		$map[ $key ] = ld_parallax_filter_identity_string( $key );
	}
	return $map;
}

function ld_parallax_origin_part( $slider ) {
	if ( ! ld_parallax_slider_size_is_real( $slider ) ) {
		return '0';
	}
	$unit = isset( $slider['unit'] ) ? $slider['unit'] : '';
	return $slider['size'] . $unit;
}

function ld_parallax_origin_string( $x, $y, $z ) {
	return ld_parallax_origin_part( $x ) . ' ' . ld_parallax_origin_part( $y ) . ' ' . ld_parallax_origin_part( $z );
}

function ld_parallax_transform_map( $source, $prefix ) {
	$map = array();
	if ( ! is_array( $source ) ) {
		$source = array();
	}
	foreach ( ld_parallax_transform_props() as $key => $kind ) {
		$slider = isset( $source[ $prefix . $key ] ) ? $source[ $prefix . $key ] : null;
		$formatted = ld_parallax_format_prop( $slider, $kind );
		if ( null !== $formatted ) {
			$map[ $key ] = $formatted;
		}
	}
	$map['transformOrigin'] = ld_parallax_origin_string(
		isset( $source[ $prefix . 'transformOriginX' ] ) ? $source[ $prefix . 'transformOriginX' ] : null,
		isset( $source[ $prefix . 'transformOriginY' ] ) ? $source[ $prefix . 'transformOriginY' ] : null,
		isset( $source[ $prefix . 'transformOriginZ' ] ) ? $source[ $prefix . 'transformOriginZ' ] : null
	);
	foreach ( ld_parallax_collect_filters( $source, $prefix ) as $key => $value ) {
		$map[ $key ] = $value;
	}
	$color = ld_parallax_color_value( $source, $prefix . 'color' );
	if ( null !== $color ) {
		$map['color'] = $color;
	}
	return $map;
}

function ld_parallax_color_value( $source, $key ) {
	if ( ! is_array( $source ) || ! array_key_exists( $key, $source ) ) {
		return null;
	}
	$value = $source[ $key ];
	if ( ! is_string( $value ) ) {
		return null;
	}
	$value = trim( $value );
	if ( '' === $value ) {
		return null;
	}
	return $value;
}

function ld_parallax_ease_string( $ease ) {
	if ( is_array( $ease ) ) {
		$ease = reset( $ease );
	}
	if ( ! is_string( $ease ) || '' === $ease ) {
		return null;
	}
	return $ease;
}

function ld_parallax_clamp_percent( $value, $fallback ) {
	if ( ! is_numeric( $value ) ) {
		return (float) $fallback;
	}
	$number = (float) $value;
	if ( $number < 0 ) {
		return 0.0;
	}
	if ( $number > 100 ) {
		return 100.0;
	}
	return $number;
}

function ld_parallax_viewport_range( $slider ) {
	$start = 0.0;
	$end = 100.0;
	if ( is_array( $slider ) && isset( $slider['sizes'] ) && is_array( $slider['sizes'] ) ) {
		if ( array_key_exists( 'start', $slider['sizes'] ) ) {
			$start = ld_parallax_clamp_percent( $slider['sizes']['start'], 0 );
		}
		if ( array_key_exists( 'end', $slider['sizes'] ) ) {
			$end = ld_parallax_clamp_percent( $slider['sizes']['end'], 100 );
		}
	}
	if ( $end < $start ) {
		$swap = $start;
		$start = $end;
		$end = $swap;
	}
	return array(
		'start' => $start,
		'end' => $end,
	);
}

function ld_parallax_raw_viewport_range( $source, $key ) {
	if ( ! is_array( $source ) || ! array_key_exists( $key, $source ) ) {
		return null;
	}
	$slider = $source[ $key ];
	if ( ! is_array( $slider ) || ! isset( $slider['sizes'] ) || ! is_array( $slider['sizes'] ) ) {
		return null;
	}
	$sizes = $slider['sizes'];
	$has_start = array_key_exists( 'start', $sizes ) && is_numeric( $sizes['start'] );
	$has_end = array_key_exists( 'end', $sizes ) && is_numeric( $sizes['end'] );
	if ( ! $has_start && ! $has_end ) {
		return null;
	}
	return ld_parallax_viewport_range( $slider );
}

function ld_parallax_normalize_device_list( $value, $devices ) {
	if ( is_string( $value ) ) {
		$value = '' === trim( $value ) ? array() : preg_split( '/\s*,\s*/', $value );
	}
	if ( ! is_array( $value ) ) {
		return array();
	}
	$allowed = array_fill_keys( $devices, true );
	$out = array();
	foreach ( $value as $item ) {
		if ( is_array( $item ) || is_object( $item ) ) {
			continue;
		}
		$item = sanitize_key( (string) $item );
		if ( '' === $item || ! isset( $allowed[ $item ] ) || isset( $out[ $item ] ) ) {
			continue;
		}
		$out[ $item ] = $item;
	}
	return array_values( $out );
}

function ld_parallax_perspective_value( $settings ) {
	if ( ! is_array( $settings ) ) {
		return null;
	}
	$perspective = isset( $settings['lqd_parallax_settings_perspective'] ) ? $settings['lqd_parallax_settings_perspective'] : null;
	if ( ! is_array( $perspective ) || ! isset( $perspective['size'] ) || '' === $perspective['size'] || null === $perspective['size'] ) {
		return null;
	}
	if ( ! is_numeric( $perspective['size'] ) || (float) $perspective['size'] === 0.0 ) {
		return null;
	}
	$unit = ( isset( $perspective['unit'] ) && '' !== $perspective['unit'] ) ? $perspective['unit'] : 'px';
	return $perspective['size'] . $unit;
}

function ld_parallax_follows_global( $element, $prefix = 'lqd_parallax' ) {
	$raw = ld_parallax_raw_settings( $element );
	$key = $prefix . '_apply_follow';
	if ( ! array_key_exists( $key, $raw ) ) {
		return true;
	}
	return 'yes' === $raw[ $key ];
}

function ld_parallax_apply_devices( $element, $prefix = 'lqd_parallax' ) {
	$devices = ld_parallax_v2_device_names();
	$raw = ld_parallax_raw_settings( $element );
	if ( ld_parallax_follows_global( $element, $prefix ) ) {
		return $devices;
	}
	$apply_key = $prefix . '_apply_on';
	$settings = $element->get_settings();
	if ( is_array( $settings ) && array_key_exists( $apply_key, $settings ) ) {
		return ld_parallax_normalize_device_list( $settings[ $apply_key ], $devices );
	}
	if ( array_key_exists( $apply_key, $raw ) ) {
		return ld_parallax_normalize_device_list( $raw[ $apply_key ], $devices );
	}
	if ( 'lqd_parallax' !== $prefix ) {
		return $devices;
	}
	$saw_old = false;
	$apply = array();
	foreach ( $devices as $device ) {
		$key = 'lqd_parallax_disable_' . $device;
		if ( array_key_exists( $key, $raw ) ) {
			$saw_old = true;
		}
		if ( isset( $raw[ $key ] ) && 'yes' === $raw[ $key ] ) {
			continue;
		}
		$apply[] = $device;
	}
	if ( $saw_old ) {
		return $apply;
	}
	return $devices;
}

function ld_parallax_step_payload( $item, $prefix = 'lqd_parallax_step_' ) {
	if ( ! is_array( $item ) ) {
		$item = array();
	}
	$range = ld_parallax_viewport_range( isset( $item[ $prefix . 'range' ] ) ? $item[ $prefix . 'range' ] : null );
	$step = array(
		'start' => $range['start'],
		'end' => $range['end'],
		'values' => ld_parallax_transform_map( $item, $prefix ),
	);
	$ease = ld_parallax_ease_string( isset( $item[ $prefix . 'ease' ] ) ? $item[ $prefix . 'ease' ] : '' );
	if ( null !== $ease ) {
		$step['ease'] = $ease;
	}
	return $step;
}

function ld_parallax_raw_settings( $element ) {
	$settings = $element->get_data( 'settings' );
	return is_array( $settings ) ? $settings : array();
}

function ld_parallax_raw_slider( $source, $key ) {
	if ( ! is_array( $source ) || ! array_key_exists( $key, $source ) ) {
		return null;
	}
	$slider = $source[ $key ];
	if ( ! ld_parallax_slider_size_is_real( $slider ) ) {
		return null;
	}
	return $slider;
}

function ld_parallax_raw_text( $source, $key ) {
	if ( ! is_array( $source ) || ! array_key_exists( $key, $source ) ) {
		return null;
	}
	$value = $source[ $key ];
	if ( null === $value || '' === $value || ! is_scalar( $value ) ) {
		return null;
	}
	return (string) $value;
}

function ld_parallax_origin_override( $raw, $base, $prefix, $device ) {
	$axes = array( 'transformOriginX', 'transformOriginY', 'transformOriginZ' );
	$any = false;
	$parts = array();
	foreach ( $axes as $axis ) {
		$override = ld_parallax_raw_slider( $raw, $prefix . $axis . '_' . $device );
		if ( null !== $override ) {
			$any = true;
			$slider = $override;
		} else {
			$slider = ( is_array( $base ) && isset( $base[ $prefix . $axis ] ) ) ? $base[ $prefix . $axis ] : null;
		}
		$parts[] = ld_parallax_origin_part( $slider );
	}
	if ( ! $any ) {
		return null;
	}
	return implode( ' ', $parts );
}

function ld_parallax_side_overrides( $raw, $base, $prefix, $device ) {
	$out = array();
	foreach ( ld_parallax_transform_props() as $key => $kind ) {
		$slider = ld_parallax_raw_slider( $raw, $prefix . $key . '_' . $device );
		if ( null === $slider ) {
			continue;
		}
		$formatted = ld_parallax_format_prop( $slider, $kind );
		if ( null !== $formatted ) {
			$out[ $key ] = $formatted;
		}
	}
	foreach ( array_keys( ld_parallax_filter_defs() ) as $key ) {
		$slider = ld_parallax_raw_slider( $raw, $prefix . $key . '_' . $device );
		if ( null === $slider ) {
			continue;
		}
		$formatted = ld_parallax_format_prop( $slider, 'length' );
		if ( null !== $formatted ) {
			$out[ $key ] = $formatted;
		}
	}
	$color = ld_parallax_color_value( $raw, $prefix . 'color_' . $device );
	if ( null !== $color ) {
		$out['color'] = $color;
	}
	$origin = ld_parallax_origin_override( $raw, $base, $prefix, $device );
	if ( null !== $origin ) {
		$out['transformOrigin'] = $origin;
	}
	return $out;
}

function ld_parallax_v2_breakpoints( $element, $prefix = 'lqd_parallax' ) {
	$settings = $element->get_settings();
	$raw = ld_parallax_raw_settings( $element );
	$steps_key = $prefix . '_steps';
	$step_prefix = $prefix . '_step_';
	$steps_on = isset( $settings[ $prefix . '_steps_enable' ] ) && 'yes' === $settings[ $prefix . '_steps_enable' ];
	$breakpoints = array();
	$desktop_steps = array();
	$raw_steps = array();

	if ( $steps_on ) {
		$desktop_steps = ( isset( $settings[ $steps_key ] ) && is_array( $settings[ $steps_key ] ) ) ? array_values( $settings[ $steps_key ] ) : array();
		$raw_steps = ( isset( $raw[ $steps_key ] ) && is_array( $raw[ $steps_key ] ) ) ? array_values( $raw[ $steps_key ] ) : array();
	}

	$apply = array_fill_keys( ld_parallax_apply_devices( $element, $prefix ), true );

	foreach ( ld_parallax_v2_device_names() as $device ) {
		$bp = array();
		if ( ! isset( $apply[ $device ] ) ) {
			$bp['disable'] = true;
		}

		if ( 'desktop' !== $device ) {
			if ( $steps_on ) {
				$steps = array();
				$any = false;
				$count = count( $desktop_steps );
				for ( $i = 0; $i < $count; $i++ ) {
					$stop = array();
					$item_raw = ( isset( $raw_steps[ $i ] ) && is_array( $raw_steps[ $i ] ) ) ? $raw_steps[ $i ] : array();
					$item_base = ( isset( $desktop_steps[ $i ] ) && is_array( $desktop_steps[ $i ] ) ) ? $desktop_steps[ $i ] : array();
					$range = ld_parallax_raw_viewport_range( $item_raw, $step_prefix . 'range_' . $device );
					if ( null !== $range ) {
						$stop['start'] = $range['start'];
						$stop['end'] = $range['end'];
					}
					$values = ld_parallax_side_overrides( $item_raw, $item_base, $step_prefix, $device );
					if ( count( $values ) > 0 ) {
						$stop['values'] = $values;
					}
					if ( count( $stop ) > 0 ) {
						$any = true;
						$steps[] = $stop;
					} else {
						// JSON object, not []. Unchanged stops stay {}.
						$steps[] = (object) array();
					}
				}
				if ( $any ) {
					$bp['steps'] = $steps;
				}
				$from = ld_parallax_side_overrides( $raw, $settings, $prefix . '_from_', $device );
				if ( count( $from ) > 0 ) {
					$bp['from'] = $from;
				}
			} else {
				$trigger_key = $prefix . '_settings_trigger';
				$trigger = isset( $settings[ $trigger_key ] ) ? $settings[ $trigger_key ] : '';
				if ( 'custom' === $trigger ) {
					$start = ld_parallax_raw_text( $raw, $prefix . '_settings_trigger_start_' . $device );
					$end = ld_parallax_raw_text( $raw, $prefix . '_settings_trigger_end_' . $device );
					if ( null !== $start ) {
						$bp['start'] = $start;
					}
					if ( null !== $end ) {
						$bp['end'] = $end;
					}
				}
				$from = ld_parallax_side_overrides( $raw, $settings, $prefix . '_from_', $device );
				$to = ld_parallax_side_overrides( $raw, $settings, $prefix . '_to_', $device );
				if ( count( $from ) > 0 ) {
					$bp['from'] = $from;
				}
				if ( count( $to ) > 0 ) {
					$bp['to'] = $to;
				}
			}
		}

		if ( count( $bp ) > 0 ) {
			$breakpoints[ $device ] = $bp;
		}
	}

	return $breakpoints;
}

function ld_parallax_v2_add_breakpoints_attribute( $element, $breakpoints ) {
	if ( empty( $breakpoints ) ) {
		return;
	}
	$element->add_render_attribute( '_wrapper', array(
		'data-parallax-breakpoints' => wp_json_encode( $breakpoints ),
	) );
}

function ld_el_parallax_render_v2( $element ) {
	$breakpoints = ld_parallax_v2_breakpoints( $element );
	if ( 'yes' === $element->get_settings( 'lqd_parallax_steps_enable' ) ) {
		ld_parallax_v2_render_steps( $element, $breakpoints );
		return;
	}
	ld_parallax_v2_render_classic( $element, $breakpoints );
}

function ld_parallax_v2_render_steps( $element, $breakpoints ) {
	$settings = $element->get_settings();
	$items = ( isset( $settings['lqd_parallax_steps'] ) && is_array( $settings['lqd_parallax_steps'] ) ) ? $settings['lqd_parallax_steps'] : array();
	$steps = array();
	foreach ( $items as $item ) {
		$steps[] = ld_parallax_step_payload( $item );
	}
	$opts = array();
	$ease = ld_parallax_ease_string( isset( $settings['lqd_parallax_settings_ease'] ) ? $settings['lqd_parallax_settings_ease'] : '' );
	if ( null !== $ease ) {
		$opts['ease'] = $ease;
	}
	$perspective = ld_parallax_perspective_value( $settings );
	if ( null !== $perspective ) {
		$opts['transformPerspective'] = $perspective;
	}
	$from = ld_parallax_transform_map( $settings, 'lqd_parallax_from_' );
	$attributes = array(
		'data-parallax' => 'true',
		'data-parallax-options' => wp_json_encode( $opts ),
		'data-parallax-steps' => wp_json_encode( $steps ),
		'data-parallax-from' => wp_json_encode( $from ),
	);
	if ( ! empty( $breakpoints ) ) {
		$attributes['data-parallax-breakpoints'] = wp_json_encode( $breakpoints );
	}
	$element->add_render_attribute( '_wrapper', $attributes );
	ld_parallax_v2_add_apply_attribute( $element );
}

function ld_parallax_v2_render_classic( $element, $breakpoints ) {
            $from_x = $element->get_settings( 'lqd_parallax_from_x' );
            $from_y = $element->get_settings( 'lqd_parallax_from_y' );
            $from_z = $element->get_settings( 'lqd_parallax_from_z' );

            $from_scaleX = $element->get_settings( 'lqd_parallax_from_scaleX' );
            $from_scaleY = $element->get_settings( 'lqd_parallax_from_scaleY' );

            $from_rotationX = $element->get_settings( 'lqd_parallax_from_rotationX' );
            $from_rotationY = $element->get_settings( 'lqd_parallax_from_rotationY' );
            $from_rotationZ = $element->get_settings( 'lqd_parallax_from_rotationZ' );

            $from_opacity = $element->get_settings( 'lqd_parallax_from_opacity' );

            $from_transformOriginX = $element->get_settings( 'lqd_parallax_from_transformOriginX' );
            $from_transformOriginY = $element->get_settings( 'lqd_parallax_from_transformOriginY' );
            $from_transformOriginZ = $element->get_settings( 'lqd_parallax_from_transformOriginZ' );

            $to_x = $element->get_settings( 'lqd_parallax_to_x' );
            $to_y = $element->get_settings( 'lqd_parallax_to_y' );
            $to_z = $element->get_settings( 'lqd_parallax_to_z' );

            $to_scaleX = $element->get_settings( 'lqd_parallax_to_scaleX' );
            $to_scaleY = $element->get_settings( 'lqd_parallax_to_scaleY' );

            $to_rotationX = $element->get_settings( 'lqd_parallax_to_rotationX' );
            $to_rotationY = $element->get_settings( 'lqd_parallax_to_rotationY' );
            $to_rotationZ = $element->get_settings( 'lqd_parallax_to_rotationZ' );

            $to_opacity = $element->get_settings( 'lqd_parallax_to_opacity' );

            $to_transformOriginX = $element->get_settings( 'lqd_parallax_to_transformOriginX' );
            $to_transformOriginY = $element->get_settings( 'lqd_parallax_to_transformOriginY' );
            $to_transformOriginZ = $element->get_settings( 'lqd_parallax_to_transformOriginZ' );

            $parallax_ease = $element->get_settings( 'lqd_parallax_settings_ease' );
            $parallax_duration = $element->get_settings( 'lqd_parallax_settings_duration' );
            $parallax_trigger = $element->get_settings( 'lqd_parallax_settings_trigger' );
            $parallax_trigger_start = $element->get_settings( 'lqd_parallax_settings_trigger_start' );
            $parallax_trigger_end = $element->get_settings( 'lqd_parallax_settings_trigger_end' );

            $wrapper_attributes = $parallax_data = $parallax_data_from = $parallax_data_to = $parallax_opts = array();

            $perspective_value = ld_parallax_perspective_value( $element->get_settings() );
            if ( null !== $perspective_value ) {
                $parallax_data_from['transformPerspective'] = $perspective_value;
                $parallax_data_to['transformPerspective'] = $perspective_value;
            }

            if ( !empty( $from_x ) && !empty( $to_x ) && $from_x != $to_x ) {
                $parallax_data_from['x'] = $from_x['size'].$from_x['unit'];
                $parallax_data_to['x'] = $to_x['size'].$to_x['unit'];
            }
            if ( !empty( $from_y ) && !empty( $to_y ) && $from_y != $to_y ) {
                $parallax_data_from['y'] = $from_y['size'].$from_y['unit'];
                $parallax_data_to['y'] = $to_y['size'].$to_y['unit'];
            }
            if ( !empty( $from_z ) && !empty( $to_z ) && $from_z != $to_z ) {
                $parallax_data_from['z'] = $from_z['size'].$from_z['unit'];
                $parallax_data_to['z'] = $to_z['size'].$to_z['unit'];
            }

            if ( !empty( $from_scaleX ) && !empty( $to_scaleX ) && $from_scaleX != $to_scaleX ) {
                $parallax_data_from['scaleX'] = (float) $from_scaleX['size'];
                $parallax_data_to['scaleX'] = (float) $to_scaleX['size'];
            }
            if ( !empty( $from_scaleY ) && !empty( $to_scaleY ) && $from_scaleY != $to_scaleY ) {
                $parallax_data_from['scaleY'] = (float) $from_scaleY['size'];
                $parallax_data_to['scaleY'] = (float) $to_scaleY['size'];
            }

            if ( !empty( $from_rotationX ) && !empty( $to_rotationX ) && $from_rotationX != $to_rotationX ) {
                $parallax_data_from['rotationX'] = (int) $from_rotationX['size'];
                $parallax_data_to['rotationX'] = (int) $to_rotationX['size'];
            }
            if ( !empty( $from_rotationY ) && !empty( $to_rotationY ) && $from_rotationY != $to_rotationY ) {
                $parallax_data_from['rotationY'] = (int) $from_rotationY['size'];
                $parallax_data_to['rotationY'] = (int) $to_rotationY['size'];
            }
            if ( !empty( $from_rotationZ ) && !empty( $to_rotationZ ) && $from_rotationZ != $to_rotationZ ) {
                $parallax_data_from['rotationZ'] = (int) $from_rotationZ['size'];
                $parallax_data_to['rotationZ'] = (int) $to_rotationZ['size'];
            }

            if ( !empty( $from_opacity ) && !empty( $to_opacity ) && $from_opacity != $to_opacity ) {
                $parallax_data_from['opacity'] = (float) $from_opacity['size'];
                $parallax_data_to['opacity'] = (float) $to_opacity['size'];
            }

            $filter_settings = $element->get_settings();
            $from_filters = ld_parallax_collect_filters( $filter_settings, 'lqd_parallax_from_' );
            $to_filters = ld_parallax_collect_filters( $filter_settings, 'lqd_parallax_to_' );
            if ( $from_filters || $to_filters ) {
                if ( ! $from_filters ) {
                    $from_filters = ld_parallax_filter_identity_map();
                }
                if ( ! $to_filters ) {
                    $to_filters = ld_parallax_filter_identity_map();
                }
                $parallax_data_from = array_merge( $parallax_data_from, $from_filters );
                $parallax_data_to = array_merge( $parallax_data_to, $to_filters );
            }

            $from_toriginX = $from_transformOriginX['size'].$from_transformOriginX['unit'];
            $from_toriginY = $from_transformOriginY['size'].$from_transformOriginY['unit'];
            $from_toriginZ = $from_transformOriginZ['size'].$from_transformOriginZ['unit'];

            $to_toriginX = $to_transformOriginX['size'].$to_transformOriginX['unit'];
            $to_toriginY = $to_transformOriginY['size'].$to_transformOriginY['unit'];
            $to_toriginZ = $to_transformOriginZ['size'].$to_transformOriginZ['unit'];

            $parallax_data_from['transformOrigin'] = $from_toriginX . ' ' . $from_toriginY . ' ' . $from_toriginZ;
            $parallax_data_to['transformOrigin'] = $to_toriginX . ' ' . $to_toriginY . ' ' . $to_toriginZ;

            //Parallax general options
            $parallax_data['from'] = $parallax_data_from;
            $parallax_data['to'] = $parallax_data_to;

            if( is_array( $parallax_data['from'] ) && ! empty( $parallax_data['from'] ) ) {
                $wrapper_attributes[] = 'data-parallax-from=\'' . wp_json_encode( $parallax_data['from'] ) . '\'';
            }
            if( is_array( $parallax_data['to'] ) && ! empty( $parallax_data['to'] ) ) {
                $wrapper_attributes[] = 'data-parallax-to=\'' . wp_json_encode( $parallax_data['to'] ) . '\'';
            }

            $parallax_ease = ld_parallax_ease_string( $parallax_ease );
            if ( null !== $parallax_ease ) { $parallax_opts['ease'] = $parallax_ease; }
            if( 'custom' !== $parallax_trigger ){
                $parallax_opts['start'] = esc_attr( $parallax_trigger );
                if ( isset($parallax_duration) && ! empty($parallax_duration) ) {
                    $parallax_duration_size = (float) $parallax_duration['size'];
                    $dur = $parallax_duration_size >= 0 ? '+='.abs($parallax_duration_size).$parallax_duration['unit'].'' : '-='.abs($parallax_duration_size).$parallax_duration['unit'].'';
                    $parallax_opts['end'] = esc_attr( 'bottom'  . $dur . ' top' );
                }
            } else {
                if ( ! empty( $parallax_trigger_start ) ) {
                    $parallax_opts['start'] = esc_attr( $parallax_trigger_start );
                }
                if ( ! empty( $parallax_trigger_end ) ) {
                    $parallax_opts['end'] = esc_attr( $parallax_trigger_end );
                }
            }
            if( ! empty( $parallax_opts ) ) {
                $wrapper_attributes[] = 'data-parallax-options=\'' . wp_json_encode( $parallax_opts ) .'\'';
            }

            $element->add_render_attribute( '_wrapper', [
                'data-parallax' => 'true',
                'data-parallax-options' => wp_json_encode( $parallax_opts ),
                'data-parallax-from' => wp_json_encode( $parallax_data['from'] ),
                'data-parallax-to' => wp_json_encode( $parallax_data['to'] ),
            ] );

	ld_parallax_v2_add_breakpoints_attribute( $element, $breakpoints );
	ld_parallax_v2_add_apply_attribute( $element );
}

function ld_parallax_has_explicit_apply( $element ) {
	return ! ld_parallax_follows_global( $element );
}

function ld_parallax_v2_add_apply_attribute( $element ) {
	if ( ! ld_parallax_has_explicit_apply( $element ) ) {
		return;
	}
	$element->add_render_attribute( '_wrapper', array(
		'data-parallax-apply' => wp_json_encode( ld_parallax_apply_devices( $element ) ),
	) );
}
