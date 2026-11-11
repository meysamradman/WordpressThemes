<?php defined( 'ABSPATH' ) || exit; ?>
<?php while ( have_posts() ) : the_post(); ?>
	<?php if ( post_password_required() ) { echo '<div class="rdct-ctr rdct-single">' . get_the_password_form() . '</div>'; continue; } ?>
	<?php require_once get_template_directory() . '/inc/blog.php'; $rdct_blog_url = \RadmanCT\Blog\archive_url(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-single rdct-ctr' ); ?>>
		<?php if ( $rdct_blog_url ) : ?><a class="rdct-single__back" href="<?php echo esc_url( $rdct_blog_url ); ?>"><?php esc_html_e( 'Back to articles', 'radmanct' ); ?></a><?php endif; ?>
		<header class="rdct-single__header">
			<?php get_template_part( 'template-parts/content/post-meta', null, array( 'author' => true ) ); ?>
			<?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php the_title(); ?></h1><?php endif; ?>
			<?php if ( has_excerpt() ) : ?><div class="rdct-single__intro"><?php the_excerpt(); ?></div><?php endif; ?>
		</header>
		<?php if ( has_post_thumbnail() ) : ?><div class="rdct-single__image rdct-blog-media-frame"><?php the_post_thumbnail( 'large' ); \RadmanCT\BlogMedia\badge( get_the_ID() ); ?></div><?php endif; ?>
		<div class="rdct-cnt"><?php the_content(); ?></div>
		<?php wp_link_pages( array( 'before' => '<nav class="rdct-pg" aria-label="' . esc_attr__( 'Content pages', 'radmanct' ) . '">', 'after' => '</nav>' ) ); ?>
	</article>
<?php endwhile; ?>
