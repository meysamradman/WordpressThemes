<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/archive-filters.php';
$rdct_values = \RadmanCTChild\Catalog\Archive\values();
$rdct_active = array();
$rdct_labels = array( 'rm_search' => __( 'Search products', 'radmanct' ), 'rm_category' => __( 'Category', 'radmanct' ), 'rm_brand' => __( 'Brand', 'radmanct' ), 'rm_feature' => __( 'Feature', 'radmanct' ), 'rm_tag' => __( 'Tags', 'default' ), 'rm_sort' => __( 'Sort by', 'radmanct' ) );
foreach ( $rdct_labels as $rdct_key => $rdct_label ) {
	if ( '' === $rdct_values[ $rdct_key ] ) { continue; }
	$rdct_value = $rdct_values[ $rdct_key ];
	if ( isset( \RadmanCTContent\Catalog\FILTERS[ $rdct_key ] ) ) {
		$rdct_term = get_term_by( 'slug', $rdct_value, \RadmanCTContent\Catalog\FILTERS[ $rdct_key ] );
		if ( $rdct_term ) { $rdct_value = $rdct_term->name; }
	} elseif ( 'rm_sort' === $rdct_key ) { $rdct_value = \RadmanCT\ArchiveFilters\sorts()[ $rdct_value ] ?? $rdct_value; }
	$rdct_active[ $rdct_key ] = $rdct_label . ': ' . $rdct_value;
}
global $wp_query;
?>
<p class="rdct-cat-results" data-rdct-result-count><?php printf( esc_html__( 'Results: %s', 'radmanct' ), esc_html( number_format_i18n( max( 0, (int) $wp_query->found_posts ) ) ) ); ?></p>
<?php \RadmanCT\ArchiveFilters\active( $rdct_active, $rdct_values, (string) get_post_type_archive_link( 'radman_product' ) ); ?>
