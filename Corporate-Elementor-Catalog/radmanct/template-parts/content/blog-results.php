<?php defined( 'ABSPATH' ) || exit; ?>
<?php global $wp_query; $rdct_has_posts = $wp_query->post_count > 0; ?>
<?php if ( 'blog' === \RadmanCT\Archives\scope() ) : ?>
	<?php $rdct_show_controls = apply_filters( 'radmanct/blog/show_controls', true ); if ( $rdct_show_controls ) { \RadmanCT\BlogArchive\filters(); } global $wp_the_query; ?>
	<p data-rdct-result-count <?php if ( ! $rdct_show_controls ) { echo 'class="screen-reader-text"'; } ?>><?php echo sprintf( esc_html__( 'Results: %s', 'radmanct' ), esc_html( number_format_i18n( max( 0, (int) $wp_the_query->found_posts ) ) ) ); ?></p>
<?php endif; ?>
<?php if ( $rdct_has_posts ) : ?>
	<?php do_action( 'radmanct/blog/before_grid' ); ?>
	<?php if ( have_posts() ) : ?>
	<div class="rdct-post-grid">
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/blog/card' ); endwhile; ?>
	</div>
	<?php endif; ?>
	<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'radmanct' ), 'next_text' => __( 'Next', 'radmanct' ) ) ); ?>
<?php else : get_template_part( 'template-parts/content/none', null, array( 'blog' => 'blog' === \RadmanCT\Archives\scope() ) ); endif; ?>
