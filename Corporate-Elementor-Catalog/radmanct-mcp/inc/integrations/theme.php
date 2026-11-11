<?php

namespace RadmanCT\Mcp\Integrations\Theme;

defined( 'ABSPATH' ) || exit;

function available(): bool {
	return current_user_can( 'edit_theme_options' ) && function_exists( '\\RadmanCT\\Builder\\read' );
}

function read( array $input ) {
	if ( ! available() || ! \RadmanCT\Builder\Storage\exists( $input['kind'], $input['layout_id'] ?? 'default' ) ) { return \RadmanCT\Mcp\Content\error( 'Radman CT layout is unavailable.' ); }
	return \RadmanCT\Builder\read( $input['kind'], $input['layout_id'] ?? 'default' );
}

function save( array $input ) {
	$old = read( $input );
	if ( is_wp_error( $old ) ) { return $old; }
	if ( strlen( wp_json_encode( $input['layout'] ) ) > 131072 ) { return \RadmanCT\Mcp\Content\error( 'Layout exceeds the size limit.' ); }
	$layout = \RadmanCT\Builder\validate( $input['layout'] );
	if ( null === $layout ) { return \RadmanCT\Mcp\Content\error( 'Invalid Radman CT layout.' ); }
	if ( $old['revision'] !== $input['revision'] ) { return \RadmanCT\Mcp\Content\error( 'Layout changed. Read it again before saving.', 'radmanct_mcp_conflict' ); }
	if ( $input['dry_run'] ?? false ) { return array( 'status' => 'validated', 'layout' => $layout, 'revision' => $old['revision'] ); }
	$result = \RadmanCT\Builder\Storage\save( $input['kind'], $input['layout_id'] ?? 'default', $layout, $input['revision'] );
	return 'saved' === $result['status'] ? $result : \RadmanCT\Mcp\Content\error( 'Layout changed. Read it again before saving.', 'radmanct_mcp_conflict' );
}
