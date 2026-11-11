<?php defined( 'ABSPATH' ) || exit; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-clt-page rdct-ctr' ); ?>>
	<div class="rdct-clt-page__frame">
		<header class="rdct-clt-page__heading" <?php if ( \RadmanCT\PageTitle\active() ) { echo 'hidden'; } ?>>
			<?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php the_title(); ?></h1><?php endif; ?>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="rdct-clt-page__image"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>
	</div>
	<div class="rdct-cnt"><?php the_content(); ?></div>
	<?php wp_link_pages( array( 'before' => '<nav class="rdct-pg" aria-label="' . esc_attr__( 'Content pages', 'radmanct' ) . '">', 'after' => '</nav>' ) ); ?>
</article>
