<?php
defined( 'ABSPATH' ) || exit;
$rdct_action = (string) get_post_type_archive_link( 'radman_product' );
$rdct_values = \RadmanCTChild\Catalog\Archive\values();
require_once get_template_directory() . '/inc/integrations/catalog-filters.php';
require_once get_template_directory() . '/inc/ui/fields.php';
?>
<form class="rdct-catalog-filter rdct-form" role="search" method="get" action="<?php echo esc_url( $rdct_action ); ?>" data-rdct-archive-form data-rdct-catalog-search>
	<?php foreach ( \RadmanCT\Content\CatalogFilters\routing( $rdct_action ) as $rdct_key => $rdct_value ) : ?>
		<input type="hidden" name="<?php echo esc_attr( $rdct_key ); ?>" value="<?php echo esc_attr( $rdct_value ); ?>">
	<?php endforeach; ?>
	<input type="hidden" name="rm_category" value="<?php echo esc_attr( $rdct_values['rm_category'] ); ?>">
	<input type="hidden" name="rm_tag" value="<?php echo esc_attr( $rdct_values['rm_tag'] ); ?>">
	<?php
	\RadmanCT\UI\input_field( 'rm_search', __( 'Search products', 'radmanct' ), $rdct_values['rm_search'], array( 'type' => 'search', 'maxlength' => 80 ) );
	foreach ( array( 'rm_brand' => __( 'Brand', 'radmanct' ), 'rm_feature' => __( 'Feature', 'radmanct' ) ) as $rdct_key => $rdct_label ) {
		$rdct_options = array( array( '', __( 'All', 'radmanct' ) ) );
		foreach ( \RadmanCT\ArchiveFilters\terms( \RadmanCTContent\Catalog\FILTERS[ $rdct_key ], $rdct_values[ $rdct_key ] ) as $rdct_term ) {
			$rdct_options[] = array( $rdct_term->slug, $rdct_term->name );
		}
		\RadmanCT\UI\select_field( $rdct_key, $rdct_label, $rdct_options, $rdct_values[ $rdct_key ], array( 'searchable' => 'auto' ) );
	}
	$rdct_sorts = \RadmanCT\ArchiveFilters\sorts();
	\RadmanCT\UI\select_field( 'rm_sort', __( 'Sort by', 'radmanct' ), array_map( static fn( $key, $label ): array => array( $key, $label ), array_keys( $rdct_sorts ), $rdct_sorts ), $rdct_values['rm_sort'] );
	?>
	<a data-rdct-archive-link href="<?php echo esc_url( $rdct_action ); ?>"><?php esc_html_e( 'Clear filters', 'radmanct' ); ?></a>
</form>
