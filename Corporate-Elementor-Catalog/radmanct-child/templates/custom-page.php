<?php
/* Template Name: Custom client page */
defined( 'ABSPATH' ) || exit;
$render = static function (): void {
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/client/page' );
	endwhile;
};
$content = function_exists( 'rdct_prepare' ) ? rdct_prepare( $render ) : null;
get_header();
if ( null !== $content ) { echo $content; } else { $render(); }
get_footer();
