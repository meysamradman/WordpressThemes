<?php

require getenv( 'RDCT_WP_ROOT' ) . '/wp-load.php';

$expect = static function ( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } };
$original_query = $GLOBALS['wp_query'];
$original_main = $GLOBALS['wp_the_query'];
$original_post = $GLOBALS['post'] ?? null;
$base = \RadmanCT\Settings\values();
$settings = $base;
foreach ( \RadmanCT\Typography\Profiles\scopes() as $scope ) { $settings[ $scope . '_archive_renderer' ] = $settings[ $scope . '_single_renderer' ] = 'custom'; }
$filter = static function () use ( &$settings ): array { return $settings; };
add_filter( 'pre_option_radman_settings', $filter );
$font = null;
foreach ( array_diff( array_keys( \RadmanCT\Typography\registry() ), \RadmanCT\Typography\used() ) as $candidate ) {
	if ( \RadmanCT\Fonts\valid( $candidate, 'persian' ) ) { $font = $candidate; break; }
}
$expect( null !== $font, 'A second bundled Persian font is required for this focused check.' );
$cases = array(
	'empty' => array(),
	'identical' => \RadmanCT\Typography\Profiles\fields( $base ),
	'color' => array( 'h2_color' => '#123abc' ),
	'font' => array( 'h4_persian_font' => $font ),
);

try {
	foreach ( array( 'post' => 'blog', 'radman_product' => 'catalog', 'radman_portfolio' => 'portfolio' ) as $type => $scope ) {
		$posts = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => 1, 'has_password' => false ) );
		$expect( (bool) $posts, 'A published item is required for ' . $scope . '.' );
		foreach ( array( 'archive', 'single' ) as $view ) {
			$query = new WP_Query();
			$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = $query;
			$query->query( array( 'post_type' => $type, 'posts_per_page' => 1 ) + ( 'single' === $view ? array( 'p' => $posts[0]->ID ) : array() ) );
			$expect( array( $scope, $view ) === \RadmanCT\Renderers\context(), 'Incorrect test context.' );
			foreach ( $cases as $name => $raw ) {
				$settings[ $scope . '_typography' ] = $raw;
				wp_dequeue_style( 'rdct-content-typography' );
				wp_deregister_style( 'rdct-content-typography' );
				\RadmanCT\Typography\Profiles\enqueue();
				if ( in_array( $name, array( 'empty', 'identical' ), true ) ) {
					$expect( ! wp_style_is( 'rdct-content-typography', 'registered' ), $scope . '/' . $view . ': redundant CSS for ' . $name . ' profile.' );
					continue;
				}
				$css = implode( '', wp_styles()->get_data( 'rdct-content-typography', 'after' ) ?: array() );
				$expect( str_contains( $css, 'body.rdct-site #main-content' ), 'Content styles escaped their scope.' );
				if ( 'color' === $name ) {
					$expect( 1 === substr_count( $css, '#123abc' ) && ! str_contains( $css, '@font-face' ) && ! str_contains( $css, 'font-family:' ) && ! str_contains( $css, 'font-size:' ), 'A color-only change repeated typography or its value.' );
				} else {
					$expected = \RadmanCT\Typography\font_faces( array( $font ) );
					$handle = 'rdct-font-' . sanitize_key( $font );
					$faces = implode( '', wp_styles()->get_data( $handle, 'after' ) ?: array() );
					$expect( $faces === $expected && ! str_contains( $css, '@font-face' ), 'A font change repeated an existing font face.' );
					\RadmanCT\Typography\enqueue_font_faces( array( $font, $font ) );
					$expect( $faces === implode( '', wp_styles()->get_data( $handle, 'after' ) ?: array() ), 'Shared font declarations repeated across consumers.' );
				}
			}
			$settings[ $scope . '_typography' ] = $base[ $scope . '_typography' ];
		}
	}
	echo "Passed: six archive/single views; empty and identical profiles emit no stylesheet, color-only changes emit no font/size, new local font faces appear once. No settings saved.\n";
} finally {
	wp_dequeue_style( 'rdct-content-typography' );
	wp_deregister_style( 'rdct-content-typography' );
	if ( $font ) { wp_dequeue_style( 'rdct-font-' . sanitize_key( $font ) ); wp_deregister_style( 'rdct-font-' . sanitize_key( $font ) ); }
	remove_filter( 'pre_option_radman_settings', $filter );
	$GLOBALS['wp_query'] = $original_query;
	$GLOBALS['wp_the_query'] = $original_main;
	$GLOBALS['post'] = $original_post;
}
