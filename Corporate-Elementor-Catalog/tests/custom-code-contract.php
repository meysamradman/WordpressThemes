<?php

require __DIR__ . '/settings-contract.php';
use RadmanCT\CustomCode;

$calls = array();
function is_admin() { return false; }
function is_feed() { return false; }
function wp_register_style( ...$args ) { $GLOBALS['calls']['style'] = $args; }
function wp_enqueue_style( ...$args ) {}
function wp_add_inline_style( ...$args ) { $GLOBALS['calls']['css'] = $args; }
function wp_register_script( ...$args ) { $GLOBALS['calls']['script'] = $args; }
function wp_enqueue_script( ...$args ) {}
function wp_add_inline_script( ...$args ) { $GLOBALS['calls']['js'] = $args; }

$saved = \RadmanCT\Settings\defaults();
CustomCode\enqueue(); expect( ! $calls, 'Empty custom code registered frontend assets.' );
$saved['custom_css'] = '.existing{color:red}';
$settings_caps = array( 'edit_theme_options' => true );
$result = \RadmanCT\Settings\sanitize( array( 'custom_css' => '.replaced{}' ) );
expect( '.existing{color:red}' === $result['custom_css'], 'Missing unfiltered_html permission replaced saved code.' );
$settings_caps['unfiltered_html'] = true;
$code = "document.body.dataset.ready = 'yes';\n";
$result = \RadmanCT\Settings\sanitize( array( 'custom_js' => $code ) );
expect( $code === $result['custom_js'], 'Authorized code lost quotes or newlines.' );
foreach ( array( '</ScRiPt><img src=x>', '</style >', '<?php echo 1;', str_repeat( 'x', 65537 ), array() ) as $bad ) {
	expect( ! CustomCode\valid( $bad ), 'Code accepted tag breakout, PHP, unbounded or non-string input.' );
}
$saved['custom_css_mobile'] = '.mobile{display:block}';
$saved['custom_js'] = $code;
CustomCode\enqueue();
expect( false === $calls['style'][1] && str_contains( $calls['css'][1], '@media (max-width:767.98px)' ), 'Custom CSS lost its scoped media or created an external request.' );
expect( false === $calls['script'][1] && true === $calls['script'][4] && str_contains( $calls['js'][1], $code ) && str_contains( $calls['js'][1], 'DOMContentLoaded' ), 'Custom JavaScript lost footer/DOM-ready execution or created a script URL.' );
$result = \RadmanCT\Settings\sanitize( array( 'custom_js' => '' ) );
expect( '' === $result['custom_js'], 'Clearing custom code did not disable it.' );
$settings_caps = array();
echo "Passed: guarded code saves, byte bounds, tag breakout protection, media scopes and empty/inline-only assets.\n";
