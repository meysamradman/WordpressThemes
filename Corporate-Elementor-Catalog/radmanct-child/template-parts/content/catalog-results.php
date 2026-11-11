<?php defined( 'ABSPATH' ) || exit; ?>
<?php
if ( \RadmanCT\Sidebars\current() ) { get_template_part( 'template-parts/catalog/archive/results-status' ); }
else { \RadmanCT\Content\filters(); }
?>
<?php if ( have_posts() ) : ?>
	<div class="rdct-cnt-grid">
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/catalog/card' ); endwhile; ?>
	</div>
	<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'radmanct' ), 'next_text' => __( 'Next', 'radmanct' ) ) ); ?>
<?php else : get_template_part( 'template-parts/content/none', null, array( 'catalog' => true ) ); endif; ?>
