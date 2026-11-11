<?php

namespace RadmanCT\Admin\BuilderPage;

defined( 'ABSPATH' ) || exit;

function library_parameter( string $key ): string {
	$value = $_GET[ $key ] ?? '';
	return is_string( $value ) && strlen( $value ) <= 320 ? sanitize_text_field( wp_unslash( $value ) ) : '';
}

function render_library(): void {
	$kind = kind();
	$url = admin_url( 'admin.php?page=rdct-' . $kind );
	$add = 'header' === $kind ? __( 'Add New Header', 'radmanct' ) : __( 'Add New Footer', 'radmanct' );
	require_once __DIR__ . '/list-table.php';
	$table = new Layout_List_Table( $kind );
	$table->prepare_items();
	echo '<div class="wrap" id="rdct-layout-library"><h1 class="wp-heading-inline" tabindex="-1">' . esc_html( labels()[ $kind ] ) . '</h1> <a class="page-title-action" href="' . esc_url( add_query_arg( 'new', 1, $url ) ) . '">' . esc_html( $add ) . '</a><hr class="wp-header-end"><div data-rdct-list-notice role="status" aria-live="polite"></div>';
	$table->views();
	echo '<form method="get"><input type="hidden" name="page" value="rdct-' . esc_attr( $kind ) . '">';
	$status = library_parameter( 'status' );
	if ( in_array( $status, array( 'enabled', 'disabled' ), true ) ) { echo '<input type="hidden" name="status" value="' . esc_attr( $status ) . '">'; }
	$table->search_box( __( 'Search layouts', 'radmanct' ), 'rdct-layout-search' );
	$table->display();
	echo '</form><p><a href="' . esc_url( admin_url( 'admin.php?page=rdct-settings#rdct-header_footer' ) ) . '">' . esc_html__( 'Header and footer settings', 'radmanct' ) . '</a></p></div>';
}
