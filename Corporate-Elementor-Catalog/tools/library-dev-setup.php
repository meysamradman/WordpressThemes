<?php
if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$path = '/var/www/html/wp-config.php';
file_put_contents( $path, str_replace( 'http://radman.test', 'http://127.0.0.1:8094', file_get_contents( $path ) ) );
file_put_contents( '/var/www/html/wp-content/mu-plugins/library-dev.php', '<?php if ( "1" === getenv("RDCT_ISOLATED_QA") ) { add_filter("determine_current_user", static fn() => 1, 99); add_action("init",static function(){ if(!wp_validate_auth_cookie("","logged_in")){wp_set_auth_cookie(1);} }); }' );
require '/var/www/html/wp-load.php';
update_option( 'active_plugins', array( 'radmanct-components/radmanct-components.php' ) );
copy( '/workspace/tools/library-dev.php', '/var/www/html/library-dev.php' );
echo "Local library development site ready.\n";
