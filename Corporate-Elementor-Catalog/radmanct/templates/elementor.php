<?php
/* Template Name: Radman CT — Elementor with header and footer */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/elementor' );
endwhile;
get_footer();
