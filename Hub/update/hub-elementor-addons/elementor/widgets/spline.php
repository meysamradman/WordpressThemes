<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Liquid Spline 3D widget.
 *
 * Embeds an interactive Spline (spline.design) scene, either through the
 * official `<spline-viewer>` web component or a plain iframe.
 *
 * @since 1.0.0
 */
class LD_Spline extends Widget_Base {

	/**
	 * Pinned @splinetool/viewer version served from unpkg.
	 * Override with the `liquid_spline_viewer_version` or `liquid_spline_viewer_src` filters.
	 */
	const VIEWER_VERSION = '2.0.53';

	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		wp_register_script(
			'liquid-spline',
			LD_ELEMENTOR_URL . 'assets/js/spline/liquid-spline.min.js',
			[ 'jquery' ],
			LD_ELEMENTOR_VERSION,
			true
		);

	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		if ( liquid_helper()->liquid_elementor_script_depends() ) {
			return [ 'liquid-spline' ];
		} else {
			return [ '' ];
		}
	}

	public function get_name() {
		return 'ld_spline';
	}

	public function get_title() {
		return __( 'Liquid Spline 3D', 'hub-elementor-addons' );
	}

	public function get_icon() {
		return 'eicon-slider-3d lqd-element';
	}

	public function get_categories() {
		return [ 'hub-core' ];
	}

	public function get_keywords() {
		return [ 'spline', '3d', 'scene', 'webgl', 'interactive' ];
	}

	/**
	 * Resolve the `<spline-viewer>` module URL for the chosen renderer.
	 *
	 * Spline ships engine-specific builds that are noticeably smaller than the
	 * dual-engine default, so we pick the matching one when the user forces a renderer.
	 *
	 * @param string $renderer auto|webgl|webgpu
	 * @return string
	 */
	public static function get_viewer_src( $renderer = 'auto' ) {

		$version = apply_filters( 'liquid_spline_viewer_version', self::VIEWER_VERSION );

		switch ( $renderer ) {
			case 'webgl':
				$build = 'spline-viewer.webgl.js';
				break;
			case 'webgpu':
				$build = 'spline-viewer.webgpu.js';
				break;
			default:
				$build = 'spline-viewer.js';
		}

		$src = sprintf( 'https://unpkg.com/@splinetool/viewer@%s/build/%s', $version, $build );

		return apply_filters( 'liquid_spline_viewer_src', $src, $renderer, $version );

	}

	protected function register_controls() {

		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'Scene', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'embed_type',
			[
				'label' => __( 'Embed Type', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'viewer',
				'options' => [
					'viewer' => __( 'Spline Viewer (recommended)', 'hub-elementor-addons' ),
					'iframe' => __( 'Iframe', 'hub-elementor-addons' ),
				],
				'description' => __( 'The viewer renders directly on the page (transparent background, global mouse events, better control). Use the iframe only if you just have a public share link.', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'scene_url',
			[
				'label' => __( 'Scene URL', 'hub-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://prod.spline.design/xxxxxxxx/scene.splinecode',
				'options' => false,
				'dynamic' => [
					'active' => true,
				],
				'description' => __( 'In Spline: Export → Spline Viewer → copy the scene URL (ends with .splinecode).', 'hub-elementor-addons' ),
				'condition' => [
					'embed_type' => 'viewer',
				],
			]
		);

		$this->add_control(
			'iframe_url',
			[
				'label' => __( 'Public URL', 'hub-elementor-addons' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://my.spline.design/xxxxxxxx/',
				'options' => false,
				'dynamic' => [
					'active' => true,
				],
				'description' => __( 'In Spline: Export → Public URL.', 'hub-elementor-addons' ),
				'condition' => [
					'embed_type' => 'iframe',
				],
			]
		);

		$this->add_control(
			'watermark_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Scenes exported on a free Spline plan display a Spline logo. This is controlled by your Spline plan/export settings, not by the widget.', 'hub-elementor-addons' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'poster',
			[
				'label' => __( 'Poster Image', 'hub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'description' => __( 'Shown while the scene loads, and as a fallback when the scene is disabled on mobile.', 'hub-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'poster_size',
				'default' => 'full',
				'condition' => [
					'poster[url]!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'viewer_section',
			[
				'label' => __( 'Viewer Options', 'hub-elementor-addons' ),
				'condition' => [
					'embed_type' => 'viewer',
				],
			]
		);

		$this->add_control(
			'events_target',
			[
				'label' => __( 'Mouse Events', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'local',
				'options' => [
					'local' => __( 'Inside the scene only', 'hub-elementor-addons' ),
					'global' => __( 'Whole page', 'hub-elementor-addons' ),
				],
				'description' => __( 'Use "Whole page" for scenes that follow the cursor, so they keep reacting when the mouse leaves the canvas.', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'disable_interaction',
			[
				'label' => __( 'Disable Interaction', 'hub-elementor-addons' ),
				'description' => __( 'Lets clicks and scroll pass through the scene. Useful for decorative backgrounds.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'hint',
			[
				'label' => __( 'Show Drag Hint', 'hub-elementor-addons' ),
				'description' => __( 'Displays a small drag animation until the user interacts with the scene.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'disable_interaction!' => 'yes',
				],
			]
		);

		$this->add_control(
			'viewer_background',
			[
				'label' => __( 'Background Override', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'description' => __( 'Overrides the background color set in the Spline editor. Leave empty to keep the scene background.', 'hub-elementor-addons' ),
				'render_type' => 'template',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'loading_anim_type',
			[
				'label' => __( 'Loading Spinner', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'spinner-small-dark' => __( 'Small – Dark', 'hub-elementor-addons' ),
					'spinner-small-light' => __( 'Small – Light', 'hub-elementor-addons' ),
					'spinner-big-dark' => __( 'Big – Dark', 'hub-elementor-addons' ),
					'spinner-big-light' => __( 'Big – Light', 'hub-elementor-addons' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'performance_section',
			[
				'label' => __( 'Performance', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'performance_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Spline runtime and scenes are heavy (several MB). Keep lazy loading on and consider disabling the scene on mobile with a poster image.', 'hub-elementor-addons' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'loading',
			[
				'label' => __( 'Loading', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'lazy',
				'options' => [
					'lazy' => __( 'Lazy (when near viewport)', 'hub-elementor-addons' ),
					'eager' => __( 'Eager (on page load)', 'hub-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'unloadable',
			[
				'label' => __( 'Unload When Off-screen', 'hub-elementor-addons' ),
				'description' => __( 'Frees GPU/memory when the scene leaves the viewport and reloads it when it comes back.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'embed_type' => 'viewer',
				],
			]
		);

		$this->add_control(
			'renderer',
			[
				'label' => __( 'Renderer', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => __( 'Auto (WebGPU when available)', 'hub-elementor-addons' ),
					'webgl' => __( 'WebGL only (smaller download)', 'hub-elementor-addons' ),
					'webgpu' => __( 'WebGPU only (no WebGL fallback)', 'hub-elementor-addons' ),
				],
				'description' => __( 'Must match the "Renderer" setting your scene was exported with. WebGL is the safe choice for most scenes.', 'hub-elementor-addons' ),
				'condition' => [
					'embed_type' => 'viewer',
				],
			]
		);

		$this->add_control(
			'disable_on_mobile',
			[
				'label' => __( 'Disable on Mobile', 'hub-elementor-addons' ),
				'description' => __( 'Shows only the poster image on small screens.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label' => __( 'Mobile Breakpoint (px)', 'hub-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 320,
				'max' => 1600,
				'step' => 1,
				'default' => 767,
				'condition' => [
					'disable_on_mobile' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Layout', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1600,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'vw' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'hub-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Start', 'hub-elementor-addons' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'hub-elementor-addons' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __( 'End', 'hub-elementor-addons' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'display: flex; justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .lqd-spline',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => __( 'Blend Mode', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'hub-elementor-addons' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline' => 'mix-blend-mode: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'poster_style_section',
			[
				'label' => __( 'Poster', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'poster[url]!' => '',
				],
			]
		);

		$this->add_control(
			'poster_fit',
			[
				'label' => __( 'Object Fit', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover' => __( 'Cover', 'hub-elementor-addons' ),
					'contain' => __( 'Contain', 'hub-elementor-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline-poster img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'poster_position',
			[
				'label' => __( 'Object Position', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'center center' => __( 'Center Center', 'hub-elementor-addons' ),
					'center left' => __( 'Center Left', 'hub-elementor-addons' ),
					'center right' => __( 'Center Right', 'hub-elementor-addons' ),
					'top center' => __( 'Top Center', 'hub-elementor-addons' ),
					'top left' => __( 'Top Left', 'hub-elementor-addons' ),
					'top right' => __( 'Top Right', 'hub-elementor-addons' ),
					'bottom center' => __( 'Bottom Center', 'hub-elementor-addons' ),
					'bottom left' => __( 'Bottom Left', 'hub-elementor-addons' ),
					'bottom right' => __( 'Bottom Right', 'hub-elementor-addons' ),
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline-poster img' => 'object-position: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'poster_fade_duration',
			[
				'label' => __( 'Fade Out Duration (ms)', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
						'step' => 50,
					],
				],
				'default' => [
					'size' => 600,
				],
				'selectors' => [
					'{{WRAPPER}} .lqd-spline-poster' => 'transition-duration: {{SIZE}}ms;',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function get_data_options( $settings, $embed_type ) {

		$options = [
			'type' => $embed_type,
			'loading' => ! empty( $settings['loading'] ) ? $settings['loading'] : 'lazy',
			'disableOnMobile' => 'yes' === $settings['disable_on_mobile'],
			'mobileBreakpoint' => ! empty( $settings['mobile_breakpoint'] ) ? (int) $settings['mobile_breakpoint'] : 767,
		];

		if ( 'viewer' === $embed_type ) {
			$options['src'] = self::get_viewer_src( $settings['renderer'] );
		}

		return wp_json_encode( $options );

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$embed_type = 'iframe' === $settings['embed_type'] ? 'iframe' : 'viewer';
		$url_key = 'iframe' === $embed_type ? 'iframe_url' : 'scene_url';
		$scene_url = ! empty( $settings[ $url_key ]['url'] ) ? esc_url( $settings[ $url_key ]['url'] ) : '';

		if ( empty( $scene_url ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				?>
				<div class="lqd-spline lqd-spline-placeholder">
					<i class="eicon-slider-3d" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Add your Spline scene URL to get started.', 'hub-elementor-addons' ); ?></span>
				</div>
				<?php
			}
			return;
		}

		$has_poster = ! empty( $settings['poster']['url'] );

		$this->add_render_attribute( 'wrapper', 'class', [
			'lqd-spline',
			'lqd-spline-type-' . $embed_type,
			'pos-rel',
			'overflow-hidden',
		] );

		if ( $has_poster ) {
			$this->add_render_attribute( 'wrapper', 'class', 'lqd-spline-has-poster' );
		}

		if ( 'yes' === $settings['disable_interaction'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'pointer-events-none' );
		}

		$this->add_render_attribute( 'wrapper', 'data-lqd-spline', '' );
		$this->add_render_attribute( 'wrapper', 'data-spline-options', $this->get_data_options( $settings, $embed_type ) );

		if ( 'viewer' === $embed_type ) {

			$this->add_render_attribute( 'viewer', [
				'class' => 'lqd-spline-viewer d-block w-100 h-100',
				'url' => $scene_url,
				'loading' => 'eager' === $settings['loading'] ? 'eager' : 'lazy',
				'events-target' => 'global' === $settings['events_target'] ? 'global' : 'local',
			] );

			if ( ! empty( $settings['renderer'] ) && 'auto' !== $settings['renderer'] ) {
				$this->add_render_attribute( 'viewer', 'renderer', $settings['renderer'] );
			}

			if ( ! empty( $settings['viewer_background'] ) ) {
				$this->add_render_attribute( 'viewer', 'background', $settings['viewer_background'] );
			}

			if ( ! empty( $settings['loading_anim_type'] ) ) {
				$this->add_render_attribute( 'viewer', 'loading-anim-type', $settings['loading_anim_type'] );
			}

			if ( 'yes' === $settings['hint'] && 'yes' !== $settings['disable_interaction'] ) {
				$this->add_render_attribute( 'viewer', 'hint', '' );
			}

			if ( 'yes' === $settings['unloadable'] ) {
				$this->add_render_attribute( 'viewer', 'unloadable', '' );
			}

		} else {

			$this->add_render_attribute( 'iframe', [
				'class' => 'lqd-spline-iframe lqd-overlay',
				'src' => $scene_url,
				'frameborder' => '0',
				'loading' => 'eager' === $settings['loading'] ? 'eager' : 'lazy',
				'allow' => 'autoplay; fullscreen; xr-spatial-tracking',
				'title' => __( 'Spline 3D scene', 'hub-elementor-addons' ),
			] );

		}

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'viewer' === $embed_type ) : ?>
				<spline-viewer <?php $this->print_render_attribute_string( 'viewer' ); ?>></spline-viewer>
			<?php else : ?>
				<iframe <?php $this->print_render_attribute_string( 'iframe' ); ?>></iframe>
			<?php endif; ?>
			<?php if ( $has_poster ) : ?>
				<figure class="lqd-spline-poster lqd-overlay pointer-events-none">
					<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'poster_size', 'poster' ); ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Spline() );
