<?php defined( 'ABSPATH' ) || exit; ?>
<?php if ( have_posts() ) : ?>
	<div class="rdct-client-projects" data-rdct-archive-items>
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/portfolio/card' ); endwhile; ?>
	</div>
	<?php global $wp_query; $rdct_page = max( 1, (int) get_query_var( 'paged' ) ); ?>
	<nav class="rdct-client-projects-pagination" data-rdct-archive-pagination <?php if ( $rdct_page <= 1 && $rdct_page >= (int) $wp_query->max_num_pages ) { echo 'hidden'; } ?> aria-label="<?php esc_attr_e( 'Posts pagination', 'default' ); ?>">
		<?php if ( $rdct_page > 1 ) : ?><a data-rdct-archive-link href="<?php echo esc_url( get_pagenum_link( $rdct_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'radmanct' ); ?></a><?php endif; ?>
		<?php if ( $rdct_page < (int) $wp_query->max_num_pages ) : ?><a class="rdct-button" data-rdct-load-more href="<?php echo esc_url( get_pagenum_link( $rdct_page + 1 ) ); ?>"><?php esc_html_e( 'Load more', 'radmanct-child' ); ?></a><?php endif; ?>
	</nav>
<?php else : get_template_part( 'template-parts/content/none', null, array( 'portfolio' => true ) ); endif; ?>
