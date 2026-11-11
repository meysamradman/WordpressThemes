<?php
defined( 'ABSPATH' ) || exit;
$rdct_post_id = (int) $args['post_id'];
if ( post_password_required( $rdct_post_id ) ) { return; }
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/icons.php';
$rdct_image = get_the_post_thumbnail( $rdct_post_id, 'large', array( 'class' => 'rdct-article-cover__image', 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '(max-width: 1024px) 100vw, 360px' ) );
$rdct_categories = \RadmanCT\Blog\categories( $rdct_post_id );
$rdct_media = \RadmanCT\BlogMedia\presentation( $rdct_post_id );
?>
<div class="rdct-article-cover">
	<?php if ( $rdct_image ) : ?>
		<figure class="rdct-article-cover__media rdct-blog-media-frame">
			<?php echo $rdct_image; ?>
			<time class="rdct-article-cover__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $rdct_post_id ) ); ?>">
				<span><?php echo esc_html( get_the_date( 'd', $rdct_post_id ) ); ?></span>
				<span><?php echo esc_html( get_the_date( 'M', $rdct_post_id ) ); ?></span>
				<span><?php echo esc_html( get_the_date( 'Y', $rdct_post_id ) ); ?></span>
			</time>
			<?php if ( $rdct_media['type'] ) { \RadmanCT\BlogMedia\badge( $rdct_post_id ); } else { ?><span class="rdct-article-cover__badge" aria-hidden="true"><?php echo \RadmanCT\Icons\render( 'file-text' ); ?></span><?php } ?>
		</figure>
	<?php endif; ?>
	<div class="rdct-article-cover__details">
		<span class="rdct-article-cover__type-icon" aria-hidden="true"><?php echo \RadmanCT\Icons\render( $rdct_media['icon'] ); ?></span>
		<div>
			<h3 class="rdct-article-cover__title"><?php echo esc_html( $rdct_media['type'] ? $rdct_media['label'] : __( 'Articles', 'radmanct-child' ) ); ?></h3>
			<?php if ( $rdct_categories ) : ?><p class="rdct-article-cover__categories"><?php echo $rdct_categories; ?></p><?php endif; ?>
		</div>
	</div>
</div>
