<?php

namespace RadmanCT\UI;

defined( 'ABSPATH' ) || exit;

function search_widget( $instance, $widget, $args ) {
	if ( false === $instance || 'search' !== $widget->id_base || ! function_exists( 'rdct_archive_search' ) ) { return $instance; }
	$sidebar = \RadmanCT\Sidebars\current();
	if ( ! $sidebar || ! str_starts_with( $sidebar['scope'], 'blog_' ) || $sidebar['area'] !== ( $args['id'] ?? '' ) ) { return $instance; }
	$filter = static function ( array $options ): array {
		return array_replace( $options, array( 'post_type' => 'post', 'ajax' => true, 'layout' => 'stacked', 'show_label' => true, 'label' => __( 'Search articles', 'radmanct' ), 'placeholder' => '' ) );
	};
	add_filter( 'radmanct/search/options', $filter );
	try { $widget->widget( $args, $instance ); }
	finally { remove_filter( 'radmanct/search/options', $filter ); }
	return false;
}

add_filter( 'widget_display_callback', __NAMESPACE__ . '\search_widget', 20, 3 );
