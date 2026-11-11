<?php

namespace WP\MCP\Core {
	class McpAdapter {
		public static function instance(): self { return new self(); }
		public function get_servers(): array {
			return array_map( static function ( $route ) {
				return new class( $route ) {
					public function __construct( private string $route ) {}
					public function get_server_route_namespace(): string { return 'custom/v2'; }
					public function get_server_route(): string { return $this->route; }
				};
			}, array( 'assistant', 'stdio-only' ) );
		}
	}
}

namespace {
	define( 'ABSPATH', __DIR__ );
	function wp_register_ability(): void {}
	function wp_get_current_user(): object { return new stdClass(); }
	function wp_is_application_passwords_available_for_user( $user ): bool { return false; }
	function rest_url( $path ): string { return 'https://example.test/subsite/?rest_route=/' . $path; }
	function rest_get_server(): object {
		return new class {
			public function get_routes(): array {
				return array( '/custom/v2/assistant' => array( array( 'methods' => array( 'POST' => true ) ) ) );
			}
		};
	}
	require $argv[1] ?? __DIR__ . '/../radmanct-mcp/inc/admin/connection.php';
	$status = \RadmanCT\Mcp\Admin\connection_status();
	if ( array( 'https://example.test/subsite/?rest_route=/custom/v2/assistant' ) !== $status['endpoints'] || $status['application_passwords'] ) {
		throw new RuntimeException( 'Configured HTTP routes, plain permalinks or account availability were misreported.' );
	}
	echo "Passed: custom route, HTTP-only detection, native REST URL and unavailable application passwords.\n";
}
