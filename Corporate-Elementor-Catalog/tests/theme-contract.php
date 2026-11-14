<?php

define( 'ABSPATH', '/usr/src/wordpress/' );
$workspace = dirname( __DIR__ );
$styles = array();
$scripts = array();
$font_choices = null;
$preload_groups = array();
$page_template = '';
$page = true;
$blog = false;
$single_post = false;

function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_stylesheet_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function get_stylesheet_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value ) { if ( 'radmanct/typography/preload' === $hook ) { return $GLOBALS['preload_groups']; } return $hook === 'radmanct/typography/fonts' ? ( $GLOBALS['font_choices'] ?? $value ) : $value; }
function esc_url_raw( $value ) { return $value; }
function get_option( $key, $default = false ) { return $default; }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function is_page() { return $GLOBALS['page']; }
function get_page_template_slug() { return $GLOBALS['page_template']; }
function is_page_template( $template ) { return $GLOBALS['page_template'] === $template; }
function is_home() { return $GLOBALS['blog']; }
function is_archive() { return false; }
function is_post_type_archive( $type ) { return false; }
function is_tax( $type = null ) { return false; }
function is_search() { return false; }
function is_singular( $type ) { return $type === 'post' && $GLOBALS['single_post']; }
function wp_enqueue_style( $handle, $url, $dependencies, $version ) { $GLOBALS['styles'][ $handle ] = array( $url, $dependencies ); }
function wp_enqueue_script_module( $handle, $url, $dependencies, $version ) { $GLOBALS['scripts'][ $handle ] = $url; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require $workspace . '/radmanct/inc/assets.php';
require $workspace . '/radmanct/inc/settings.php';
require $workspace . '/radmanct/inc/typography.php';

\RadmanCT\Assets\enqueue();
expect( isset( $styles['rdct-theme'] ) && count( $styles ) === 1, 'The shell must not enqueue customer page designs.' );
expect( count( $scripts ) === 1, 'Unexpected initial JavaScript modules.' );
expect( \RadmanCT\Assets\entry( 'missing.css' ) === array(), 'Missing asset did not fail closed.' );

$css = \RadmanCT\Typography\css();
expect( str_contains( $css, 'IRANSansXV.woff2' ) && ! str_contains( $css, 'PeydaWebVF.woff2' ), 'Unselected font declared.' );
expect( ! preg_match( '~https?://(?!radman\.test/)~', $css ), 'External font resource.' );
$font_choices = array( 'persian' => 'peyda', 'latin' => array() );
$selected = \RadmanCT\Typography\selected();
expect( $selected['persian'] === 'peyda' && $selected['latin'] === 'gt-walsheim', 'Font selection fallback failed.' );
foreach ( \RadmanCT\Typography\registry() as $key => $font ) {
	foreach ( $font['files'] as $file ) {
		expect( is_readable( get_template_directory() . '/fonts/' . $file[0] ), 'Registered font is missing: ' . $file[0] );
	}
	if ( 'persian' === $font['group'] ) {
		$font_choices = array( 'persian' => $key, 'latin' => 'gt-walsheim' );
		$font_css = \RadmanCT\Typography\css();
		expect( str_contains( $font_css, 'font-family:"' . $font['family'] . '"' ), 'Selected family missing.' );
		foreach ( \RadmanCT\Typography\registry() as $other_key => $other ) {
			if ( 'persian' === $other['group'] && $other_key !== $key ) {
				expect( ! str_contains( $font_css, 'font-family:"' . $other['family'] . '"' ), 'Unselected Persian family declared.' );
			}
		}
	}
}
$font_choices = array( 'persian' => 'pinar', 'latin' => 'gt-walsheim' );
$pinar_css = \RadmanCT\Typography\css();
expect( str_contains( $pinar_css, 'Pinar-VF%5BDSTY%2CKSHD%2Cwght%5D.woff2' ) && str_contains( $pinar_css, 'font-weight:300 900' ), 'Variable font path encoding or weight range failed.' );
$font_choices = array( 'persian' => 'gt-walsheim', 'latin' => 'pinar' );
expect( \RadmanCT\Typography\selected() === array( 'persian' => 'iransansx', 'latin' => 'gt-walsheim' ), 'Cross-group font selection was accepted.' );
$resources = array( array( 'href' => 'https://radman.test/critical.css', 'as' => 'style' ) );
expect( $resources === \RadmanCT\Typography\preloads( $resources ), 'Fonts were preloaded without explicit critical-use declaration.' );
$preload_groups = array( 'persian', 'persian', 'latin', 'unknown' );
$font_choices = array( 'persian' => 'pinar', 'latin' => 'gt-walsheim' );
$preloads = \RadmanCT\Typography\preloads( $resources );
expect( count( $preloads ) === 3 && $preloads[0] === $resources[0], 'Critical font preload duplicated groups or lost unrelated resources.' );
expect( str_contains( $preloads[1]['href'], 'Pinar-VF%5B' ) && str_contains( $preloads[2]['href'], 'GT-Walsheim-Regular.woff2' ) && 'anonymous' === $preloads[1]['crossorigin'], 'Critical font selection or CORS mismatch.' );

require ABSPATH . 'wp-includes/compat.php';
require ABSPATH . 'wp-includes/pomo/mo.php';
$translations = new MO();
expect( $translations->import_from_file( $workspace . '/radmanct/languages/fa_IR.mo' ), 'Persian catalog did not load.' );
expect( $translations->translate( 'Menu' ) !== 'Menu', 'Persian interface translation missing.' );

echo "Passed: shell assets, font fallback and WordPress MO reader.\n";
