<?php
if ( empty( $args ) ) {
	$args = array();
}

$format = get_post_format();
if ( 'audio' === $format ) {
	liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args );
}
?>
<?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
<header class="lqd-lp-header">
	<div class="lqd-lp-meta lqd-lp-meta-dot-between d-flex flex-wrap align-items-center font-weight-bold text-uppercase ltr-sp-1">
		<?php liquid_ajax_posts_entry_tags( 'reset-ul inline-ul pos-rel z-index-3', $args ); ?>
		<?php liquid_ajax_posts_entry_time(); ?>
	</div>
	<?php liquid_ajax_posts_entry_title( 'h5 mt-3 mb-0', $args ); ?>
</header>
<?php liquid_ajax_posts_overlay_link(); ?>
