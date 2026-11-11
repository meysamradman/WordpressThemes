<?php

namespace RadmanCT\Mcp\Admin;

defined( 'ABSPATH' ) || exit;

function connection_status(): array {
	$adapter = class_exists( '\\WP\\MCP\\Core\\McpAdapter' );
	$endpoints = array();
	if ( $adapter && is_callable( array( '\\WP\\MCP\\Core\\McpAdapter', 'instance' ) ) ) {
		$instance = \WP\MCP\Core\McpAdapter::instance();
		if ( is_callable( array( $instance, 'get_servers' ) ) ) {
			$routes = rest_get_server()->get_routes();
			foreach ( $instance->get_servers() as $server ) {
				if ( ! is_callable( array( $server, 'get_server_route_namespace' ) ) || ! is_callable( array( $server, 'get_server_route' ) ) ) { continue; }
				$route = '/' . trim( $server->get_server_route_namespace(), '/' ) . '/' . trim( $server->get_server_route(), '/' );
				foreach ( $routes[ $route ] ?? array() as $handler ) {
					if ( is_array( $handler ) && ! empty( $handler['methods']['POST'] ) ) { $endpoints[] = rest_url( ltrim( $route, '/' ) ); break; }
				}
			}
		}
	}
	return array(
		'abilities' => function_exists( 'wp_register_ability' ),
		'adapter' => $adapter,
		'endpoints' => array_values( array_unique( $endpoints ) ),
		'application_passwords' => function_exists( 'wp_is_application_passwords_available_for_user' ) && wp_is_application_passwords_available_for_user( wp_get_current_user() ),
	);
}

function connection_fields(): void {
	$status = connection_status();
	echo '<h2>' . esc_html__( 'Connection status', 'radmanct-mcp' ) . '</h2><ul>';
	foreach ( array(
		__( 'WordPress Abilities API', 'radmanct-mcp' ) => $status['abilities'],
		__( 'Official MCP Adapter', 'radmanct-mcp' ) => $status['adapter'],
		__( 'Registered HTTP endpoint', 'radmanct-mcp' ) => (bool) $status['endpoints'],
		__( 'Application passwords for your account', 'radmanct-mcp' ) => $status['application_passwords'],
	) as $label => $available ) {
		echo '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $available ? __( 'Available', 'radmanct-mcp' ) : __( 'Unavailable', 'radmanct-mcp' ) ) . '</li>';
	}
	echo '</ul><p>' . esc_html__( 'Detected routes do not verify client authentication or access to every ability. Custom servers may expose a different tool set.', 'radmanct-mcp' ) . '</p>';
	foreach ( $status['endpoints'] as $index => $endpoint ) {
		$id = 'rdct-mcp-endpoint-' . $index;
		echo '<p><label for="' . esc_attr( $id ) . '">' . esc_html__( 'Adapter endpoint', 'radmanct-mcp' ) . '</label><br><input id="' . esc_attr( $id ) . '" class="large-text code" type="url" dir="ltr" readonly value="' . esc_attr( $endpoint ) . '"></p>';
	}
	echo '<h2>' . esc_html__( 'Connect your assistant', 'radmanct-mcp' ) . '</h2><ol>';
	foreach ( array(
		__( 'Install and activate the official MCP Adapter separately. The AI plugin is not required for Radman tools.', 'radmanct-mcp' ),
		__( 'Enable Radman tools and save. Leave changes disabled for read-only access.', 'radmanct-mcp' ),
		__( 'Create an application password in your WordPress profile. Use your login name and that password in your client; never use your main account password.', 'radmanct-mcp' ),
		__( 'Use a registered endpoint above with a transport supported by your client. Follow the official Adapter connection guide. Do not disable TLS verification.', 'radmanct-mcp' ),
		__( 'Ask the assistant to discover abilities, inspect radmanct/site-context, then execute it with parameters={}. Read the target and its version before requesting changes.', 'radmanct-mcp' ),
	) as $step ) { echo '<li>' . esc_html( $step ) . '</li>'; }
	echo '</ol><p><a class="button" href="' . esc_url( admin_url( 'profile.php#application-passwords-section' ) ) . '">' . esc_html__( 'Application passwords', 'radmanct-mcp' ) . '</a> <a href="https://github.com/WordPress/mcp-adapter/blob/trunk/docs/getting-started/site-owners.md" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Official connection guide', 'radmanct-mcp' ) . '</a></p>';
	echo '<p>' . esc_html__( 'Enable Elementor tools in Elementor itself. Radman does not rewrite Elementor documents or custom PHP source files.', 'radmanct-mcp' ) . '</p>';
}
