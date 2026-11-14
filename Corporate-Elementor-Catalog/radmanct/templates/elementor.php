<?php
/* Template Name: Radman CT — Elementor with header and footer */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! \RadmanCT\Renderers\render() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/elementor' );
	}
}
get_footer();
