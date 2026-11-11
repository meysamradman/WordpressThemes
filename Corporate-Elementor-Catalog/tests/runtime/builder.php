<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
define( 'DOING_AJAX', true );
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
require_once '/workspace/radmanct/inc/admin/builder-api.php';
require_once '/workspace/radmanct/inc/admin/builder-assignment.php';
require_once '/workspace/radmanct/inc/admin/builder-preview.php';
wp_set_current_user( 1 );
function expect( bool $value, string $message ): void { if ( ! $value ) { throw new RuntimeException( $message ); } }
add_filter( 'wp_die_ajax_handler', static function () { return static function () { throw new RuntimeException( 'qa-json-end' ); }; } );
function api( string $operation, string $id, array $fields = array() ): array {
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_POST = array( 'kind' => 'header', 'id' => $id, 'operation' => $operation, 'nonce' => wp_create_nonce( 'radman_builder' ) );
	foreach ( $fields as $key => $value ) {
		$_POST[ $key ] = is_array( $value ) ? wp_slash( wp_json_encode( $value ) ) : $value;
	}
	$_REQUEST = $_POST;
	ob_start();
	try { \RadmanCT\Admin\BuilderApi\request(); } catch ( RuntimeException $error ) {
		if ( 'qa-json-end' !== $error->getMessage() ) { ob_end_clean(); throw $error; }
	}
	$result = json_decode( ob_get_clean(), true );
	expect( is_array( $result ), 'API did not return valid JSON.' );
	return $result;
}

$raw = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'qaRow', 'items' => array( array( 'id' => 'qaText', 'type' => 'text', 'text' => 'Draft header text' ), array( 'id' => 'qaCopyright', 'type' => 'copyright' ), array( 'id' => 'qaSearch', 'type' => 'search' ) ) ) ) );
$raw['sticky'] = 'desktop';
$first = api( 'save', 'default', array( 'layout' => $raw, 'revision' => '0' ) );
expect( $first['success'] && 1 === $first['data']['data']['revision'], 'Initial AJAX save failed.' );
expect( 'desktop' === $first['data']['data']['layout']['sticky'], 'Sticky header setting did not survive AJAX save.' );
expect( ! api( 'read', 'default', array( 'nonce' => 'invalid' ) )['success'], 'Invalid AJAX nonce was accepted.' );
expect( ! api( 'create', 'default', array( 'layout' => $raw, 'title' => '<b></b>' ) )['success'], 'Empty sanitized title was accepted.' );
$stale = api( 'save', 'default', array( 'layout' => $raw, 'revision' => '0' ) );
expect( ! $stale['success'] && 1 === \RadmanCT\Builder\read( 'header' )['revision'], 'Stale save overwrote the layout.' );
$old = get_option( 'radman_builder_header' );
$competing = $old;
$competing['revision'] = 2;
update_option( 'radman_builder_header', $competing, false );
expect( ! \RadmanCT\Builder\Storage\compare_exchange( 'radman_builder_header', $old, $old + array( 'extra' => true ) ), 'SQL compare-and-swap ignored a competing revision.' );
$invalid = api( 'save', 'default', array( 'layout' => array( 'version' => 999 ), 'revision' => '2' ) );
expect( ! $invalid['success'] && 2 === \RadmanCT\Builder\read( 'header' )['revision'], 'Invalid data overwrote the layout.' );
$strings = array();
add_action( 'wpml_register_single_string', static function ( $context, $name ) use ( &$strings ) { $strings[] = $name; }, 10, 2 );
$created = api( 'create', 'default', array( 'layout' => $raw, 'title' => 'Client header' ) );
expect( $created['success'], 'Named layout creation failed.' );
$id = $created['data']['id'];
expect( in_array( 'header/' . $id . '/qaText/text', $strings, true ), 'New layout did not register namespaced translation strings.' );
expect( api( 'rename', $id, array( 'title' => 'Renamed header' ) )['success'], 'Rename failed.' );
expect( api( 'default', $id, array( 'language' => 'all' ) )['success'], 'Site default assignment failed.' );
expect( $id === \RadmanCT\Builder\selection( 'header' ), 'Site assignment was not used by renderer.' );
expect( api( 'default', 'default', array( 'language' => 'all' ) )['success'], 'Restoring original site default failed.' );
expect( api( 'default', $id, array( 'language' => 'fa' ) )['success'], 'Language assignment failed.' );
add_filter( 'wpml_current_language', static function () { return 'fa'; } );
expect( $id === \RadmanCT\Builder\selection( 'header' ), 'Language selection did not override site default.' );
expect( api( 'inherit', $id, array( 'language' => 'fa' ) )['success'], 'Clearing language assignment failed.' );
expect( 'default' === \RadmanCT\Builder\selection( 'header' ), 'Language inheritance did not restore site default.' );
$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Page assignment' ) );
$_POST = array( 'radman_layout_nonce' => wp_create_nonce( 'radman_page_layouts' ), 'radman_page_layout' => array( 'header' => $id, 'footer' => 'none' ) );
\RadmanCT\Admin\BuilderAssignment\save( $page );
expect( $id === get_post_meta( $page, '_radman_header_layout', true ), 'Page layout did not save.' );
$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page ) );
expect( $id === \RadmanCT\Builder\selection( 'header' ) && 'none' === \RadmanCT\Builder\selection( 'footer' ), 'Page selection did not override site defaults.' );
ob_start();
expect( \RadmanCT\Builder\render( 'footer' ), 'Hidden footer did not suppress fallback.' );
expect( '' === ob_get_clean(), 'Hidden footer rendered content.' );
$before_preview = get_option( \RadmanCT\Builder\Storage\key( 'header', $id ) );
expect( \RadmanCT\Builder\sticky_header(), 'Named/page-selected header lost its sticky setting.' );
update_option( 'radman_settings', array( 'smooth_scroll' => true ), false );
expect( ! \RadmanCT\PageEffects\values()['smooth_scroll'] && get_option( 'radman_settings' )['smooth_scroll'], 'Sticky header did not safely resolve the smooth-scroll conflict.' );
\RadmanCT\Builder\enqueue();
expect( wp_style_is( 'rdct-sticky-header', 'enqueued' ), 'Selected sticky header CSS was not enqueued.' );
$preview = api( 'preview', $id, array( 'layout' => $raw, 'direction' => 'rtl' ) );
expect( $preview['success'] && str_contains( $preview['data']['html'], 'dir="rtl"' ) && str_contains( $preview['data']['html'], 'Draft header text' ), 'PHP draft preview failed.' );
expect( str_contains( $preview['data']['html'], 'class="rdct-site"' ) && ! str_contains( $preview['data']['html'], '<script' ), 'Preview isolation or theme styling missing.' );
expect( $before_preview === get_option( \RadmanCT\Builder\Storage\key( 'header', $id ) ), 'Preview mutated saved layout.' );
expect( ! str_contains( $preview['data']['html'], 'rdct-bld--sticky-desktop' ), 'Sticky behavior leaked into inert editor preview.' );
ob_start();
\RadmanCT\Builder\menu( 'header', 'missing', 999999 );
expect( '' === ob_get_clean(), 'Missing explicit menu fell back to another menu.' );
expect( api( 'delete', $id )['success'] && ! \RadmanCT\Builder\Storage\exists( 'header', $id ), 'Assigned layout could not be deleted.' );
expect( '' === get_post_meta( $page, '_radman_header_layout', true ), 'Deleted layout left a stale page assignment.' );
$footer = $raw;
$footer['rows'][0]['items'][0]['text'] = 'Independent footer text';
expect( api( 'save', 'default', array( 'kind' => 'footer', 'layout' => $footer, 'revision' => '0' ) )['success'], 'Footer save failed.' );
expect( 2 === \RadmanCT\Builder\read( 'header' )['revision'], 'Footer save changed the header.' );
delete_post_meta( $page, '_radman_footer_layout' );
ob_start();
expect( \RadmanCT\Builder\render( 'footer' ), 'Saved footer did not render.' );
$footer_html = ob_get_clean();
expect( str_contains( $footer_html, 'Independent footer text' ) && str_contains( $footer_html, '<footer' ) && ! str_contains( $footer_html, 'Draft header text' ), 'Footer renderer was not independent.' );
expect( ! str_contains( $footer_html, 'rdct-builder--sticky-' ), 'Sticky setting affected footer output.' );
$linked = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'linked-row', 'items' => array(
	array( 'id' => 'linked-contact', 'type' => 'contact', 'text' => 'Office phone', 'url' => 'tel:+9821000000', 'icon' => 'phone', 'direction' => 'ltr' ),
	array( 'id' => 'linked-icon', 'type' => 'icon', 'text' => 'Location', 'url' => '/contact/', 'icon' => 'map-pin' ),
	array( 'id' => 'linked-social', 'type' => 'social', 'links' => array( array( 'id' => 'linked-profile', 'text' => 'Company profile', 'url' => 'https://example.test/', 'icon' => 'external-link' ) ) ),
) ) ) );
expect( api( 'save', 'default', array( 'kind' => 'footer', 'layout' => $linked, 'revision' => '1' ) )['success'], 'Linked footer save failed.' );
$linked_preview = api( 'preview', 'default', array( 'kind' => 'footer', 'layout' => $linked, 'direction' => 'rtl' ) );
$linked_html = $linked_preview['data']['html'] ?? '';
expect( $linked_preview['success'] && str_contains( $linked_html, 'tel:+9821000000' ) && str_contains( $linked_html, 'dir="ltr"' ) && 3 === substr_count( $linked_html, '<svg' ), 'Linked footer preview did not preserve icons and contact direction.' );
expect( str_contains( $linked_html, 'builder-links-' ) && str_contains( $linked_html, 'icons-' ) && ! str_contains( $linked_html, '<script' ), 'Conditional linked-element styles or script-free preview failed.' );
wp_set_current_user( 0 );
expect( ! api( 'read', 'default' )['success'], 'Unauthenticated user accessed builder.' );
echo "Passed: actual WordPress/PHP 8.4 builder AJAX, SQL revision conflict, named layouts, page/language assignments, deletion guards, local PHP preview and capability boundary.\n";
