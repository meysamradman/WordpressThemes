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

<div class="lqd-lp-img pos-rel mb-3 overflow-hidden">
	<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
</div>

<div class="lqd-lp-meta mb-3">
	<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat lqd-lp-cat-shaped lqd-lp-cat-solid reset-ul inline-nav inline-ul pos-rel z-index-3 font-weight-bold text-uppercase ltr-sp-1', $args ); ?>
</div>
<header class="lqd-lp-header mb-2">
	<?php liquid_ajax_posts_entry_title( 'm-0 h5', $args ); ?>
</header>

<?php liquid_ajax_posts_entry_content( 'lqd-lp-excerpt mb-3', $args ); ?>

<?php liquid_ajax_posts_overlay_link(); ?>

