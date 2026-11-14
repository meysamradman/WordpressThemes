<?php
defined( 'ABSPATH' ) || exit;
$rdct_variant = in_array( $args['variant'] ?? '', array( 'feature', 'tile', 'compact' ), true ) ? $args['variant'] : 'tile';
$rdct_compact = 'compact' === $rdct_variant;
$rdct_post_id = $args['post_id'] ?? get_the_ID();
$rdct_categories = $rdct_compact ? array() : get_the_category( $rdct_post_id );
$rdct_image = post_password_required( $rdct_post_id ) ? '' : get_the_post_thumbnail( $rdct_post_id, $rdct_compact ? 'medium' : 'medium_large', array(
	'alt' => '', 'loading' => 'lazy', 'decoding' => 'async',
	'sizes' => $rdct_compact ? '108px' : ( 'feature' === $rdct_variant ? '(max-width: 767px) 100vw, 50vw' : '(max-width: 767px) 100vw, 25vw' ),
) );
$rdct_title = get_the_title( $rdct_post_id );
$rdct_preview = \RadmanCT\Media\frame( (int) $rdct_post_id, $rdct_image );
?>
<article <?php if ( ! $rdct_compact ) { echo 'id="rdc-' . esc_attr( $rdct_variant ) . '-post-' . (int) $rdct_post_id . '" '; } ?><?php post_class( 'rdc-card-pre-own rdc-blog-pv rdc-blog-pv--' . $rdct_variant, $rdct_post_id ); ?>>
	<a data-rdc-cur-lbl="<?php esc_attr_e('Read article','radmanct'); ?>" href="<?php echo esc_url( get_permalink( $rdct_post_id ) ); ?>">
		<?php if ( $rdct_preview['html'] ) : ?>
			<div class="rdc-blog-pv__md rdc-blog-md-frm">
				<?php echo $rdct_preview['html']; ?>
				<?php \RadmanCT\Media\badge( $rdct_post_id ); ?>
			</div>
		<?php endif; ?>
		<header class="rdc-blog-pv__body">
			<?php if ( $rdct_categories ) : ?><p class="rdc-blog-pv__cat"><?php echo esc_html( $rdct_categories[0]->name ); ?></p><?php endif; ?>
			<h3 class="rdc-card-tx"><?php echo esc_html( $rdct_title ?: __( '(no title)' ) ); ?></h3>
			<?php if ( ! $rdct_compact ) : ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $rdct_post_id ) ); ?>"><?php echo esc_html( get_the_date( '', $rdct_post_id ) ); ?></time><?php endif; ?>
		</header>
	</a>
	<?php echo $rdct_preview['control']; ?>
</article>
