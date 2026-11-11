<?php
/* Template Name: Full width */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	$rdct_external_content = \RadmanCT\Layout\elementor_content();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $rdct_external_content ? 'rdct-full-width' : 'rdct-full-width rdct-ctr' ); ?>>
		<?php if ( $rdct_external_content ) : ?>
			<?php the_content(); ?>
		<?php else : ?>
			<div class="rdct-cnt"><?php the_content(); ?></div>
			<?php \RadmanCT\Templates\content_pagination(); ?>
		<?php endif; ?>
	</article>
	<?php
endwhile;
get_footer();
