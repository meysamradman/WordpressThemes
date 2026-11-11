<?php
if ( '1' !== getenv( 'RDCT_ISOLATED_QA' ) ) { exit( 1 ); }
$_SERVER['HTTP_HOST'] = 'radman.test';
$_SERVER['SERVER_NAME'] = 'radman.test';
$_SERVER['REQUEST_URI'] = '/wp-admin/plugins.php';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
wp_set_current_user( 1 );
$mode = $argv[1] ?? 'verify';
if ( in_array( $mode, array( 'elementor', 'activate' ), true ) ) {
	$plugin = 'elementor' === $mode ? 'elementor/elementor.php' : 'radmanct-components/radmanct-components.php';
	$result = activate_plugin( $plugin );
	if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
	if ( 'elementor' === $mode ) { update_option( 'elementor_experiment-e_atomic_elements', 'active' ); }
	echo 'Native activation after theme setup passed: ' . $plugin . ".\n";
	exit;
}
if ( ! is_plugin_active( 'radmanct-components/radmanct-components.php' ) ) { throw new RuntimeException( 'Components did not stay active.' ); }
$function = new ReflectionFunction( 'rdct_table' );
if ( ! str_contains( $function->getFileName(), '/plugins/radmanct-components/' ) ) { throw new RuntimeException( 'Fresh request kept theme fallback helpers.' ); }
$classic = \Elementor\Plugin::$instance->widgets_manager->get_widget_types( 'rdct-table' );
if ( ! $classic || ! isset( $classic->get_controls()['data'] ) ) { throw new RuntimeException( 'Table widget/control not registered.' ); }
$control = \Elementor\Plugin::$instance->controls_manager->get_control( 'rdct-table-grid' );
if ( ! $control ) { throw new RuntimeException( 'Matrix editor control missing.' ); }
ob_start(); rdct_table( array( 'columns' => array( 'Feature', 'Value' ), 'rows' => array( array( 'cells' => array( 'Material', 'Steel' ) ) ) ) ); $html = ob_get_clean();
if ( ! str_contains( $html, 'rdct-tb' ) || ! str_contains( $html, 'Steel' ) ) { throw new RuntimeException( 'Activated table did not render.' ); }
echo "Fresh request: Components active, plugin helpers selected, table control registered and PHP render passed.\n";
