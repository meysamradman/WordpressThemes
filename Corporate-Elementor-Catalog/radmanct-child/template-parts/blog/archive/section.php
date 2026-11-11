<?php defined( 'ABSPATH' ) || exit; ?>
<section class="rdct-blog-section" aria-labelledby="rdct-blog-section-title">
	<h2 id="rdct-blog-section-title" class="rdct-blog-section__title"><?php echo esc_html( $args['title'] ); ?></h2>
	<div class="rdct-blog-section__grid">
		<?php foreach ( $args['posts'] as $rdct_index => $rdct_entry ) : ?>
			<?php get_template_part( 'template-parts/blog/shared/post-card', null, array( 'variant' => 0 === $rdct_index ? 'feature' : 'tile', 'post_id' => $rdct_entry->ID ) ); ?>
		<?php endforeach; ?>
	</div>
</section>
