<?php

define( 'ABSPATH', '/' );
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/safe-attributes.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/safe-attributes.php';
spl_autoload_register( static function ( $class ): void {
	$prefix = 'ElementorDeps\\Twig\\';
	if ( ! str_starts_with( $class, $prefix ) ) { return; }
	$path = dirname( __DIR__ ) . '/themes/elementor/vendor_prefixed/twig/twig/twig/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
	if ( is_file( $path ) ) { require $path; }
} );
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$root = dirname( __DIR__ );
$macro = file_get_contents( $root . '/radmanct/inc/integrations/elementor/safe-attributes.html.twig' );
$twig = new \ElementorDeps\Twig\Environment( new \ElementorDeps\Twig\Loader\ArrayLoader( array(
	'radmanct/attributes' => $macro,
	'elementor/macros' => file_get_contents( $root . '/themes/elementor/modules/atomic-widgets/elements/base/_macros.html.twig' ),
	'test' => "{% import 'radmanct/attributes' as attributes %}<div {{ attributes.render(settings) }}>Content</div>",
) ), array( 'autoescape' => 'html' ) );
$cases = json_decode( file_get_contents( __DIR__ . '/fixtures/atomic-attributes.json' ), true );
foreach ( $cases as $type => $values ) {
	foreach ( $values as $value ) {
		$expected = 'valid' === $type ? $value : '';
		expect( $expected === \RadmanCTShared\Elementor\Attributes\sanitize( $value ), 'PHP attribute boundary failed: ' . $type );
		$html = $twig->render( 'test', array( 'settings' => array( 'attributes' => $value, '_cssid' => 'native-id' ) ) );
		expect( str_contains( $html, 'id="native-id"' ), 'Native CSS ID was lost.' );
		if ( $value && 'valid' === $type ) { expect( str_contains( $html, $value ), 'Twig rejected an allowed attribute string.' ); }
		if ( 'invalid' === $type ) { expect( ! str_contains( $html, $value ) && ! str_contains( $html, 'onclick=' ) && ! str_contains( $html, 'onmouseover=' ), 'Twig emitted unsafe attributes.' ); }
	}
}
$large = 'data-value="' . str_repeat( 'a', 16384 ) . '"';
expect( '' === \RadmanCTShared\Elementor\Attributes\sanitize( $large ), 'Unbounded attribute payload accepted.' );
expect( ! str_contains( $twig->render( 'test', array( 'settings' => array( 'attributes' => $large ) ) ), 'data-value=' ), 'Twig accepted an unbounded payload.' );
foreach ( array( null, false, 42, array() ) as $value ) { expect( '' === \RadmanCTShared\Elementor\Attributes\sanitize( $value ), 'Non-string attributes accepted.' ); }
$templates = array_merge( glob( $root . '/radmanct-components/inc/integrations/elementor/atomic-components/*.twig' ), glob( $root . '/radmanct/inc/integrations/elementor/navigation/*.twig' ) );
foreach ( $templates as $path ) {
	$content = file_get_contents( $path );
	expect( str_contains( $content, "import 'radmanct/attributes'" ) && ! str_contains( $content, 'm.render_custom_attributes(settings)' ), 'A Radman preview bypasses the shared boundary.' );
}
echo "Passed: PHP and real supplied Twig/native macro agree on allowed, executable, injected and bounded attributes; 11 preview templates share the boundary.\n";
