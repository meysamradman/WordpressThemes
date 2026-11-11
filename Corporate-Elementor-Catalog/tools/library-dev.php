<?php
if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { http_response_code( 404 ); exit; }
require '/var/www/html/wp-load.php';
wp_set_current_user( 1 );
require_once RDCT_COMPONENTS_PATH . '/inc/admin/components-page.php';
if ( 'index' === ( $_GET['view'] ?? '' ) ) {
    header( 'Content-Type: application/json; charset=utf-8' );
    echo wp_json_encode( \RadmanCTComponents\Components\Library\catalog(), JSON_UNESCAPED_UNICODE );
    exit;
}
if ( 'jobs' === ( $_GET['view'] ?? '' ) ) {
    require_once RDCT_COMPONENTS_PATH . '/inc/components/library-preview.php';
    $jobs = array(); $definitions = \RadmanCTComponents\Components\definitions();
    foreach ( \RadmanCTComponents\Components\Library\catalog() as $id => $item ) {
        if ( ! empty( $item['hidden'] ) ) { continue; }
        $settings = \RadmanCTComponents\Components\Library\preview_settings( $id, 'fa', array() );
        $requirements = \RadmanCTComponents\Components\requirements( $item['component'], $settings );
        $entries = array( 'src/css/base.css', 'src/css/admin/component-preview.css' );
        foreach ( $requirements['styles'] as $key ) { if ( isset( $definitions[ $key ] ) ) { $entries[] = $definitions[ $key ]['entry']; } }
        $jobs[] = array( 'id' => $id, 'component' => $item['component'], 'family' => $item['family'], 'variant' => $item['variant'], 'settings' => $settings, 'entries' => array_unique( $entries ), 'script' => $requirements['script'] );
    }
    header( 'Content-Type: application/json; charset=utf-8' ); echo wp_json_encode( $jobs, JSON_UNESCAPED_UNICODE ); exit;
}
$_GET['_wpnonce'] = wp_create_nonce( 'radman_component_preview' );
$_REQUEST['_wpnonce'] = $_GET['_wpnonce'];
\RadmanCTComponents\Admin\ComponentsPage\preview();
