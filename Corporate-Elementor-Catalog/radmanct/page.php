<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/' . ( \RadmanCT\Layout\elementor_content() ? 'elementor' : 'page' ) );
endwhile;
get_footer();
