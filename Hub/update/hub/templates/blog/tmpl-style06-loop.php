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

<div class="lqd-lp-img overflow-hidden">

	<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>

	<div class="lqd-lp-meta text-uppercase font-weight-bold pos-rel z-index-3">
		<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat-shaped lqd-lp-cat-solid reset-ul inline-ul font-weight-bold text-uppercase ltr-sp-1', $args ); ?>
	</div>

</div>

<header class="lqd-lp-header p-5">

	<div class="lqd-lp-meta lqd-lp-meta-dot-between d-flex flex-wrap align-items-center text-uppercase ltr-sp-1 font-weight-bold">

		<div class="lqd-lp-author pos-rel z-index-3">
			<div class="lqd-lp-author-info">
				<h3 class="mt-0 mb-0 h5"><a href="<?php echo esc_url( $author_url ); ?>"><?php echo get_the_author(); ?></a></h3>
			</div>
		</div>

		<?php liquid_ajax_posts_entry_time(); ?>

	</div>

	<?php liquid_ajax_posts_entry_title( 'mt-3 mb-0 h5', $args ); ?>

</header>

<?php liquid_ajax_posts_overlay_link(); ?>
