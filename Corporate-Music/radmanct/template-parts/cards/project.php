<?php
defined( 'ABSPATH' ) || exit;
$rdct_id = (int) get_the_ID();
$rdct_allowed = ! post_password_required( $rdct_id );
$rdct_preview = \RadmanCT\Media\frame( $rdct_id, has_post_thumbnail() ? get_the_post_thumbnail( $rdct_id, 'large', array( 'alt' => '', 'sizes' => '(min-width: 960px) 50vw, 100vw' ) ) : '' );
$rdct_terms = $rdct_allowed ? get_the_terms( $rdct_id, 'project_category' ) : array();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdc-card-pre-own rdc-prj' ); ?>>
	<a data-rdc-cur-lbl="<?php esc_attr_e('View details','radmanct'); ?>" class="rdc-prj__md" <?php if ( has_post_thumbnail() ) { echo 'data-rdc-prj-mot'; } ?> href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( $rdct_preview['html'] ) : ?><span class="rdc-prj__px"><span class="rdc-prj__zoom"><?php echo $rdct_preview['html']; ?></span></span><?php else : ?><span class="rdc-im-ph" aria-hidden="true"></span><?php endif; ?>
	</a>
	<div class="rdc-prj__body">
		<?php if ( is_array( $rdct_terms ) && $rdct_terms ) : ?>
			<div class="rdc-prj__cats"><?php foreach ( array_slice( $rdct_terms, 0, 3 ) as $rdct_term ) : $rdct_url = get_term_link( $rdct_term ); ?>
				<?php if ( ! is_wp_error( $rdct_url ) ) : ?><a href="<?php echo esc_url( $rdct_url ); ?>"><?php echo esc_html( $rdct_term->name ); ?></a><?php endif; ?>
			<?php endforeach; ?></div>
		<?php endif; ?>
		<h3 class="rdc-card-tx"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="rdc-prj__exc"><p class="rdc-card-tx"><?php if($rdct_allowed)echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p></div>
		<a class="rdc-prj__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View details', 'radmanct' ); ?><?php echo \RadmanCT\icon( 'arrow-up-right' ); ?></a>
	</div>
	<?php echo $rdct_preview['control']; ?>
</article>
