<?php
if ( function_exists( 'rdct_archive_search' ) ) { rdct_archive_search( apply_filters( 'radmanct/search/options', array( 'post_type' => '', 'ajax' => false, 'layout' => 'inline', 'show_label' => false ) ) ); return; }
?>
<?php defined( 'ABSPATH' ) || exit; $rdct_search_id = wp_unique_id( 'rdct-search-' ); ?>
<form class="rdct-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $rdct_search_id ); ?>"><?php esc_html_e( 'Search', 'radmanct' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $rdct_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>" placeholder="<?php esc_attr_e( 'Search the site', 'radmanct' ); ?>">
	<button class="rdct-button" type="submit"><?php esc_html_e( 'Search', 'radmanct' ); ?></button>
</form>
