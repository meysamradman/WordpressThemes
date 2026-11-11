<?php
defined( 'ABSPATH' ) || exit;
if ( 'post' !== get_post_type() ) { return; }
require_once get_template_directory() . '/inc/blog.php';
$rdct_categories = false === ( $args['categories'] ?? true ) ? '' : \RadmanCT\Blog\categories( get_the_ID() );
?>
<?php if ( $rdct_categories ) : ?><div class="rdct-post-categories"><?php echo $rdct_categories; ?></div><?php endif; ?>
<div class="rdct-post-meta">
	<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
	<?php if ( ! empty( $args['author'] ) ) : ?><span><?php echo esc_html( get_the_author() ); ?></span><?php endif; ?>
</div>
