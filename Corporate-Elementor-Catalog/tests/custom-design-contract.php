<?php

require __DIR__ . '/renderers-contract.php';

function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_header() { echo '[header]'; }
function get_footer() { echo '[footer]'; }
function add_shortcode( ...$args ) {}
function get_template_part( $slug, $name = null, $args = array() ) {
	if ( in_array( $slug, array( 'template-parts/content/archive', 'template-parts/content/detail' ), true ) ) {
		require get_template_directory() . '/' . $slug . '.php';
	} else {
		echo '[' . $slug . ']';
	}
}

foreach ( array( 'blog' => 'post', 'catalog' => 'radman_product', 'portfolio' => 'radman_portfolio' ) as $scope => $type ) {
	foreach ( array( 'archive', 'single' ) as $view ) {
		$singular = 'single' === $view ? $type : '';
		$archive = 'archive' === $view && 'blog' !== $scope ? $type : '';
		$home = 'archive' === $view && 'blog' === $scope;
		$taxonomy = '';
		$protected = false;
		$matched = true;
		$file = 'blog' === $scope ? ( 'single' === $view ? 'single.php' : 'index.php' ) : $view . '-' . $type . '.php';
		foreach ( array( 'custom', 'elementor' ) as $mode ) {
			$settings = array( $scope . '_' . $view . '_renderer' => $mode );
			$calls = 0;
			ob_start(); require get_template_directory() . '/' . $file; $html = ob_get_clean();
			$content = 'custom' === $mode ? '[template-parts/' . $scope . '/' . $view . ']' : 'Pro-' . $view;
			expect( '[header]' . $content . '[footer]' === $html, 'Incorrect design route: ' . $scope . '/' . $view . '/' . $mode );
			expect( 'custom' !== $mode || 0 === $calls, 'Custom design queried Pro templates.' );
		}
		$matched = false;
		ob_start(); require get_template_directory() . '/' . $file; $html = ob_get_clean();
		expect( '[header][template-parts/' . $scope . '/' . $view . '][footer]' === $html, 'Missing Pro design lost the custom fallback.' );
	}
}

echo "Passed: six independent custom design routes, single header/footer lifecycle, Pro isolation and missing-template fallback.\n";
