<?php

require __DIR__ . '/settings-contract.php';
function get_template_directory_uri() { return 'https://local.test/wp-content/themes/radmanct'; }
function esc_url_raw( $url ) { return $url; }
function get_locale() { return $GLOBALS['font_locale'] ?? 'fa_IR'; }
require __DIR__ . '/../radmanct/inc/typography.php';
require __DIR__ . '/../radmanct/inc/integrations/elementor.php';

$saved = array_replace( \RadmanCT\Settings\defaults(), array( 'persian_font' => 'peyda', 'mixed_language_fonts' => true ) );
$roles = \RadmanCT\Typography\selections();
expect( 'peyda' === $roles['headings']['persian'] && 'peyda' === $roles['h6']['persian'], 'Legacy typography lost body inheritance.' );
$css = \RadmanCT\Typography\css();
foreach ( array( 'heading-fonts.css' ) as $stylesheet ) {
	$source = file_get_contents( __DIR__ . '/../radmanct/src/css/' . $stylesheet );
	expect( ! str_contains( $source, 'editor-styles-wrapper' ), 'Site typography must not style Gutenberg.' );
}
expect( 4 === substr_count( $css, '@font-face' ) && ! str_contains( $css, 'IRANSansXV' ), 'Inherited headings introduced unused font declarations.' );

$saved['headings_persian_font'] = 'pinar';
$saved['h1_persian_font'] = 'iranyekan';
$roles = \RadmanCT\Typography\selections();
expect( 'iranyekan' === $roles['h1']['persian'] && 'pinar' === $roles['h2']['persian'] && 'peyda' === $roles['body']['persian'], 'Independent heading/body selections failed.' );
$css = \RadmanCT\Typography\css();
expect( 1 === substr_count( $css, 'PeydaWebVF.woff2' ) && 1 === substr_count( $css, 'Pinar-VF%5BDSTY%2CKSHD%2Cwght%5D.woff2' ), 'Shared families were duplicated or local URLs changed.' );
expect( str_contains( $css, '--rdct-font-h1-fa:"Radman CT IRANYekan"' ) && str_contains( $css, '--rdct-font-h2-fa:"Radman CT Pinar"' ) && ! str_contains( $css, 'Pesteh-' ), 'Tag tokens or unused-family isolation failed.' );
$elementor_fonts = \RadmanCT\Integrations\Elementor\fonts( array( 'Remote font' => 'googlefonts' ) );
expect( isset( $elementor_fonts['Radman CT Peyda'], $elementor_fonts['Radman CT Pinar'], $elementor_fonts['Radman CT IRANYekan'] ) && ! isset( $elementor_fonts['Remote font'] ), 'Elementor local families diverged from resolved typography roles.' );
$resources = array( array( 'href' => '/existing-resource' ) );
expect( $resources === \RadmanCT\Typography\preloads( $resources ), 'Choosing extra fonts created automatic preloads.' );

$invalid = \RadmanCT\Settings\sanitize( array( 'h1_persian_font' => 'gt-walsheim', 'h2_latin_font' => 'peyda', 'headings_persian_font' => array() ) );
expect( 'iranyekan' === $invalid['h1_persian_font'] && '' === $invalid['h2_latin_font'] && 'pinar' === $invalid['headings_persian_font'], 'Invalid heading selection overwrote a valid value.' );
$reset = \RadmanCT\Settings\sanitize( array( 'h1_persian_font' => '' ) );
expect( '' === $reset['h1_persian_font'], 'Explicit inheritance could not be restored.' );
foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $tag ) { $saved[ $tag . '_persian_font' ] = 'peyda'; }
$css = \RadmanCT\Typography\css();
expect( ! str_contains( $css, 'Pinar-VF' ) && ! in_array( 'pinar', \RadmanCT\Typography\used(), true ), 'Completely overridden heading family was still declared.' );
echo "Passed: body/heading/tag inheritance, local deduplicated faces, unused-family isolation, invalid-group preservation and no automatic preload.\n";

$font_locale = 'en_US';
$saved = array_replace( \RadmanCT\Settings\defaults(), array( 'latin_font' => 'manrope', 'headings_latin_font' => 'manrope' ) );
expect( 'manrope' === \RadmanCT\Settings\sanitize( array( 'latin_font' => 'manrope' ) )['latin_font'], 'Local Manrope selection was rejected.' );
$css = \RadmanCT\Typography\css();
expect( 1 === substr_count( $css, 'english/Manrope/Manrope.woff2' ) && str_contains( $css, 'font-weight:200 800' ) && ! str_contains( $css, 'GT-Walsheim' ), 'Manrope axes, deduplication or unused-family isolation failed.' );
expect( isset( \RadmanCT\Integrations\Elementor\fonts( array() )['Radman CT Manrope'] ), 'Selected Manrope was missing from Elementor families.' );
expect( ! \RadmanCT\Fonts\valid( 'manrope', 'persian' ), 'Latin font entered the Persian group.' );
echo "Passed: local variable Manrope selection, weight range, deduplicated output and Elementor family.\n";

$font_locale = 'fa_IR'; $saved = \RadmanCT\Settings\defaults();
$css = \RadmanCT\Typography\css();
expect( ! str_contains( $css, 'english/' ) && ! str_contains( $css, '--rdct-font-latin:' ) && 1 === substr_count( $css, '@font-face' ), 'Single-language Persian page includes inactive Latin fonts or declarations.' );
$font_language_context = 'en';
$css = \RadmanCT\Typography\css();
expect( ! str_contains( $css, 'persian/' ) && ! str_contains( $css, '--rdct-font-fa:' ) && str_contains( $css, 'english/' ), 'Translated English page includes the Persian font group.' );
$font_language_context = 'fa';
expect( ! str_contains( \RadmanCT\Typography\css(), 'english/' ), 'Language switching reused the previous language group.' );
$saved['mixed_language_fonts'] = true;
$css = \RadmanCT\Typography\css();
expect( str_contains( $css, 'persian/' ) && str_contains( $css, 'english/' ), 'Explicit mixed-language setting did not load both groups.' );
unset( $font_language_context );
echo "Passed: per-page language isolation, WPML language switching and explicit mixed-language opt-in.\n";

$font_language_context = 'fa';
$saved = array_replace( \RadmanCT\Settings\defaults(), array( 'persian_font' => 'dana', 'headings_persian_font' => 'aria', 'body_persian_weight' => '400', 'headings_persian_weight' => '700', 'h1_persian_weight' => '900', 'h2_persian_font' => 'hafez' ) );
$css = \RadmanCT\Typography\css();
expect( str_contains( $css, '--rdct-weight-body:400;' ) && str_contains( $css, '--rdct-weight-h1:900;' ) && str_contains( $css, '--rdct-weight-h3:700;' ), 'Explicit or inherited role weights were lost.' );
expect( str_contains( $css, '--rdct-weight-h2:400;' ) && ! str_contains( $css, 'PeydaWebVF' ) && ! str_contains( $css, 'AriaWebFaNum-Normal' ) && ! str_contains( $css, 'english/' ), 'Single-weight fallback or unused-font isolation failed.' );
expect( '700' === \RadmanCT\Settings\sanitize( array( 'body_persian_weight' => '700' ) )['body_persian_weight'], 'Available static bold was rejected.' );
expect( '400' === \RadmanCT\Settings\sanitize( array( 'body_persian_weight' => '400;display:none' ) )['body_persian_weight'], 'Invalid weight replaced the saved value.' );
expect( '' === \RadmanCT\Settings\sanitize( array( 'h2_persian_weight' => '700' ) )['h2_persian_weight'], 'Unavailable static weight was accepted.' );
expect( '' === \RadmanCT\Settings\sanitize( array( 'h1_persian_weight' => '' ) )['h1_persian_weight'], 'Weight inheritance could not be restored.' );
expect( in_array( '1000', \RadmanCT\Fonts\weights( 'peyda' ), true ) && array( '400' ) === \RadmanCT\Fonts\weights( 'hafez' ), 'Real variable/static weight options diverged from the registry.' );
foreach ( \RadmanCT\Fonts\registry() as $font ) {
	foreach ( $font['files'] as $file ) {
		$directory = __DIR__ . '/../radmanct/fonts';
		foreach ( explode( '/', $file[0] ) as $segment ) {
			expect( in_array( $segment, scandir( $directory ), true ), 'Font path casing does not match disk: ' . $file[0] );
			$directory .= '/' . $segment;
		}
	}
}
echo "Passed: actual font paths/casing, new families, supported weight selection, inheritance and unused family isolation.\n";

$saved['body_persian_line_height'] = '1.8';
$saved['headings_persian_line_height'] = '1.4';
$saved['h1_persian_line_height'] = '1.25';
$css = \RadmanCT\Typography\css();
expect( str_contains( $css, '--rdct-line-height-body:1.8;' ) && str_contains( $css, '--rdct-line-height-h1:1.25;' ) && str_contains( $css, '--rdct-line-height-h2:1.4;' ), 'Line-height role inheritance failed.' );
expect( str_contains( $css, '.rdct-site h1' ) && ! str_contains( $css, 'editor-styles-wrapper' ), 'Dynamic heading metrics must target the site only.' );
expect( '2.05' === \RadmanCT\Settings\sanitize( array( 'body_persian_line_height' => '2.050' ) )['body_persian_line_height'], 'Line height did not normalize.' );
foreach ( array( '1px', '0', '5', array(), '1;display:none' ) as $invalid_line ) {
	expect( '1.8' === \RadmanCT\Settings\sanitize( array( 'body_persian_line_height' => $invalid_line ) )['body_persian_line_height'], 'Invalid line height replaced its saved value.' );
}
expect( '' === \RadmanCT\Settings\sanitize( array( 'h1_persian_line_height' => '' ) )['h1_persian_line_height'], 'Line height could not return to inheritance.' );
echo "Passed: line-height inheritance, decimal normalization, empty reset and invalid input preservation.\n";

$saved['h1_persian_size'] = '64';
$saved['h1_persian_mobile_size'] = '32';
$saved['headings_persian_size'] = '40';
$saved['body_persian_size'] = '18';
expect( '64' === \RadmanCT\Typography\role_size( $saved, 'h1', 'persian', '_tablet' ) && '32' === \RadmanCT\Typography\role_size( $saved, 'h1', 'persian', '_mobile' ), 'Responsive size inheritance failed.' );
expect( '40' === \RadmanCT\Typography\role_size( $saved, 'h2', 'persian' ), 'Shared heading size failed.' );
$size_css = \RadmanCT\Typography\size_css( $saved, array( 'persian' ) );
expect( str_contains( $size_css, ':where(h1)' ) && str_contains( $size_css, '--rdct-size-h1:4rem' ) && str_contains( $size_css, 'font-size:var(--rdct-size-h1)' ) && ! str_contains( $size_css, 'font-size:4rem' ) && str_contains( $size_css, '@media(max-width:767px)' ) && ! str_contains( $size_css, '@media(max-width:1024px)' ), 'Scoped responsive size CSS is incorrect or redundant.' );
expect( '20.5' === \RadmanCT\Settings\sanitize( array( 'body_persian_size' => '20.50' ) )['body_persian_size'], 'Font size did not normalize.' );
foreach ( array( '0', '201', '16px', array(), '16;display:none' ) as $invalid_size ) {
	expect( '18' === \RadmanCT\Settings\sanitize( array( 'body_persian_size' => $invalid_size ) )['body_persian_size'], 'Invalid size replaced the saved value.' );
}
expect( '' === \RadmanCT\Settings\sanitize( array( 'h1_persian_size' => '' ) )['h1_persian_size'], 'Size could not return to defaults.' );
echo "Passed: responsive font size fallback, scoped rem output, numeric validation and reset.\n";
