<?php

$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
$format = get_post_format();

if ( empty( $args ) ) {
	$args = array();
}

if( 'audio' === $format ) {
	liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args );
}
?>
<header class="lqd-lp-header mb-5">

	<div class="lqd-lp-meta lqd-lp-meta-dot-between d-flex flex-wrap align-items-center font-weight-bold text-uppercase ltr-sp-1">

		<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat-shaped lqd-lp-cat-solid reset-ul inline-ul pos-rel z-index-3', $args ); ?>

		<?php liquid_ajax_posts_entry_time(); ?>

	</div>
	
	<?php liquid_ajax_posts_entry_title( 'h5 mt-4', $args ); ?>

</header>

<footer class="lqd-lp-footer mt-5">

	<div class="lqd-lp-meta">

		<div class="lqd-lp-author d-flex flex-wrap align-items-center pos-rel z-index-3">
			<a href="<?php echo esc_url( $author_url ); ?>" class="lqd-overlay"></a>
			<figure class="border-radius-circle overflow-hidden">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), '50', get_option( 'avatar_default', 'mystery' ), get_the_author(), array( 'class' => 'w-100' ) ); ?>
			</figure>
			<div class="lqd-lp-author-info ms-3 ml-3">
				<h3 class="mt-0 mb-0 font-weight-medium"><?php echo get_the_author(); ?></h3>
			</div>
		</div>

	</div>
</footer>

<?php liquid_ajax_posts_overlay_link(); ?>