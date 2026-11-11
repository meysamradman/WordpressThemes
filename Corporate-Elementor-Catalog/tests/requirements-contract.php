<?php

define( 'ABSPATH', '/' );
$allowed = false;
$manager = false;
$network = false;
$scans = 0;
$installed = array( 'radmanct-mcp/radmanct-mcp.php' => array( 'Version' => '<unsafe-version>' ), 'radmanct-content/radmanct-content.php' => array( 'Version' => '0.1.0' ) );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function __( $text, $domain ) { return $text; }
function esc_html__( $text, $domain ) { return esc_html( $text ); }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function esc_url( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function current_user_can( $capability ) { return 'edit_theme_options' === $capability ? $GLOBALS['allowed'] : $GLOBALS['manager']; }
function get_site_transient( $key ) { return false; }
function get_plugins() { ++$GLOBALS['scans']; return $GLOBALS['installed']; }
function is_plugin_active( $file ) { return 'radmanct-mcp/radmanct-mcp.php' === $file; }
function is_multisite() { return $GLOBALS['network']; }
function admin_url( $path ) { return '/wp-admin/' . $path; }
function network_admin_url( $path ) { return '/wp-admin/network/' . $path; }
function get_template_directory() { return $GLOBALS['requirements_theme_dir'] ?? dirname( __DIR__ ) . '/radmanct'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function wp_nonce_url( $url, $action ) { return $url . '&_wpnonce=' . $action; }
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/admin/requirements-page.php';
expect( 0 === $scans, 'Including the admin module scanned plugins.' );
expect( array() === \RadmanCT\Admin\RequirementsPage\statuses(), 'Unauthorized status access returned plugin data.' );
ob_start();
\RadmanCT\Admin\RequirementsPage\render();
expect( '' === ob_get_clean() && 0 === $scans, 'Unauthorized render scanned plugins or exposed content.' );
$allowed = true;
$states = \RadmanCT\Admin\RequirementsPage\statuses();
expect( 5 === count( $states ) && isset( $states['components'], $states['content'], $states['mcp'], $states['mcp-adapter'], $states['elementor'], $states['mcp'] ), 'Important optional plugin inventory is incomplete or includes unrelated packages.' );
expect( $states['content']['update_available'] && '' !== $states['mcp']['available'], 'Bundled version status is missing.' );
expect( ! $states['elementor']['installed'] && ! $states['elementor']['active'], 'Missing plugin reported as present.' );
expect( $states['content']['installed'] && ! $states['content']['active'], 'Inactive plugin reported as active.' );
expect( $states['mcp']['active'] && $states['mcp']['dependency_missing'], 'Active plugin with missing prerequisite was not identified.' );
ob_start();
\RadmanCT\Admin\RequirementsPage\render();
$html = ob_get_clean();
expect( ! str_contains( $html, '<unsafe-version>' ) && str_contains( $html, '&lt;unsafe-version&gt;' ), 'Plugin metadata was not escaped.' );
expect( ! str_contains( $html, 'href=' ) && ! str_contains( $html, '<script' ) && ! str_contains( $html, '<link' ), 'Limited administrator got management links or extra assets.' );
$manager = true;
$network = true;
ob_start();
\RadmanCT\Admin\RequirementsPage\render();
expect( str_contains( ob_get_clean(), '/wp-admin/network/plugins.php' ), 'Network administrator did not receive the network plugin manager.' );
expect( 3 === $scans, 'Status page repeated inventory scans within a render.' );
$missing = array( 'installed' => false, 'active' => false, 'dependency_missing' => false, 'file' => 'elementor/elementor.php' );
$install = \RadmanCT\Admin\PluginInstaller\action( 'elementor', $missing );
expect( str_contains( $install['url'], '/network/plugin-install.php?tab=upload' ), 'Elementor did not use local ZIP upload.' );
$core_install = \RadmanCT\Admin\PluginInstaller\action( 'content', array_merge( $missing, array( 'file' => 'radmanct-content/radmanct-content.php' ) ) );
$mcp_install = \RadmanCT\Admin\PluginInstaller\action( 'mcp', array_merge( $missing, array( 'file' => 'radmanct-mcp/radmanct-mcp.php' ) ) );
expect( str_contains( $mcp_install['url'], 'install-plugin_radmanct-mcp' ), 'MCP did not use its own nonce-protected local installer.' );
expect( str_contains( $core_install['url'], '/network/update.php?' ) && str_contains( $core_install['url'], 'install-plugin_radmanct-content' ), 'Core did not use the nonce-protected local installer.' );
expect( str_contains( \RadmanCT\Admin\PluginInstaller\action( 'wpml', $missing )['url'], 'plugin-install.php?tab=upload' ), 'Commercial plugin did not use WordPress ZIP upload.' );
$inactive = array_merge( $missing, array( 'installed' => true ) );
expect( str_contains( \RadmanCT\Admin\PluginInstaller\action( 'elementor', $inactive )['url'], 'activate-plugin_elementor' ), 'Installed plugin did not use native activation.' );
$inactive['dependency_missing'] = true;
expect( null === \RadmanCT\Admin\PluginInstaller\action( 'elementor-pro', $inactive ), 'Activation was offered without an active prerequisite.' );
$manager = false;
expect( null === \RadmanCT\Admin\PluginInstaller\action( 'elementor', $missing ), 'Unprivileged user received an installation action.' );
$package = \RadmanCT\Admin\PluginInstaller\bundle_package();
expect( null !== $package, 'Generated Core package failed checksum validation.' );
$temporary = sys_get_temp_dir() . '/rdct-package-contract-' . bin2hex( random_bytes( 8 ) );
mkdir( $temporary . '/bundled-plugins', 0777, true );
try {
	copy( $package['path'], $temporary . '/bundled-plugins/radmanct-content.zip' );
	copy( get_template_directory() . '/bundled-plugins/radmanct-content.json', $temporary . '/bundled-plugins/radmanct-content.json' );
	$GLOBALS['requirements_theme_dir'] = $temporary;
	file_put_contents( $temporary . '/bundled-plugins/radmanct-content.zip', 'changed', FILE_APPEND );
	expect( null === \RadmanCT\Admin\PluginInstaller\bundle_package(), 'Changed Core package bypassed checksum validation.' );
} finally {
	unset( $GLOBALS['requirements_theme_dir'] );
	unlink( $temporary . '/bundled-plugins/radmanct-content.zip' );
	unlink( $temporary . '/bundled-plugins/radmanct-content.json' );
	rmdir( $temporary . '/bundled-plugins' );
	rmdir( $temporary );
}
echo "Passed: requirements capability boundary, local status inventory, dependency reporting, metadata escaping and native management links.\n";
