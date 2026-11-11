<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_set_current_user( 1 );
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$mode = $argv[1] ?? 'enabled';
if ( 'activate' === $mode ) {
	$result = activate_plugin( 'radmanct-content/radmanct-content.php' );
	expect( ! is_wp_error( $result ), 'Core activation failed.' );
	expect( ! post_type_exists( 'radman_product' ) && ! post_type_exists( 'radman_portfolio' ), 'Core enabled modules by default.' );
	expect( ! function_exists( '\\RadmanCTContent\\Model\\read' ), 'Disabled Core loaded content logic.' );
	update_option( 'radman_core_modules', array( 'catalog' => true, 'portfolio' => true ) );
	echo "Passed: Core activation leaves modules disabled by default.\n";
	exit;
}
if ( 'portfolio-only' === $mode ) {
	expect( ! post_type_exists( 'radman_product' ) && post_type_exists( 'radman_portfolio' ), 'Catalog could not be disabled independently.' );
	expect( ! taxonomy_exists( 'radman_product_cat' ) && ! function_exists( '\\RadmanCTContent\\Catalog\\query' ), 'Disabled catalog retained taxonomy or query logic.' );
	expect( ! shortcode_exists( 'radman_catalog_filters' ) && shortcode_exists( 'radman_content_gallery' ), 'Disabled catalog retained frontend tools.' );
	expect( 'Pump Alpha' === get_the_title( (int) get_option( 'qa_product_id' ) ), 'Disabling catalog deleted product data.' );
	update_option( 'radman_core_modules', array( 'catalog' => false, 'portfolio' => false ) );
	echo "Passed: portfolio operates with catalog disabled and product data retained.\n";
	exit;
}
if ( 'disabled' === $mode ) {
	expect( ! post_type_exists( 'radman_product' ) && ! post_type_exists( 'radman_portfolio' ), 'Disabled Core retained post types.' );
	expect( ! function_exists( '\\RadmanCTContent\\Model\\read' ) && ! function_exists( '\\RadmanCT\\Content\\data' ), 'Disabled modules loaded model or theme integration.' );
	expect( (bool) get_post_meta( (int) get_option( 'qa_product_id' ), '_radman_content_data', true ), 'Disabled modules lost metadata.' );
	echo "Passed: all content modules disabled without model, theme hooks or data deletion.\n";
	exit;
}
if ( 'other-theme' === $mode ) {
	expect( 'radmanct' !== get_template() && post_type_exists( 'radman_product' ) && post_type_exists( 'radman_portfolio' ), 'Core depends on Radman CT theme.' );
	expect( \RadmanCTContent\Model\read( (int) get_option( 'qa_product_id' ) )['code'] === 'P-100', 'Theme switch lost product data.' );
	switch_theme( 'radmanct' );
	update_option( 'radman_core_modules', array( 'catalog' => false, 'portfolio' => true ) );
	echo "Passed: content types and metadata remain available with another theme.\n";
	exit;
}

expect( post_type_exists( 'radman_product' ) && post_type_exists( 'radman_portfolio' ), 'Enabled types did not register.' );
expect( taxonomy_exists( 'radman_product_brand' ) && taxonomy_exists( 'radman_portfolio_cat' ), 'Enabled taxonomies missing.' );
require_once '/workspace/radmanct-content/inc/admin/content.php';
$product = wp_insert_post( array( 'post_type' => 'radman_product', 'post_status' => 'publish', 'post_title' => 'Pump Alpha', 'post_excerpt' => 'Public product introduction', 'post_content' => '<p>Public product description</p>' ) );
$other = wp_insert_post( array( 'post_type' => 'radman_product', 'post_status' => 'publish', 'post_title' => 'Valve Beta' ) );
$draft = wp_insert_post( array( 'post_type' => 'radman_product', 'post_status' => 'draft', 'post_title' => 'Pump Draft' ) );
$project = wp_insert_post( array( 'post_type' => 'radman_portfolio', 'post_status' => 'publish', 'post_title' => 'Independent project' ) );
update_option( 'qa_product_id', $product );
wp_set_object_terms( $product, 'industrial', 'radman_product_cat' );
wp_set_object_terms( $product, 'rdct-brand', 'radman_product_brand' );
wp_set_object_terms( $draft, 'industrial', 'radman_product_cat' );
$upload = wp_upload_bits( 'rdct-qa.png', null, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jYuoAAAAASUVORK5CYII=' ) );
expect( ! $upload['error'], 'Temporary gallery image could not be created.' );
$image = wp_insert_attachment( array( 'post_mime_type' => 'image/png', 'post_title' => 'Gallery image', 'post_status' => 'inherit' ), $upload['file'], $product );
wp_update_attachment_metadata( $image, array( 'width' => 1, 'height' => 1, 'file' => _wp_relative_upload_path( $upload['file'] ) ) );
$data = array( 'version' => 1, 'code' => 'P-100', 'gallery' => array( $image ), 'specifications' => array( array( 'id' => 'flow', 'label' => 'Flow rate', 'value' => '50', 'unit' => 'm3/h' ) ) );
$data['specifications'][] = array( 'id' => 'path', 'label' => 'Drawing path', 'value' => 'C:\\drawings\\pump', 'unit' => '' );
$_POST = array( 'radman_content_nonce' => wp_create_nonce( 'radman_content_' . $product ), 'radman_content_data' => wp_slash( wp_json_encode( $data ) ) );
\RadmanCTContent\Admin\Content\save( $product );
expect( $data === \RadmanCTContent\Model\read( $product ), 'Native content metadata did not save.' );
$_POST['radman_content_data'] = '{broken';
\RadmanCTContent\Admin\Content\save( $product );
expect( $data === \RadmanCTContent\Model\read( $product ), 'Invalid JSON overwrote saved details.' );
$_POST['radman_content_nonce'] = 'invalid';
$_POST['radman_content_data'] = wp_slash( wp_json_encode( array_merge( $data, array( 'code' => 'Changed' ) ) ) );
\RadmanCTContent\Admin\Content\save( $product );
expect( 'P-100' === \RadmanCTContent\Model\read( $product )['code'], 'Invalid nonce changed product metadata.' );
wp_set_current_user( 0 );
$_POST['radman_content_nonce'] = wp_create_nonce( 'radman_content_' . $product );
\RadmanCTContent\Admin\Content\save( $product );
expect( 'P-100' === \RadmanCTContent\Model\read( $product )['code'], 'Guest changed product metadata.' );
wp_set_current_user( 1 );

function query( array $args ): WP_Query {
	$query = new WP_Query();
	$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = $query;
	$query->query( $args );
	return $query;
}
$_GET = array( 'rm_category' => 'industrial', 'rm_brand' => 'rdct-brand', 'rm_search' => 'Pump', 'rm_sort' => 'title' );
$query = query( array( 'post_type' => 'radman_product' ) );
expect( array( $product ) === array_map( 'intval', wp_list_pluck( $query->posts, 'ID' ) ), 'Catalog filtering included unrelated or draft content.' );
expect( 12 === $query->get( 'posts_per_page' ) && ! $query->get( 'meta_query' ), 'Catalog query has unbounded pagination or metadata filtering.' );
$query = query( array( 'post_type' => 'post', 'posts_per_page' => 7 ) );
expect( 7 === $query->get( 'posts_per_page' ) && ! $query->get( 'tax_query' ) && ! $query->get( 's' ), 'Catalog filters changed blog query.' );
$_GET = array();
$query = query( array( 'p' => $product, 'post_type' => 'radman_product' ) );
ob_start();
\RadmanCT\Content\enqueue();
\RadmanCT\Content\gallery( $product );
\RadmanCT\Content\specifications( $product );
$html = ob_get_clean();
expect( str_contains( $html, 'data-rdct-gallery' ) && str_contains( $html, 'Flow rate' ), 'Gallery or specifications did not render.' );
expect( wp_style_is( 'rdct-catalog-single', 'enqueued' ) && wp_style_is( 'rdct-gallery', 'enqueued' ) && ! wp_style_is( 'rdct-portfolio-single', 'enqueued' ), 'Content assets were not isolated.' );
$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Contact' ) );
update_option( 'radman_core_inquiry_page', $page );
expect( get_permalink( $page ) === \RadmanCTContent\Catalog\inquiry_url( $product ), 'Contact link did not use the selected page directly.' );
wp_update_post( array( 'ID' => $page, 'post_status' => 'draft' ) );
expect( '' === \RadmanCTContent\Catalog\inquiry_url( $product ), 'Draft contact page appeared in inquiry output.' );
$secret = wp_insert_post( array( 'post_type' => 'radman_product', 'post_status' => 'publish', 'post_title' => 'Protected', 'post_password' => 'private-password' ) );
update_post_meta( $secret, \RadmanCTContent\Model\META, $data );
query( array( 'p' => $secret, 'post_type' => 'radman_product' ) );
expect( array() === \RadmanCT\Content\gallery_ids( $secret ) && '' === do_shortcode( '[radman_product_specs][radman_content_gallery]' ), 'Password-protected metadata leaked.' );
wp_set_current_user( 0 );
query( array( 'p' => $product, 'post_type' => 'radman_product' ) );
$response = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/radman_product/' . $product ) );
expect( ! isset( $response->get_data()['meta'][ \RadmanCTContent\Model\META ] ), 'Private editing metadata appeared in REST.' );
wp_set_current_user( 1 );
$other_theme = wp_get_theme( 'twentytwentyfive' );
expect( $other_theme->exists(), 'Bundled alternate theme is missing.' );
$catalog = json_decode( file_get_contents( '/workspace/tools/translations/fa_IR.json' ), true );
add_filter( 'locale', static function () { return 'fa_IR'; } );
unload_textdomain( 'radmanct-content', true );
load_plugin_textdomain( 'radmanct-content', false, 'radmanct-content/languages' );
expect( __( 'Products', 'radmanct-content' ) === $catalog['radmanct-content']['Products'], 'Core Persian plugin text domain did not load.' );
switch_theme( 'twentytwentyfive' );
echo "Passed: independent catalog/portfolio, secure bounded metadata, real filters, blog isolation, gallery/specification assets, protected content, inquiry target and REST boundaries.\n";
