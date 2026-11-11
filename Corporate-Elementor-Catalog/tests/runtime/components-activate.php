<?php
if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$result = activate_plugin( 'radmanct-components/radmanct-components.php' );
if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
