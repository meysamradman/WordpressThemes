<?php

$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
$format = get_post_format();

if ( empty( $args ) ) {
	$args = array();
}

if ( class_exists( 'Liquid_Elementor_Addons' ) && defined( 'ELEMENTOR_VERSION' )){
	$show_read_more_button = $args['show_read_more_button'];
} else {
	$show_read_more_button = 'yes';
}

if( 'audio' === $format ) {
	liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args );
}
?>

<div class="lqd-lp-img w-sm-35 w-100 pos-rel overflow-hidden mb-4 mb-sm-0">
	<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
</div>

<div class="lqd-lp-contents w-sm-65 w-100 pl-sm-6 pl-md-9 pr-sm-5 d-flex flex-column">

	<header class="lqd-lp-header">
		<div class="lqd-lp-meta d-flex align-items-center justify-content-between text-uppercase ltr-sp-1 mb-3">
			<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat reset-ul inline-ul pos-rel z-index-3', $args ); ?>
		</div>
		<h2 class="lqd-lp-title mt-1 mb-3 h5"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	</header>

	<?php liquid_ajax_posts_entry_content( 'lqd-lp-excerpt', $args ); ?>

	<?php if( $show_read_more_button === 'yes' ) : ?>
	<footer class="lqd-lp-footer pt-3">
		<a href="<?php the_permalink(); ?>" class="btn btn-naked lqd-lp-read-more">
			<span class="btn-icon">
				<i class="lqd-icn-ess icon-md-arrow-forward"></i>
			</span>
		</a>
	</footer>
	<?php endif; ?>

</div>

<?php liquid_ajax_posts_overlay_link(); ?>
