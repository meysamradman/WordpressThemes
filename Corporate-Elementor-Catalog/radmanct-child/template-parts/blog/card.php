<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/blog.php';
$rdct_categories = \RadmanCT\Blog\categories( get_the_ID() );
?>
<?php $rdct_preview = \RadmanCT\CardMedia\frame( (int) get_the_ID(), has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 767px) 100vw, 35vw' ) ) : '' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-card-preview-owner rdct-blog-journal-card' ); ?>>
	<?php if ( ! post_password_required() && has_post_thumbnail() ) : ?>
		<a class="rdct-post-card__image rdct-blog-journal-card__image rdct-blog-media-frame" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo $rdct_preview['html']; \RadmanCT\BlogMedia\badge( get_the_ID() ); ?></a>
	<?php endif; ?>
	<div class="rdct-blog-journal-card__body">
		<?php if ( $rdct_categories ) : ?><div class="rdct-post-categories"><?php echo $rdct_categories; ?></div><?php endif; ?>
		<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php get_template_part( 'template-parts/content/post-meta', null, array( 'categories' => false, 'author' => true ) ); ?>
		<?php if ( ! post_password_required() ) : ?><div class="rdct-blog-journal-card__excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
		<a class="rdct-button rdct-blog-journal-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article', 'radmanct' ); ?></a>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
