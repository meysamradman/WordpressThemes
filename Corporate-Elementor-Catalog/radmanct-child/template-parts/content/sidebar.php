<?php defined( 'ABSPATH' ) || exit; ?>
<?php
if ( in_array( $args['scope'] ?? '', array( 'catalog_archive', 'catalog_single' ), true ) ) {
	get_template_part( 'template-parts/catalog/sidebar', null, $args );
	return;
}
?>
<aside class="rdct-content-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'radmanct' ); ?>" <?php if ( str_starts_with( $args['scope'] ?? '', 'blog_' ) ) { echo 'data-rdct-blog-sidebar'; } ?>>
	<?php if ( str_starts_with( $args['scope'] ?? '', 'blog_' ) ) : ?><div class="rdct-blog-sidebar__content" data-rdct-sticky-sidebar data-rdct-natural-sidebar role="region" aria-label="<?php esc_attr_e( 'Sidebar widgets', 'radmanct-child' ); ?>"><?php endif; ?>
	<?php dynamic_sidebar( $args['area'] ); ?>
	<?php if ( str_starts_with( $args['scope'] ?? '', 'blog_' ) ) : ?></div><?php endif; ?>
</aside>
