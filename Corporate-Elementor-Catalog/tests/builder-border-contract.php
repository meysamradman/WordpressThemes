<?php
defined( 'ABSPATH' ) || exit;
function rdct_border_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$layout = array( 'version' => 1, 'enabled' => false, 'rows' => array() );
rdct_border_expect( '' === \RadmanCT\Builder\Layout\border_style( $layout ), 'Old layouts must not gain a border.' );
$border_layout = $layout;
$border_layout['border'] = array( 'style' => 'solid', 'color' => '#AABBCC', 'top' => 0, 'bottom' => 2, 'start' => 1, 'end' => 0 );
$border_clean = \RadmanCT\Builder\validate( $border_layout );
rdct_border_expect( null !== $border_clean && '#aabbcc' === $border_clean['border']['color'], 'Border must survive schema normalization.' );
foreach ( array( 'header', 'footer' ) as $kind ) {
	ob_start(); \RadmanCT\Builder\render_layout( $kind, $border_clean, 'default', true ); $border_html = ob_get_clean();
	rdct_border_expect( str_contains( $border_html, 'border-block-end:2px solid #aabbcc;' ) && str_contains( $border_html, 'border-inline-start:1px solid #aabbcc;' ), 'Selected borders missing from shared header/footer preview.' );
	rdct_border_expect( ! str_contains( $border_html, 'border-block-start:' ), 'Zero-width side must not emit a border.' );
}
$border_clean['border']['style'] = 'none';
rdct_border_expect( '' === \RadmanCT\Builder\Layout\border_style( $border_clean ), 'Disabling border must retain values without emitting CSS.' );
$border_clean['border']['style'] = 'dotted'; $border_clean['border']['color'] = '';
rdct_border_expect( str_contains( \RadmanCT\Builder\Layout\border_style( $border_clean ), 'currentColor' ), 'Empty border color must inherit.' );
foreach ( array( array( 'style' => 'url(x)' ), array( 'color' => '#fff;display:none' ), array( 'bottom' => -1 ), array( 'bottom' => 21 ), array( 'bottom' => '2' ), array( 'bottom' => 1.5 ), array( 'unknown' => 1 ) ) as $invalid_border ) {
	$border_layout['border'] = $invalid_border;
	rdct_border_expect( null === \RadmanCT\Builder\validate( $border_layout ), 'Unsafe or unbounded border accepted.' );
}
echo "Passed: opt-in builder borders, independent logical sides, inherited color and strict validation.\n";

$border_layout['border'] = array( 'style' => 'solid', 'color' => '#102030', 'bottom' => 2 );
$border_layout['transparent'] = true;
foreach ( array( 'immediate', 'after-first-section' ) as $rdct_border_start ) {
	$border_layout['sticky'] = 'all'; $border_layout['stickyStart'] = $rdct_border_start;
	$rdct_border_valid = \RadmanCT\Builder\validate( $border_layout );
	ob_start(); \RadmanCT\Builder\render_layout( 'header', $rdct_border_valid ); $rdct_border_output = ob_get_clean();
	rdct_border_expect( str_contains( $rdct_border_output, 'border-block-end:2px solid #102030;' ), 'Explicit border must survive transparent/sticky combinations.' );
	rdct_border_expect( str_contains( $rdct_border_output, 'data-rdct-transparent-header' ), 'Transparent behavior lost.' );
}
foreach ( array( 'components/transparent-header.css', 'components/sticky-header.css', 'motion/smooth-scroll.css' ) as $rdct_border_file ) {
	$rdct_border_css = file_get_contents( get_template_directory() . '/src/css/' . $rdct_border_file );
	rdct_border_expect( ! preg_match( '/(?:border(?:-[a-z-]+)?|box-shadow)\s*:/', $rdct_border_css ), 'Header state stylesheet must not override chosen border or add a shadow.' );
}
echo "Passed: explicit border preserved across transparent/sticky variants; state CSS has no automatic decoration.\n";
