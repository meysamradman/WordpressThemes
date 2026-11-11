<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
wp_set_current_user( 1 );
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function ability( string $name, array $input = array() ) {
	$ability = wp_get_ability( 'radmanct/' . $name );
	expect( (bool) $ability, 'Ability missing: ' . $name );
	return $ability->execute( $input );
}
$mode = $argv[1] ?? 'write';
if ( 'prepare' === $mode ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$result = activate_plugin( 'mcp-adapter/mcp-adapter.php' );
	expect( ! is_wp_error( $result ), is_wp_error( $result ) ? $result->get_error_message() : '' );
	$result = activate_plugin( 'radmanct-mcp/radmanct-mcp.php' );
	expect( ! is_wp_error( $result ), is_wp_error( $result ) ? $result->get_error_message() : '' );
	expect( ! wp_has_ability( 'radmanct/site-context' ), 'MCP is enabled by default.' );
	update_option( 'radmanct_mcp_enabled', true );
	update_option( 'radmanct_mcp_write', false );
	exit;
}
if ( 'standalone-prepare' === $mode ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( 'radmanct-content/radmanct-content.php' );
	switch_theme( 'twentytwentyfive' );
	update_option( 'radmanct_mcp_enabled', true );
	update_option( 'radmanct_mcp_write', true );
	exit;
}
if ( 'standalone' === $mode ) {
	$context = ability( 'site-context' );
	expect( array( 'post', 'page' ) === $context['content_types'], 'MCP requires the content plugin.' );
	expect( ! wp_has_ability( 'radmanct/read-layout' ), 'Unavailable theme layout tools remained exposed.' );
	$result = ability( 'create-content', array( 'type' => 'page', 'fields' => array( 'title' => 'Independent MCP page' ) ) );
	expect( ! is_wp_error( $result ) && 'draft' === $result['fields']['status'], 'Standalone native page creation failed.' );
	expect( null === $result['details'], 'Standalone content unexpectedly read Core data.' );
	echo "Passed: standalone MCP operates without Radman CT Content or Theme, with only native page/post tools.\n";
	exit;
}
if ( 'read' === $mode ) {
	rest_get_server();
	$dispatcher = wp_get_ability( 'mcp-adapter/execute-ability' );
	expect( (bool) $dispatcher, 'Supplied Adapter dispatcher was not registered.' );
	$dispatched = $dispatcher->execute( array( 'ability_name' => 'radmanct/site-context', 'parameters' => array() ) );
	expect( ! is_wp_error( $dispatched ) && ( $dispatched['success'] ?? false ), is_wp_error( $dispatched ) ? $dispatched->get_error_message() : 'Argument-free Adapter dispatch failed.' );
	expect( ! is_wp_error( wp_get_ability( 'radmanct/site-context' )->execute( null ) ), 'Empty input schema default failed.' );
	$context = ability( 'site-context' );
	expect( ! is_wp_error( $context ) && ! $context['writes_enabled'], 'Read-only context failed.' );
	expect( ! wp_has_ability( 'radmanct/create-content' ), 'Read-only mode exposed creation.' );
	update_option( 'radmanct_mcp_write', true );
	echo "Passed: MCP default-off, read-only discovery, actual Adapter 0.7.1 dispatch with empty parameters and native null-input default.\n";
	exit;
}
foreach ( array( 'post', 'page', 'radman_product', 'radman_portfolio' ) as $type ) {
	$result = ability( 'create-content', array( 'type' => $type, 'fields' => array( 'title' => 'MCP ' . $type, 'content' => '<p>Native content</p>' ) ) );
	expect( ! is_wp_error( $result ), is_wp_error( $result ) ? $result->get_error_message() : '' );
	expect( 'draft' === $result['fields']['status'], 'Creation published without explicit status.' );
	$updated = ability( 'update-content', array( 'id' => $result['id'], 'version' => $result['version'], 'fields' => array( 'title' => 'Changed ' . $type ) ) );
	expect( ! is_wp_error( $updated ) && 'Changed ' . $type === $updated['fields']['title'], 'Native content update failed.' );
	expect( is_wp_error( ability( 'update-content', array( 'id' => $result['id'], 'version' => $result['version'], 'fields' => array( 'title' => 'Stale overwrite' ) ) ) ), 'Stale update overwrote newer content.' );
	if ( 'radman_product' === $type ) {
		$details = $updated['details']; $details['code'] = 'MCP-01'; $details['pricing'] = array( 'mode' => 'price', 'text' => '120 USD' );
		$term = wp_insert_term( 'MCP brand', 'radman_product_brand' );
		$updated = ability( 'update-content', array( 'id' => $result['id'], 'version' => $updated['version'], 'fields' => array( 'radman_product_brand' => array( $term['term_id'] ) ), 'details' => $details ) );
		expect( ! is_wp_error( $updated ) && 'MCP-01' === $updated['details']['code'] && array( $term['term_id'] ) === $updated['fields']['radman_product_brand'], 'Core details or native terms did not save.' );
	}
}
update_post_meta( $updated['id'], '_elementor_edit_mode', 'builder' );
$owned = ability( 'read-content', array( 'id' => $updated['id'] ) );
expect( is_wp_error( ability( 'update-content', array( 'id' => $owned['id'], 'version' => $owned['version'], 'fields' => array( 'content' => 'Wrong owner' ) ) ) ), 'Elementor body ownership was bypassed.' );
$layout = ability( 'read-layout', array( 'kind' => 'header' ) );
$input = array( 'kind' => 'header', 'revision' => $layout['revision'], 'layout' => array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'mcpRow', 'items' => array( array( 'id' => 'mcpText', 'type' => 'text', 'text' => 'MCP header' ) ) ) ) ), 'dry_run' => true );
$dry = ability( 'save-layout', $input );
expect( ! is_wp_error( $dry ), is_wp_error( $dry ) ? $dry->get_error_message() : '' );
expect( 'validated' === $dry['status'], 'Layout dry run failed.' );
expect( $layout['revision'] === ability( 'read-layout', array( 'kind' => 'header' ) )['revision'], 'Dry run mutated storage.' );
$input['dry_run'] = false;
expect( 'saved' === ability( 'save-layout', $input )['status'], 'Native layout save failed.' );
expect( is_wp_error( ability( 'save-layout', $input ) ), 'Stale layout revision was accepted.' );
wp_set_current_user( 0 );
expect( is_wp_error( ability( 'site-context' ) ), 'Anonymous access was accepted.' );
wp_set_current_user( 1 );
update_option( 'radmanct_mcp_enabled', false );
expect( is_wp_error( ability( 'site-context' ) ), 'Disabled MCP remained accessible.' );
echo "Passed: real WordPress abilities, native draft create/update for four content types, stale-write rejection, Core price/brand, Elementor ownership, layout dry-run/revision and permission boundaries.\n";
