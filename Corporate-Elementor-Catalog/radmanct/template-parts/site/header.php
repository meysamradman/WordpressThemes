<?php
defined( 'ABSPATH' ) || exit;
if ( \RadmanCT\Builder\render( 'header' ) ) {
	return;
}
?>
<header class="rdct-hdr">
	<div class="rdct-ctr rdct-hdr__inner" data-rdct-menu data-submenu-label="<?php esc_attr_e( 'Toggle submenu', 'radmanct' ); ?>">
		<?php \RadmanCT\Templates\brand(); ?>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<button class="rdct-menu-toggle" type="button" aria-expanded="false" aria-controls="rdct-primary-menu" hidden><?php esc_html_e( 'Menu', 'radmanct' ); ?></button>
			<nav id="rdct-primary-menu" class="rdct-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'radmanct' ); ?>">
				<?php \RadmanCT\Navigation\menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'rdct-menu', 'fallback_cb' => false, 'depth' => 0, 'menu_id' => 'rdct-primary-menu-list' ) ); ?>
			</nav>
		<?php endif; ?>
	</div>
</header>
