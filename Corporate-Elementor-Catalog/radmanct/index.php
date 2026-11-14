<?php
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! \RadmanCT\Renderers\render() ) {
	if ( is_singular() ) {
		while ( have_posts() ) { the_post(); get_template_part( 'template-parts/content/elementor' ); }
	} elseif ( have_posts() ) {
		echo '<ul>';
		while ( have_posts() ) {
			the_post();
			echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
		}
		echo '</ul>';
		the_posts_pagination();
	} else {
		echo '<p>' . esc_html__( 'No results found.', 'radmanct' ) . '</p>';
	}
}
get_footer();
