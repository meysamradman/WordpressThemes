<?php
defined( 'ABSPATH' ) || exit;
$rdct_id = (int) get_the_ID();
$rdct_allowed = ! post_password_required( $rdct_id );
$rdct_preview = \RadmanCT\CardMedia\frame( $rdct_id, has_post_thumbnail() ? get_the_post_thumbnail( $rdct_id, 'large', array( 'alt' => '', 'sizes' => '(min-width: 960px) 50vw, 100vw' ) ) : '' );
$rdct_terms = $rdct_allowed ? get_the_terms( $rdct_id, 'radman_portfolio_cat' ) : array();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-card-preview-owner rdct-client-project' ); ?>>
	<a class="rdct-client-project__media" <?php if ( has_post_thumbnail() ) { echo 'data-rdct-project-motion'; } ?> href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( has_post_thumbnail() ) : ?><span class="rdct-client-project__parallax"><span class="rdct-client-project__zoom"><?php echo $rdct_preview['html']; ?></span></span><?php else : ?><span class="rdct-image-placeholder" aria-hidden="true"></span><?php endif; ?>
	</a>
	<div class="rdct-client-project__body">
		<?php if ( is_array( $rdct_terms ) && $rdct_terms ) : ?>
			<div class="rdct-client-project__categories"><?php foreach ( array_slice( $rdct_terms, 0, 3 ) as $rdct_term ) : $rdct_url = get_term_link( $rdct_term ); ?>
				<?php if ( ! is_wp_error( $rdct_url ) ) : ?><a href="<?php echo esc_url( $rdct_url ); ?>"><?php echo esc_html( $rdct_term->name ); ?></a><?php endif; ?>
			<?php endforeach; ?></div>
		<?php endif; ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $rdct_allowed ) : ?><div class="rdct-client-project__excerpt"><?php the_excerpt(); ?></div><?php endif; ?>
		<a class="rdct-client-project__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View details', 'radmanct' ); ?><?php echo \RadmanCT\Icons\render( 'arrow-up-right' ); ?></a>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
