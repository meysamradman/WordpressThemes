<?php

if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) || ! is_dir( '/var/www/html/wp-includes' ) ) {
	exit( 1 );
}
file_put_contents( '/var/www/html/wp-config.php', <<<'CONFIG'
<?php
define( 'DB_NAME', 'radman_qa' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'db' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
define( 'WP_HOME', 'http://radman.test' );
define( 'WP_SITEURL', 'http://radman.test' );
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'DISABLE_WP_CRON', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
$table_prefix = 'qa_';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/var/www/html/' ); }
require ABSPATH . 'wp-settings.php';
CONFIG
);
mkdir( '/var/www/html/wp-content/mu-plugins', 0777, true );
file_put_contents( '/var/www/html/wp-content/mu-plugins/qa-boundaries.php', <<<'PLUGIN'
<?php
add_filter( 'pre_http_request', static function () { return new WP_Error( 'qa_network_disabled', 'Network disabled in isolated QA.' ); } );
add_filter( 'pre_wp_mail', '__return_false' );
PLUGIN
);
mysqli_report( MYSQLI_REPORT_OFF );
$ready = false;
for ( $attempt = 0; $attempt < 60; ++$attempt ) {
	$db = @new mysqli( 'db', 'root', '', 'radman_qa' );
	if ( ! $db->connect_errno ) { $ready = true; $db->close(); break; }
	usleep( 200000 );
}
if ( ! $ready ) { throw new RuntimeException( 'Isolated database did not become ready.' ); }
define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST'] = 'radman.test';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
wp_install( 'Radman CT QA', 'qa_admin', 'qa@example.invalid', false, '', wp_generate_password( 32 ), 'en_US' );
switch_theme( 'radmanct' );
echo "Installed isolated WordPress.\n";
