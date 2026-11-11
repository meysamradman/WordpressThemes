<?php
define( 'ABSPATH', '/' );
$order = $argv[1] ?? 'theme-first';
$theme = dirname( __DIR__ ) . '/radmanct/inc/template-functions.php';
$plugin = dirname( __DIR__ ) . '/radmanct-components/inc/template-functions.php';
if ( 'theme-first' === $order ) { require $theme; require $plugin; }
else { require $plugin; require $theme; }
foreach ( array( 'rdct_use', 'rdct_icon', 'rdct_text_reveal', 'rdct_curtain', 'rdct_box', 'rdct_flipbox', 'rdct_icon_box', 'rdct_image', 'rdct_table' ) as $name ) {
	if ( ! function_exists( $name ) ) { throw new RuntimeException( 'Missing component helper: ' . $name ); }
}
echo 'Component helpers loaded without redeclaration: ' . $order . ".\n";
