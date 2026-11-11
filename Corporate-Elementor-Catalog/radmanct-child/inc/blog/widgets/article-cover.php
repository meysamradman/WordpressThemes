<?php

namespace RadmanCTChild\Blog\Sidebar;

defined( 'ABSPATH' ) || exit;

class Article_Cover extends \WP_Widget {
	public function __construct() {
		parent::__construct( 'rdct-article-cover', __( 'Current article cover', 'radmanct-child' ), array( 'classname' => 'rdct-article-cover-widget', 'customize_selective_refresh' => true, 'show_instance_in_rest' => true ) );
	}

	public function widget( $args, $instance ) {
		if ( ! is_singular( 'post' ) || post_password_required( get_queried_object_id() ) ) { return; }
		echo $args['before_widget'];
		get_template_part( 'template-parts/blog/single/cover', null, array( 'post_id' => get_queried_object_id() ) );
		echo $args['after_widget'];
	}
}
