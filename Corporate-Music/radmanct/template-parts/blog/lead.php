<?php
defined( 'ABSPATH' ) || exit;
$rdct_post_id = $args['post_id'] ?? get_the_ID();
$rdct_primary = ! empty( $args['primary'] );
$rdct_categories = get_the_category( $rdct_post_id );
$rdct_title_id = 'rdc-blog-lead-ttl-' . $rdct_post_id;
$rdct_image = post_password_required( $rdct_post_id ) ? '' : get_the_post_thumbnail( $rdct_post_id, $rdct_primary ? 'large' : 'medium_large', array(
	'alt' => '',
	'loading' => $rdct_primary ? 'eager' : 'lazy',
	'decoding' => 'async',
	'sizes' => $rdct_primary ? '(max-width: 767px) 100vw, 66vw' : '(max-width: 767px) 100vw, 33vw',
) + ( $rdct_primary ? array( 'fetchpriority' => 'high' ) : array() ) );
$rdct_preview = \RadmanCT\Media\frame( (int) $rdct_post_id, $rdct_image );
?>
<article id="rdc-lead-post-<?php echo (int) $rdct_post_id; ?>" <?php post_class( 'rdc-card-pre-own rdc-lead', $rdct_post_id ); ?>>
	<a class="rdc-blog-md-frm" href="<?php echo esc_url( get_permalink( $rdct_post_id ) ); ?>" aria-labelledby="<?php echo esc_attr( $rdct_title_id ); ?>">
		<?php echo $rdct_preview['html']; ?>
		<?php if ( $rdct_image ) { \RadmanCT\Media\badge( $rdct_post_id ); } ?>
		<header>
			<?php if ( $rdct_primary && $rdct_categories ) : ?><p class="rdc-lead__cat"><?php echo esc_html( $rdct_categories[0]->name ); ?></p><?php endif; ?>
			<h3 class="rdc-card-tx" id="<?php echo esc_attr( $rdct_title_id ); ?>"><?php echo esc_html( get_the_title( $rdct_post_id ) ); ?></h3>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $rdct_post_id ) ); ?>"><?php echo esc_html( get_the_date( '', $rdct_post_id ) ); ?></time>
		</header>
	</a>
	<?php echo $rdct_preview['control']; ?>
</article>
