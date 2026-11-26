<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor product reviews widget.
 *
 * Elementor widget that displays the WooCommerce single product reviews list and review form.
 *
 * @since 1.0.0
 */
class LD_Woo_Product_Reviews extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ld_woo_product_reviews';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Liquid Product Reviews', 'hub-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-review lqd-element';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'hub-woo' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'woocommerce', 'reviews', 'comments', 'rating', 'product' ];
	}

	/**
	 * Register widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'liquid_wc_single_product_reviews_heading_typo',
				'label' => __( 'Heading Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} #reviews #comments h2, {{WRAPPER}} #review_form #respond .comment-reply-title',
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_heading_color',
			[
				'label' => esc_html__( 'Heading Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments h2, {{WRAPPER}} #review_form #respond .comment-reply-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'liquid_wc_single_product_reviews_form_typo',
				'label' => __( 'Form Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} #review_form form',
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_wrapper_bg',
			[
				'label' => esc_html__( 'Form Wrapper Bg', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_input_color',
			[
				'label' => esc_html__( 'Form Input Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_input_color_hover',
			[
				'label' => esc_html__( 'Form Input Hover/Active Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form p.input-filled, {{WRAPPER}} #review_form #respond .comment-form p.input-focused' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_input_border_color',
			[
				'label' => esc_html__( 'Form Input Border Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form p::before' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_input_border_color_hover',
			[
				'label' => esc_html__( 'Form Input Border Active Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form p.input-filled::before, {{WRAPPER}} #review_form #respond .comment-form p.input-focused::before, {{WRAPPER}} #review_form #respond .comment-form p.input-filled::after, {{WRAPPER}} #review_form #respond .comment-form p.input-focused::after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_stars_color',
			[
				'label' => esc_html__( 'Stars Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form .stars, {{WRAPPER}} div.product .star-rating' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_form_stars_border_color',
			[
				'label' => esc_html__( 'Stars Border Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #review_form #respond .comment-form .comment-form-rating' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_review_bg',
			[
				'label' => esc_html__( 'Review Backgorund', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_review_border_color',
			[
				'label' => esc_html__( 'Review Border Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_review_text_color',
			[
				'label' => esc_html__( 'Review Text Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_date_color',
			[
				'label' => esc_html__( 'Review Date Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text .meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'liquid_wc_single_product_reviews_author_color',
			[
				'label' => esc_html__( 'Review Author Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text .woocommerce-review__author' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function get_woo_item() {
		$posts = get_posts(  array(
			'numberposts' => 1,
			'post_type'   => 'product'
		  ));
		  $postid;
		  foreach ( $posts as $post ) {
			$postid = $post->ID;
		  }
		  return $postid;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		// check
		if( !liquid_helper()->is_woocommerce_active() ) {
			return;
		}

		global $product, $post, $withcomments;

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ){

			$post_id = $this->get_woo_item();

			if ( empty( $post_id ) ) { return; }

			$post = get_post( $post_id );
			$product = wc_get_product( $post_id );
			setup_postdata( $post );
			$withcomments = true;
			?>
			<div class="woocommerce">
			<div class="product" style="padding:0">
			<div class="product product-layout-component lqd-product-reviews">
				<?php comments_template(); ?>
			</div>
			</div>
			</div>
			<?php
			wp_reset_postdata();

		} else {

			$product = wc_get_product();

			if ( empty( $product ) ) { return; }
			?>
			<div class="product product-layout-component lqd-product-reviews">
				<?php comments_template(); ?>
			</div>
			<?php
		}

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Woo_Product_Reviews() );
