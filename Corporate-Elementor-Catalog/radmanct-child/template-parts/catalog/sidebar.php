<?php defined( 'ABSPATH' ) || exit; ?>
<aside class="rdct-content-sidebar rdct-client-catalog-sidebar" data-rdct-catalog-sidebar aria-label="<?php esc_attr_e( 'Sidebar', 'radmanct' ); ?>">
	<div class="rdct-catalog-sidebar__content" data-rdct-sticky-sidebar data-rdct-natural-sidebar role="region" aria-label="<?php esc_attr_e( 'Sidebar widgets', 'radmanct-child' ); ?>">
	<?php dynamic_sidebar( $args['area'] ); ?>
	</div>
</aside>
