<?php

namespace RadmanCT\Mcp\Admin;

defined( 'ABSPATH' ) || exit;

function register(): void {
	foreach ( array( 'radmanct_mcp_enabled', 'radmanct_mcp_write' ) as $name ) {
		register_setting( 'radmanct_mcp', $name, array( 'type' => 'boolean', 'default' => false, 'show_in_rest' => false, 'sanitize_callback' => static function ( $value ): bool { return in_array( $value, array( true, 1, '1' ), true ); } ) );
	}
}

function fields(): void {
	echo '<section class="rdct-cor-module"><h2>' . esc_html__( 'AI connection (MCP)', 'radmanct-mcp' ) . '</h2><p>' . esc_html__( 'Optional administrator tools for content and Radman CT layouts.', 'radmanct-mcp' ) . '</p>';
	foreach ( array( 'radmanct_mcp_enabled' => __( 'Enable MCP tools', 'radmanct-mcp' ), 'radmanct_mcp_write' => __( 'Allow content and layout changes', 'radmanct-mcp' ) ) as $name => $label ) {
		echo '<p><label><input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( (bool) get_option( $name, false ), true, false ) . '> ' . esc_html( $label ) . '</label></p>';
	}
	echo '</section>';
}

function render(): void {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	echo '<div class="wrap"><h1>' . esc_html__( 'Radman MCP', 'radmanct-mcp' ) . '</h1>';
	settings_errors();
	echo '<form action="' . esc_url( admin_url( 'options.php' ) ) . '" method="post">';
	settings_fields( 'radmanct_mcp' );
	fields();
	submit_button();
	echo '</form>';
	require_once __DIR__ . '/connection.php';
	connection_fields();
	echo '</div>';
}

add_action( 'admin_init', __NAMESPACE__ . '\\register' );
add_action( 'admin_menu', static function (): void {
	add_options_page( __( 'Radman MCP', 'radmanct-mcp' ), __( 'Radman MCP', 'radmanct-mcp' ), 'manage_options', 'radmanct-mcp', __NAMESPACE__ . '\\render' );
} );
