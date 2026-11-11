<?php
/* Template Name: Custom client home */
defined( 'ABSPATH' ) || exit;
$render = static function (): void {
	while ( have_posts() ) : the_post();
		if ( post_password_required() ) { echo '<div class="rdct-ctr">' . get_the_password_form() . '</div>'; continue; }
		get_template_part( 'template-parts/client/home/hero' );
		get_template_part( 'template-parts/client/home/offerings' );
		if ( '' !== trim( get_the_content() ) ) { echo '<div class="rdct-ctr rdct-cnt rdct-clt-home-content">'; the_content(); \RadmanCT\Templates\content_pagination(); echo '</div>'; }
	endwhile;
};
$content = function_exists( 'rdct_prepare' ) ? rdct_prepare( $render ) : null;
get_header();
if ( null !== $content ) { echo $content; } else { $render(); }
get_footer();
