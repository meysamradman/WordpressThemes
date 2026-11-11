<?php defined( 'ABSPATH' ) || exit; ?>
<?php $rdct_scope = ! empty( $args['catalog'] ) ? 'catalog' : 'portfolio'; $rdct_ajax = $rdct_scope === \RadmanCT\Archives\scope(); ?>
<section class="rdct-ctr rdct-cnt-listing" <?php if ( $rdct_ajax ) { echo \RadmanCT\Archives\attributes( $rdct_scope ); } ?>>
	<?php get_template_part( 'template-parts/content/archive-header', null, array( 'scope' => $rdct_scope ) ); ?>
	<?php if ( $rdct_ajax ) : \RadmanCT\Archives\status(); ?>
		<div data-rdct-archive-view tabindex="-1"><?php \RadmanCT\Archives\view( $rdct_scope ); ?></div>
	<?php else : ?>
	<?php if ( ! empty( $args['catalog'] ) ) { \RadmanCT\Content\filters(); } ?>
	<?php if ( have_posts() ) : ?>
		<div class="rdct-cnt-grid">
			<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/' . ( ! empty( $args['catalog'] ) ? 'catalog' : 'portfolio' ) . '/card' ); endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'radmanct' ), 'next_text' => __( 'Next', 'radmanct' ) ) ); ?>
	<?php else : get_template_part( 'template-parts/content/none', null, array( 'catalog' => ! empty( $args['catalog'] ), 'portfolio' => empty( $args['catalog'] ) ) ); endif; ?>
	<?php endif; ?>
</section>
