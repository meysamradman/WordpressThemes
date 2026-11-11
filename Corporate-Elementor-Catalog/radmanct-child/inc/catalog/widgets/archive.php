<?php

namespace RadmanCTChild\Catalog\Sidebar;

defined( 'ABSPATH' ) || exit;

class Archive_Widget extends \WP_Widget {
	public function __construct() {
		parent::__construct( 'rdct-catalog-section', __( 'Products', 'radmanct-content' ), array( 'classname' => 'rdct-catalog-widget', 'customize_selective_refresh' => true, 'show_instance_in_rest' => true ) );
	}

	public function widget( $args, $instance ) {
		if ( ! post_type_exists( 'radman_product' ) ) { return; }
		$kind = in_array( $instance['kind'] ?? '', array( 'search', 'categories', 'recent', 'tags' ), true ) ? $instance['kind'] : 'categories';
		$title = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) { echo $args['before_title'] . esc_html( $title ) . $args['after_title']; }
		get_template_part( 'template-parts/catalog/sidebar/' . $kind, null, array( 'number' => max( 1, min( 8, (int) ( $instance['number'] ?? 6 ) ) ) ) );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$choices = array( 'search' => __( 'Search products', 'radmanct' ), 'categories' => get_taxonomy( 'radman_product_cat' )->label, 'recent' => __( 'Products', 'radmanct-content' ), 'tags' => __( 'Tags', 'default' ) );
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Title' ) . '</label><input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $instance['title'] ?? '' ) . '"></p>';
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'kind' ) ) . '">' . esc_html__( 'Type' ) . '</label><select class="widefat" id="' . esc_attr( $this->get_field_id( 'kind' ) ) . '" name="' . esc_attr( $this->get_field_name( 'kind' ) ) . '">';
		foreach ( $choices as $value => $label ) { echo '<option value="' . esc_attr( $value ) . '"' . selected( $instance['kind'] ?? 'categories', $value, false ) . '>' . esc_html( $label ) . '</option>'; }
		echo '</select></p><p><label for="' . esc_attr( $this->get_field_id( 'number' ) ) . '">' . esc_html__( 'Number of posts to show:' ) . '</label> <input type="number" min="1" max="8" id="' . esc_attr( $this->get_field_id( 'number' ) ) . '" name="' . esc_attr( $this->get_field_name( 'number' ) ) . '" value="' . (int) ( $instance['number'] ?? 6 ) . '"></p>';
	}

	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ?? '' ), 'kind' => in_array( $new_instance['kind'] ?? '', array( 'search', 'categories', 'recent', 'tags' ), true ) ? $new_instance['kind'] : 'categories', 'number' => max( 1, min( 8, (int) ( $new_instance['number'] ?? 6 ) ) ) );
	}
}
