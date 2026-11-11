<?php
defined( 'ABSPATH' ) || exit;
$rdct_query = new WP_Query( array( 'post_type' => 'radman_product', 'post_status' => 'publish', 'posts_per_page' => $args['number'], 'no_found_rows' => true, 'has_password' => false, 'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ), 'post__not_in' => is_singular( 'radman_product' ) ? array( get_queried_object_id() ) : array() ) );
if ( ! $rdct_query->posts ) { return; }
?>
<ul class="rdct-catalog-recent">
	<?php foreach ( $rdct_query->posts as $rdct_entry ) : ?>
		<li><a href="<?php echo esc_url( get_permalink( $rdct_entry ) ); ?>">
			<?php echo get_the_post_thumbnail( $rdct_entry, 'thumbnail', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '88px' ) ); ?>
			<h4><?php echo esc_html( get_the_title( $rdct_entry ) ); ?></h4>
		</a></li>
	<?php endforeach; ?>
</ul>
