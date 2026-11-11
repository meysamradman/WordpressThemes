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

<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>

<header class="lqd-lp-header lqd-overlay d-flex flex-column justify-content-between p-4">

	<div class="lqd-lp-content-bg lqd-overlay"></div>
	<div class="lqd-lp-header-top">
		<div class="lqd-lp-meta text-uppercase ltr-sp-1 font-weight-bold pos-rel z-index-3">
			<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat-shaped lqd-lp-cat-solid reset-ul inline-ul font-weight-bold text-uppercase ltr-sp-1', $args ); ?>
		</div>
	</div>

	<div class="lqd-lp-header-bottom pos-rel">

		<time class="lqd-lp-date text-uppercase ltr-sp-1 font-weight-bold" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo liquid_helper()->liquid_post_date(); ?></time>
		
		<?php if ( liquid_helper()->get_option( 'blog-post-modified-date' ) === 'yes' && get_the_date() != get_the_modified_date() ){ ?>
			- <time class="lqd-lp-date text-uppercase ltr-sp-1 font-weight-bold" datetime="<?php echo get_the_modified_date( 'c' ); ?>"><?php echo get_the_modified_date(); ?></time>
		<?php } ?>

		<?php liquid_ajax_posts_entry_title( 'mt-2 mb-0 h5', $args ); ?>
		
	</div>

</header>

<?php liquid_ajax_posts_overlay_link(); ?>