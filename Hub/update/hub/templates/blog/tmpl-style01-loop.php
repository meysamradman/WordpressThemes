<?php

$format = get_post_format();

if ( empty( $args ) ) {
	$args = array();
}

if( 'audio' === $format ) {
	liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args );
}
?>
<header class="lqd-lp-header mb-6">
	<div class="lqd-lp-meta">
		<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat-shaped lqd-lp-cat-solid lqd-lp-cat-solid-colored reset-ul inline-ul pos-rel z-index-3 font-weight-bold text-uppercase ltr-sp-1', $args ); ?>
	</div>

	<?php liquid_ajax_posts_entry_title( 'mt-3 mb-3 h5', $args ); ?>

	<div class="lqd-lp-meta">
		<time class="lqd-lp-date d-inline-flex align-items-center" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo liquid_helper()->liquid_post_date(); ?></time>
		<?php if ( liquid_helper()->get_option( 'blog-post-modified-date' ) === 'yes' && get_the_date() != get_the_modified_date() ){ ?>
			- <time class="lqd-lp-date d-inline-flex align-items-center" datetime="<?php echo get_the_modified_date( 'c' ); ?>"><?php echo get_the_modified_date(); ?></time>
		<?php } ?>
	</div>
</header>
<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
<?php liquid_ajax_posts_overlay_link(); ?>
