<?php
defined( 'ABSPATH' ) || exit;
if ( \RadmanCT\Builder\render( 'footer' ) ) {
	return;
}
?>
<footer class="rdct-ftr">
	<div class="rdct-ctr rdct-ftr__inner">
		<p><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Footer menu', 'radmanct' ); ?>">
				<?php \RadmanCT\Navigation\menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'rdct-ftr-menu', 'fallback_cb' => false, 'depth' => 1, 'menu_id' => 'rdct-footer-menu-list' ) ); ?>
			</nav>
		<?php endif; ?>
	</div>
</footer>
