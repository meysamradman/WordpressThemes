<?php defined( 'ABSPATH' ) || exit; ?>
<?php $rdct_preview = \RadmanCT\CardMedia\frame( (int) get_the_ID(), has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'alt' => '' ) ) : '' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-card-preview-owner rdct-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="rdct-post-card__image rdct-blog-media-frame" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo $rdct_preview['html']; \RadmanCT\BlogMedia\badge( get_the_ID() ); ?></a>
	<?php else : ?><div class="rdct-post-card__image rdct-image-placeholder" aria-hidden="true"></div><?php endif; ?>
	<div class="rdct-post-card__body">
		<?php get_template_part( 'template-parts/content/post-meta' ); ?>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="rdct-card-excerpt"><?php the_excerpt(); ?></div>
		<a class="rdct-card-more" href="<?php the_permalink(); ?>"><?php echo 'post' === get_post_type() ? esc_html__( 'Read article', 'radmanct' ) : esc_html__( 'View details', 'radmanct' ); ?></a>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
