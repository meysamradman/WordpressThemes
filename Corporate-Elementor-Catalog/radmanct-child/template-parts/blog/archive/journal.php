<?php defined( 'ABSPATH' ) || exit; ?>
<section class="rdct-blog-journal" aria-labelledby="rdct-blog-journal-title">
	<h2 id="rdct-blog-journal-title" class="rdct-blog-journal__title"><?php esc_html_e( 'Latest from the journal', 'radmanct-child' ); ?></h2>
	<div class="rdct-blog-journal__list">
		<?php for ( $rdct_index = 0; $rdct_index < $args['count'] && have_posts(); ++$rdct_index ) : the_post(); ?>
			<?php get_template_part( 'template-parts/blog/card' ); ?>
		<?php endfor; ?>
	</div>
</section>
