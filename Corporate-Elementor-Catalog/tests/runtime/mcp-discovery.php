<?php

$GLOBALS['wp_filter']['pre_option_radmanct_mcp_enabled'][10][] = array( 'function' => static function () { return '1'; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_radmanct_mcp_write'][10][] = array( 'function' => static function () { return '0'; }, 'accepted_args' => 0 );
require '/var/www/html/wp-load.php';

function discovery_expect( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}

$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
discovery_expect( (bool) $admins, 'No local administrator available.' );
wp_set_current_user( (int) $admins[0] );
$media = wp_get_ability( 'radmanct/list-media' );
discovery_expect( (bool) $media && ! wp_has_ability( 'radmanct/update-content' ), 'Read-only registration failed.' );
$result = $media->execute( array( 'per_page' => 1 ) );
discovery_expect( ! is_wp_error( $result ) && isset( $result['items'], $result['total'], $result['total_pages'] ) && count( $result['items'] ) <= 1, 'Native media pagination failed.' );
if ( $result['items'] ) {
	discovery_expect( isset( $result['items'][0]['id'], $result['items'][0]['source_url'] ) && ! isset( $result['items'][0]['description'] ), 'Media projection failed.' );
}
discovery_expect( is_wp_error( $media->execute( array( 'per_page' => 21 ) ) ), 'Media size bound was bypassed.' );
$content = wp_get_ability( 'radmanct/list-content' )->execute( array( 'type' => 'page', 'per_page' => 1 ) );
discovery_expect( ! is_wp_error( $content ) && isset( $content['items'], $content['page'], $content['total_pages'] ), 'Content collection contract failed.' );
require_once WP_PLUGIN_DIR . '/radmanct-mcp/inc/admin/connection.php';
$status = \RadmanCT\Mcp\Admin\connection_status();
discovery_expect( $status['abilities'] && is_array( $status['endpoints'] ), 'Connection diagnostics failed.' );
if ( ! $status['adapter'] ) { discovery_expect( ! $status['endpoints'], 'Missing adapter advertised an endpoint.' ); }
ob_start();
\RadmanCT\Mcp\Admin\connection_fields();
$markup = ob_get_clean();
discovery_expect( ! str_contains( $markup, '<script' ) && ! str_contains( $markup, 'NODE_TLS_REJECT_UNAUTHORIZED' ), 'Connection panel added a runtime or TLS override.' );
wp_set_current_user( 0 );
discovery_expect( is_wp_error( $media->execute( array() ) ), 'Anonymous access was allowed.' );
wp_set_current_user( (int) $admins[0] );
add_filter( 'pre_option_radmanct_mcp_enabled', static function () { return '0'; }, 20 );
discovery_expect( is_wp_error( $media->execute( array() ) ), 'Disabled tools remained executable.' );
echo "Passed: read-only media/content pagination, media projection and bounds, dependency diagnostics, anonymous and disabled gates. No stored options or content changed.\n";
