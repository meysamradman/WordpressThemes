<?php
defined( 'ABSPATH' ) || exit;

$rdct_categories = \RadmanCT\Blog\categories( get_the_ID() );
?>
<?php $rdct_preview = \RadmanCT\Media\frame( (int) get_the_ID(), has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 767px) 100vw, 35vw' ) ) : '' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdc-card-pre-own rdc-jrn-card' ); ?>>
	<?php if ( $rdct_preview['html'] ) : ?>
		<a data-rdc-cur-lbl="<?php esc_attr_e('Read article','radmanct'); ?>" class="rdc-post-card__im rdc-jrn-card__im rdc-blog-md-frm" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo $rdct_preview['html']; \RadmanCT\Media\badge( get_the_ID() ); ?></a>
	<?php endif; ?>
	<div class="rdc-jrn-card__body">
		<?php if ( $rdct_categories ) : ?><div class="rdc-post-cats"><?php echo $rdct_categories; ?></div><?php endif; ?>
		<h3 class="rdc-card-tx"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php get_template_part( 'shared/radmanct/template-parts/blog/meta', null, array( 'categories' => false, 'author' => true ) ); ?>
		<div class="rdc-jrn-card__exc"><p class="rdc-card-tx"><?php if(!post_password_required())echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p></div>
		<?php echo \RadmanCT\button(__('Read article','radmanct'),get_permalink(),array('class'=>'rdc-jrn-card__more')); ?>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
