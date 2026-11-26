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
<header class="lqd-lp-header">

	<div class="lqd-lp-meta lqd-lp-meta-dot-between lqd-lp-meta-solid border-radius-circle d-inline-flex align-items-center mb-1 font-weight-bold text-uppercase ltr-sp-1">
		
		<?php liquid_ajax_posts_entry_tags( 'lqd-lp-cat-plain reset-ul inline-ul pos-rel z-index-3', $args ); ?>

		<?php liquid_ajax_posts_entry_time(); ?>

	</div>
	<?php liquid_ajax_posts_entry_title( 'mt-3 mb-3 h5', $args ); ?>

</header>

<?php liquid_ajax_posts_entry_content( 'lqd-lp-excerpt', $args ); ?>

<?php liquid_ajax_posts_overlay_link(); ?>