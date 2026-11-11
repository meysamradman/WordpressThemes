<?php

namespace RadmanCT\UI;

defined( 'ABSPATH' ) || exit;

function category_widget( $instance, $widget, $args ) {
	if ( ! function_exists( '\\RadmanCTComponents\\UI\\category_tree' ) ) { return $instance; }
	if ( false === $instance || 'categories' !== $widget->id_base || ! empty( $instance['dropdown'] ) ) { return $instance; }
	$sidebar = \RadmanCT\Sidebars\current();
	if ( ! $sidebar || ! str_starts_with( $sidebar['scope'], 'blog_' ) || $sidebar['area'] !== ( $args['id'] ?? '' ) ) { return $instance; }
	$options = apply_filters( 'widget_categories_args', array( 'orderby' => 'name', 'show_count' => ! empty( $instance['count'] ), 'hierarchical' => ! empty( $instance['hierarchical'] ), 'title_li' => '' ), $instance );
	if ( has_filter( 'wp_list_categories' ) || ! empty( $options['depth'] ) || ! empty( $options['walker'] ) || ! empty( $options['feed'] ) || ( ! empty( $options['taxonomy'] ) && 'category' !== $options['taxonomy'] ) ) { return $instance; }
	$query = array_intersect_key( $options, array_flip( array( 'orderby', 'order', 'include', 'exclude', 'exclude_tree', 'hide_empty', 'number', 'child_of', 'parent', 'depth' ) ) );
	$query['taxonomy'] = 'category';
	$query['pad_counts'] = ! empty( $options['hierarchical'] ) && ! empty( $options['show_count'] );
	$terms = get_terms( $query );
	if ( is_wp_error( $terms ) || ! $terms ) { return $instance; }
	$title = apply_filters( 'widget_title', ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'default' ), $instance, $widget->id_base );
	$current = is_category() ? get_queried_object_id() : 0;
	require_once get_template_directory() . '/inc/ui/category-tree.php';
	echo $args['before_widget'];
	if ( $title ) { echo $args['before_title'] . esc_html( $title ) . $args['after_title']; }
	\RadmanCT\UI\category_tree( $terms, array( 'link_attributes' => static fn(): array => array( 'data-rdct-archive-link' => '' ), 'current' => $current, 'ancestors' => $current ? get_ancestors( $current, 'category', 'taxonomy' ) : array(), 'counts' => ! empty( $options['show_count'] ), 'hierarchical' => ! empty( $options['hierarchical'] ) ) );
	echo $args['after_widget'];
	return false;
}

add_filter( 'widget_display_callback', __NAMESPACE__ . '\category_widget', 20, 3 );
