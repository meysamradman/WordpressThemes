<?php

namespace Elementor {
	class Plugin { public static $instance; }
}

namespace {
	require __DIR__ . '/carousel-contract.php';
	function esc_attr__( $value, $domain ) { return esc_attr( $value ); }

	use RadmanCTComponents\Components\Carousel;
	use RadmanCTComponents\Components\Presentation;

	$items = array( array( 'title' => 'One', 'width_tablet' => 240 ), array( 'title' => 'Two' ) );
	$plain = Carousel\render( $items );
	expect( ! str_contains( $plain, 'rdct-crs-r-' ), 'Default layout added redundant custom CSS.' );
	expect( str_contains( $plain, '&quot;tablet&quot;:768,&quot;desktop&quot;:1024' ), 'Independent PHP device defaults changed.' );
	$values = array( 'tablet' => 1200, 'mobile' => 900, 'laptop' => 1600 );
	\Elementor\Plugin::$instance = (object) array( 'breakpoints' => new class( $values ) {
		public array $values;
		public function __construct( array $values ) { $this->values = $values; }
		public function get_active_breakpoints(): array {
			return array_map( static fn( $value ) => new class( $value ) {
				private $value;
				public function __construct( $value ) { $this->value = $value; }
				public function get_value() { return $this->value; }
			}, $this->values );
		}
	} );
	foreach ( array( 'carousel', 'slider', 'marquee' ) as $mode ) {
		$html = Carousel\render( $items, array( 'mode' => $mode, 'variable_width' => true, 'height_tablet' => array( 'size' => 250, 'unit' => 'px' ) ) );
		expect( str_contains( $html, '&quot;tablet&quot;:901,&quot;desktop&quot;:1201' ), 'JS min-width edges differ from native max-width edges.' );
		expect( str_contains( $html, '@media(max-width:1200px)' ) && str_contains( $html, '@media(max-width:900px)' ), 'SSR responsive layout did not follow native breakpoints.' );
		expect( str_contains( $html, 'var(--rdct-carousel-height-tablet,' ), 'Responsive height inheritance was lost.' );
		if ( 'carousel' === $mode ) { expect( str_contains( $html, '--rdct-crs-w:var(--rdct-crs-w-m,' ), 'Variable slide width uses a different device boundary.' ); }
	}
	expect( array( 'tablet' => 1200, 'mobile' => 900 ) === Presentation\breakpoints(), 'Unknown native devices escaped the three-device contract.' );
	\Elementor\Plugin::$instance->breakpoints->values = array( 'tablet' => 600, 'mobile' => 900 );
	expect( array( 'tablet' => 1024, 'mobile' => 767 ) === Presentation\breakpoints(), 'Inverted native boundaries should use safe defaults.' );
	\Elementor\Plugin::$instance->breakpoints->values = array( 'tablet' => INF, 'mobile' => 'bad' );
	expect( array( 'tablet' => 1024, 'mobile' => 767 ) === Presentation\breakpoints(), 'Unbounded or malformed native boundaries escaped validation.' );
	echo "Passed: independent defaults, native device edge conversion, SSR CSS/JS/height/variable width and bounded fallback.\n";
}
