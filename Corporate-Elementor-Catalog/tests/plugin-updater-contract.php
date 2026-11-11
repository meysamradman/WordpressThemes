<?php

$bundle_id = $argv[1] ?? 'content';
$slug = 'radmanct-' . $bundle_id;
$temporary = sys_get_temp_dir() . '/rdct-update-' . bin2hex( random_bytes( 8 ) );
define( 'ABSPATH', '/' );
define( 'WP_PLUGIN_DIR', $temporary . '/plugins' );
mkdir( WP_PLUGIN_DIR . '/' . $slug, 0777, true );
mkdir( $temporary . '/theme/bundled-plugins', 0777, true );
$allowed = true;
$version = '0.0.1';
class WP_Error { public function __construct( public string $code, public string $message ) {} }
function __( $text, $domain ) { return $text; }
function add_filter( ...$args ) {}
function add_action( ...$args ) {}
function current_user_can( $cap ) { return $GLOBALS['allowed'] && ( $GLOBALS['package_caps'][ $cap ] ?? true ); }
function get_template_directory() { return $GLOBALS['temporary'] . '/theme'; }
function get_file_data( $file, $headers ) { return array( 'Version' => $GLOBALS['version'] ); }
function wp_tempnam( $name ) { return tempnam( sys_get_temp_dir(), 'rdct-update-zip-' ); }
function wp_delete_file( $file ) { unlink( $file ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function is_multisite() { return true; }
function network_admin_url( $path ) { return '/network/' . $path; }
function admin_url( $path ) { return '/admin/' . $path; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function wp_nonce_url( $url, $action ) { return $url . '&nonce=' . $action; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/admin/plugin-installer.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/plugin-updater.php';
$file = \RadmanCT\Admin\PluginInstaller\bundles()[ $bundle_id ]['file'];
$token = \RadmanCT\Admin\PluginUpdater\token( $bundle_id );
$directory = get_template_directory() . '/bundled-plugins/';
$original = dirname( __DIR__ ) . '/radmanct/bundled-plugins/';
copy( $original . $slug . '.zip', $directory . $slug . '.zip' );
copy( $original . $slug . '.json', $directory . $slug . '.json' );
file_put_contents( WP_PLUGIN_DIR . '/' . $file, '<?php' );
$package = \RadmanCT\Admin\PluginInstaller\bundle_package( $bundle_id );
$extra = array( 'plugin' => $file, 'type' => 'plugin', 'action' => 'update' );
$upgrader = new stdClass();
$copy = null;
$install_copies = array();
try {
	$information = \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', (object) array( 'slug' => $slug ) );
	expect( is_object( $information ) && ! is_wp_error( $information ) && '' === $information->download_link, 'Core details did not use local metadata without exposing a package path.' );
	expect( false === \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', (object) array( 'slug' => 'other' ) ), 'Third-party plugin information was intercepted.' );
	$input = (object) array( 'response' => array( 'other/plugin.php' => (object) array( 'package' => 'other' ) ) );
	$updates = \RadmanCT\Admin\PluginUpdater\updates( $input );
	expect( isset( $updates->response[ $file ], $updates->response['other/plugin.php'] ) && ! isset( $input->response[ $file ] ), 'Update merge changed third-party or shared transient data.' );
	expect( $token === $updates->response[ $file ]->package, 'Local update exposed a filesystem path or remote URL.' );
	$plugin = array( 'installed' => true, 'active' => true, 'file' => $file, 'version' => $version );
	$action = \RadmanCT\Admin\PluginInstaller\action( $bundle_id, $plugin );
	expect( str_contains( $action['url'], '/network/update.php?' ) && str_contains( $action['url'], 'upgrade-plugin_' . $file ), 'Active Core did not receive a nonce-protected network update action.' );
	$allowed = false;
	expect( is_wp_error( \RadmanCT\Admin\PluginInstaller\information( false, 'plugin_information', (object) array( 'slug' => $slug ) ) ), 'Unauthorized Core details fell through to the remote API.' );
	expect( null === \RadmanCT\Admin\PluginInstaller\action( $bundle_id, $plugin ), 'Unauthorized update action was exposed.' );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $token, $upgrader, $extra ) ), 'Unauthorized update was accepted.' );
	$allowed = true;
	$blocked = new WP_Error( 'blocked', 'Rejected by an earlier security filter.' );
	expect( $blocked === \RadmanCT\Admin\PluginUpdater\download( $blocked, $token, $upgrader, $extra ), 'An earlier security rejection was overridden.' );
	expect( '/verified.zip' === \RadmanCT\Admin\PluginUpdater\download( '/verified.zip', $token, $upgrader, $extra ), 'An earlier handled download was overridden.' );
	expect( 'untouched' === \RadmanCT\Admin\PluginUpdater\download( 'untouched', '/uploaded.zip', $upgrader, $extra ), 'Manual ZIP upload was intercepted.' );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $token, $upgrader, array( 'plugin' => 'other/plugin.php' ) ) ), 'Package token was accepted for another plugin.' );
	$copy = \RadmanCT\Admin\PluginUpdater\download( false, $token, $upgrader, $extra );
	expect( is_string( $copy ) && $copy !== $package['path'] && hash_file( 'sha256', $copy ) === $package['sha256'], 'Updater did not receive an intact temporary copy.' );
	$wp_filesystem = new class {
		public string $version = '';
		public function get_contents( $path ) { return "<?php\n/*\nPlugin Name: Radman CT Core\nVersion: {$this->version}\n*/"; }
	};
	$source = '/working/' . $slug . '/';
	$wp_filesystem->version = $package['version'];
	expect( $source === \RadmanCT\Admin\PluginUpdater\source( $source, '/working/', $upgrader, $extra ), 'Valid package header was rejected.' );
	$install_extra = array( 'type' => 'plugin', 'action' => 'install' );
	$install_upgrader = new stdClass();
	$allowed = false;
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $package['path'], $install_upgrader, $install_extra ) ), 'Unauthorized bundled installation was accepted.' );
	$allowed = true;
	$package_caps = array( 'install_plugins' => false );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $package['path'], $install_upgrader, $install_extra ) ), 'Update permission alone allowed installation.' );
	$package_caps = array();
	$install_copies[] = \RadmanCT\Admin\PluginUpdater\download( false, $package['path'], $install_upgrader, $install_extra );
	expect( is_string( $install_copies[0] ) && $install_copies[0] !== $package['path'], 'Installation did not use a verified temporary copy.' );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\source( '/working/wrong-plugin/', '/working/', $install_upgrader, $install_extra ) ), 'Initial installation accepted the wrong plugin root.' );
	expect( ! isset( \RadmanCT\Admin\PluginUpdater\operations()[ $install_upgrader ] ), 'Failed source selection retained an operation.' );
	$install_copies[] = \RadmanCT\Admin\PluginUpdater\download( false, $package['path'], $install_upgrader, $install_extra );
	expect( $source === \RadmanCT\Admin\PluginUpdater\source( $source, '/working/', $install_upgrader, $install_extra ), 'Valid initial installation source was rejected.' );
	$wp_filesystem->version = '9.9.9';
	\RadmanCT\Admin\PluginUpdater\operations()[ $upgrader ] = array( 'id' => $bundle_id, 'version' => $package['version'] );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\source( $source, '/working/', $upgrader, $extra ) ), 'Mismatched manifest/header was accepted.' );
	\RadmanCT\Admin\PluginUpdater\operations()[ $upgrader ] = array( 'id' => $bundle_id, 'version' => $package['version'] );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\source( '/working/other/', '/working/', $upgrader, $extra ) ), 'Unexpected plugin root was accepted.' );
	foreach ( array( $package['version'], '99.0.0' ) as $version ) {
		expect( ! isset( \RadmanCT\Admin\PluginUpdater\updates( $updates )->response[ $file ] ), 'Equal/newer installation retained a stale update.' );
		expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $token, $upgrader, $extra ) ), 'Equal/newer installation allowed a downgrade.' );
	}
	$version = '0.0.1';
	file_put_contents( $directory . $slug . '.zip', 'tampered', FILE_APPEND );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $token, $upgrader, $extra ) ), 'Damaged ZIP was accepted.' );
	expect( is_wp_error( \RadmanCT\Admin\PluginUpdater\download( false, $package['path'], $install_upgrader, $install_extra ) ), 'Initial installation accepted a damaged ZIP.' );
	expect( false === \RadmanCT\Admin\PluginUpdater\automatic( true, (object) array( 'plugin' => $file ) ), 'Core automatic update was enabled.' );
	expect( true === \RadmanCT\Admin\PluginUpdater\automatic( true, (object) array( 'plugin' => 'other/plugin.php' ) ), 'Third-party automatic update was changed.' );
	unlink( $directory . $slug . '.json' );
	expect( ! isset( \RadmanCT\Admin\PluginUpdater\updates( $updates )->response[ $file ] ), 'Missing manifest retained update.' );
	expect( is_readable( $package['path'] ) && is_readable( WP_PLUGIN_DIR . '/' . $file ), 'Package or installed plugin was removed.' );
	echo "Passed: local update versions, permissions, network links, temporary copy, integrity, header/root validation, stale updates and automatic-update isolation.\n";
} finally {
	foreach ( $install_copies as $install_copy ) { if ( is_string( $install_copy ) && is_file( $install_copy ) ) { unlink( $install_copy ); } }
	if ( is_string( $copy ) && is_file( $copy ) ) { unlink( $copy ); }
	foreach ( array( $directory . $slug . '.json', $directory . $slug . '.zip', WP_PLUGIN_DIR . '/' . $file ) as $path ) {
		if ( is_file( $path ) ) { unlink( $path ); }
	}
	rmdir( $directory );
	rmdir( get_template_directory() );
	rmdir( WP_PLUGIN_DIR . '/' . $slug );
	rmdir( WP_PLUGIN_DIR );
	rmdir( $temporary );
}
