<?php defined( 'ABSPATH' ) || exit; $rdct_product = 'radman_product' === get_post_type(); ?>
<?php $rdct_preview = \RadmanCT\CardMedia\frame( (int) get_the_ID(), has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'alt' => '' ) ) : '' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-card-preview-owner rdct-cnt-card rdct-client-catalog-card' . ( $rdct_product ? ' rdct-cnt-card--product' : '' ) ); ?>>
	<?php if ( has_post_thumbnail() ) : ?><a class="rdct-cnt-card__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php echo $rdct_preview['html']; ?></a>
	<?php else : ?><div class="rdct-cnt-card__image rdct-image-placeholder" aria-hidden="true"></div><?php endif; ?>
	<div class="rdct-cnt-card__body">
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php require_once get_template_directory() . '/inc/integrations/content-meta.php'; echo \RadmanCT\Content\Meta\html( get_the_ID(), true ); ?>
		<div class="rdct-card-excerpt"><?php the_excerpt(); ?></div>
		<a class="rdct-card-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View details', 'radmanct' ); ?></a>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
