<?php

define( 'WP_ADMIN', true );
$_SERVER['HTTP_HOST'] = '127.0.0.1:8094';
$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=rdct-header';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/admin.php';

function library_expect( bool $value, string $message ): void {
	if ( ! $value ) { throw new RuntimeException( $message ); }
}

$users = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
library_expect( (bool) $users, 'No local administrator.' );
wp_set_current_user( (int) $users[0] );
require_once get_template_directory() . '/inc/admin/builder-page.php';
require_once get_template_directory() . '/inc/admin/builder/library.php';
\RadmanCT\Admin\BuilderPage\screens( array( 'rdct-settings_page_rdct-header', 'rdct-settings_page_rdct-footer' ) );
foreach ( array( 'header', 'footer' ) as $kind ) {
	$_GET = array( 'page' => 'rdct-' . $kind );
	$_REQUEST = $_GET;
	set_current_screen( 'rdct-settings_page_rdct-' . $kind );
	library_expect( ! \RadmanCT\Admin\BuilderPage\editing(), 'List route opened the editor.' );
	ob_start(); \RadmanCT\Admin\BuilderPage\render(); $markup = ob_get_clean();
	$table = new \RadmanCT\Admin\BuilderPage\Layout_List_Table( $kind );
	$actions = $table->column_title( array( 'id' => 'l0123456789abcdef0123456789abcdef', 'title' => 'Fixture' ) );
	library_expect( str_contains( $actions, 'button-link-delete submitdelete' ) && str_contains( $actions, 'data-rdct-delete=' ) && ! str_contains( $actions, 'admin-post.php' ) && str_contains( $actions, 'copy=' ), 'Native duplicate/inline-delete row actions missing.' );
	$original_actions = $table->column_title( array( 'id' => 'default', 'title' => 'Original' ) );
	library_expect( str_contains( $original_actions, 'data-rdct-delete="default"' ) && str_contains( $original_actions, 'rename=1' ), 'Original layout is missing delete or rename.' );
	library_expect( str_contains( $markup, 'wp-list-table' ) && str_contains( $markup, 'page-title-action' ) && str_contains( $markup, 'row-title' ) && ! str_contains( $markup, 'rdct-builder-form' ), 'Native list markup failed.' );
	\RadmanCT\Admin\BuilderPage\enqueue( 'rdct-settings_page_rdct-' . $kind );
	library_expect( wp_script_is( 'rdct-admin-builder-list', 'enqueued' ) && array() === wp_scripts()->registered['rdct-admin-builder-list']->deps && ! wp_script_is( 'rdct-admin-builder', 'enqueued' ) && ! wp_script_is( 'media-editor', 'enqueued' ) && ! wp_style_is( 'rdct-admin-builder-list', 'enqueued' ), 'List loaded custom editor, React or media dependencies.' );
	$_GET['new'] = '1';
	$before = \RadmanCT\Builder\Storage\listing( $kind );
	$original = \RadmanCT\Builder\read( $kind, 'default' );
	$_GET['copy'] = 'default';
	$draft = \RadmanCT\Admin\BuilderPage\draft_data();
	library_expect( $draft['layout'] === $original['layout'] && 0 === $draft['revision'], 'Duplicate did not load an independent draft.' );
	library_expect( $before === \RadmanCT\Builder\Storage\listing( $kind ) && $original === \RadmanCT\Builder\read( $kind, 'default' ), 'Duplicate GET changed persisted layouts.' );
	$_GET['copy'] = array( 'invalid' );
	library_expect( '' === \RadmanCT\Admin\BuilderPage\copy_source(), 'Malformed copy source accepted.' );
	unset( $_GET['copy'] );
	ob_start(); \RadmanCT\Admin\BuilderPage\render(); $form = ob_get_clean();
	library_expect( \RadmanCT\Admin\BuilderPage\creating() && \RadmanCT\Admin\BuilderPage\editing() && str_contains( $form, 'id="rdct-builder-root"' ) && ! str_contains( $form, 'value="rdct_create_layout"' ), 'Add New did not open the builder directly.' );
	unset( $_GET['new'] );
	$_GET['s'] = 'no-matching-layout-19e7'; $_REQUEST = $_GET;
	ob_start(); \RadmanCT\Admin\BuilderPage\render(); $empty = ob_get_clean();
	library_expect( str_contains( $empty, 'class="no-items"' ) && ! str_contains( $empty, 'class="row-title"' ), 'Native search did not filter layouts.' );
	unset( $_GET['s'] );
	$_GET['layout'] = 'default';
	library_expect( \RadmanCT\Admin\BuilderPage\editing() && 'default' === \RadmanCT\Admin\BuilderPage\selected_id(), 'Existing editor URL failed.' );
}
wp_set_current_user( 0 );
ob_start(); \RadmanCT\Admin\BuilderPage\render(); $markup = ob_get_clean();
library_expect( '' === $markup, 'Unauthorized user received the library.' );
echo "Passed: native header/footer lists/search, direct Add New builder routes, inline delete controls, isolated lightweight list script and capability gate. No stored content or settings changed.\n";
