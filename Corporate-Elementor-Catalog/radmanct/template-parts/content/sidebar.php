<?php defined( 'ABSPATH' ) || exit; ?>
<aside class="rdct-content-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'radmanct' ); ?>">
	<?php dynamic_sidebar( $args['area'] ); ?>
</aside>
