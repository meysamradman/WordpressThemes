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

<div class="lqd-lp-img mb-4">
	<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
</div>

<header class="lqd-lp-header">
	<?php liquid_ajax_posts_entry_title( 'h3', $args ); ?>
</header>

<?php liquid_ajax_posts_entry_content( 'lqd-lp-excerpt', $args ); ?>

<footer class="lqd-lp-footer mt-3">	
	<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat lqd-lp-cat-shaped lqd-lp-cat-border reset-ul inline-ul pos-rel z-index-3 font-weight-bold text-uppercase ltr-sp-1', $args ); ?>
</footer>

<?php liquid_ajax_posts_overlay_link(); ?>

