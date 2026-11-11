<?php

namespace RadmanCTChild\Blog\Sidebar;

defined( 'ABSPATH' ) || exit;

class Recent_Posts extends \WP_Widget {
	public function __construct() {
		parent::__construct( 'rdct-blog-posts', __( 'Articles with thumbnails', 'radmanct-child' ), array( 'classname' => 'rdct-blog-recent', 'customize_selective_refresh' => true, 'show_instance_in_rest' => true ) );
	}

	public function widget( $args, $instance ) {
		$query = new \WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max( 1, min( 8, (int) ( $instance['number'] ?? 6 ) ) ), 'no_found_rows' => true, 'ignore_sticky_posts' => true, 'has_password' => false, 'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ), 'post__not_in' => is_singular( 'post' ) ? array( get_queried_object_id() ) : array() ) );
		if ( ! $query->posts ) { return; }
		\RadmanCT\BlogMedia\prime( $query->posts );
		\RadmanCT\CardMedia\prime( $query->posts );
		\RadmanCTChild\Blog\card_assets();
		if ( did_action( 'wp_head' ) ) { wp_print_styles( 'rdct-client-blog-cards' ); }
		$title = apply_filters( 'widget_title', $instance['title'] ?? __( 'Latest articles', 'radmanct-child' ), $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) { echo $args['before_title'] . esc_html( $title ) . $args['after_title']; }
		echo '<ul class="rdct-blog-recent__list">';
		foreach ( $query->posts as $entry ) {
			echo '<li>';
			get_template_part( 'template-parts/blog/shared/post-card', null, array( 'variant' => 'compact', 'post_id' => $entry->ID ) );
			echo '</li>';
		}
		echo '</ul>' . $args['after_widget'];
	}

	public function form( $instance ) {
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . esc_html__( 'Title' ) . '</label><input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" value="' . esc_attr( $instance['title'] ?? __( 'Latest articles', 'radmanct-child' ) ) . '"></p>';
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'number' ) ) . '">' . esc_html__( 'Number of posts to show:' ) . '</label> <input type="number" min="1" max="8" id="' . esc_attr( $this->get_field_id( 'number' ) ) . '" name="' . esc_attr( $this->get_field_name( 'number' ) ) . '" value="' . (int) ( $instance['number'] ?? 6 ) . '"></p>';
	}

	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ?? '' ), 'number' => max( 1, min( 8, (int) ( $new_instance['number'] ?? 6 ) ) ) );
	}
}
