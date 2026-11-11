<?php

require __DIR__ . '/builder-contract.php';
require __DIR__ . '/../radmanct/inc/template-tags.php';
function has_custom_logo() { return true; }
function the_custom_logo() { echo '<a class="custom-logo-link"><img class="custom-logo" width="440" height="160" alt="Site" src="/logo.png"></a>'; }
function wp_attachment_is_image( $id ) { return in_array( $id, array( 11, 12 ), true ); }
function wp_get_attachment_image_src( $id, $size ) { return array( '/image-' . $id . '.svg', 440, 160 ); }
function wp_get_attachment_image_srcset( $id, $size ) { return false; }
function wp_get_attachment_image( $id, $size, $icon, $attrs ) { return '<img class="custom-logo" src="/image-' . $id . '.svg" loading="' . $attrs['loading'] . '" width="440" height="160">'; }
function home_url( $path ) { return '/'; }
function get_bloginfo( $field ) { return 'Site'; }

$visual = $layout;
$visual['rows'][0]['items'] = array(
	array( 'id' => 'visual-logo', 'type' => 'logo', 'logoWidth' => 220, 'laptopLogoWidth' => 200, 'laptopLogoHeight' => 72, 'tabletLogoWidth' => 170, 'tabletLogoHeight' => 60, 'mobileLogoWidth' => 140, 'logoHeight' => 80, 'mobileLogoHeight' => 48 ),
	array( 'id' => 'visual-button', 'type' => 'button', 'text' => 'Contact', 'url' => '/contact/', 'buttonStyle' => 'outline', 'buttonBackground' => '#AABBCC', 'buttonRadius' => 0, 'buttonPaddingX' => 0, 'buttonPaddingY' => 12, 'newTab' => true ),
);
$clean = \RadmanCT\Builder\validate( $visual );
expect( null !== $clean && '#aabbcc' === $clean['rows'][0]['items'][1]['buttonBackground'], 'Visual settings did not normalize.' );
function render_visual( array $layout, bool $preview ): string {
	ob_start();
	\RadmanCT\Builder\render_layout( 'header', $layout, 'default', $preview );
	return ob_get_clean();
}
$html = render_visual( $clean, true );
$empty_button = $clean;
$empty_button['rows'][0]['items'][1]['url'] = '';
$empty_html = render_visual( $empty_button, true );
expect( str_contains( $empty_html, 'aria-disabled="true"' ) && ! str_contains( $empty_html, 'href=""' ) && ! str_contains( $empty_html, 'target="_blank"' ), 'Empty button caused a reload link or opened a blank tab.' );
$empty_button['rows'][0]['items'][1]['text'] = ' ';
expect( ! str_contains( render_visual( $empty_button, true ), 'rdct-bld__button' ), 'Unlabelled button rendered an interactive control.' );
expect( str_contains( render_visual( $empty_button, true ), 'rdct-bld__placeholder' ), 'Empty button disappeared from the editable canvas.' );
expect( ! str_contains( render_visual( $empty_button, false ), 'rdct-bld__placeholder' ), 'Editor placeholder leaked to the public header.' );
foreach ( array( '--builder-logo-width:220px;', '--builder-logo-mobile-width:140px;', '--builder-logo-mobile-height:48px;', 'rdct-bld__button--outline', '--builder-button-radius:0px;', '--builder-button-padding-x:0px;', '--builder-button-accent:#aabbcc;', 'rel="noopener noreferrer"' ) as $fragment ) {
	expect( str_contains( $html, $fragment ), 'Missing visual setting in shared PHP preview: ' . $fragment );
}
$public = render_visual( $clean, false );
foreach ( array( '--builder-logo-laptop-width:200px;', '--builder-logo-laptop-height:72px;', '--builder-logo-tablet-width:170px;', '--builder-logo-tablet-height:60px;' ) as $fragment ) {
	expect( str_contains( $html, $fragment ) && str_contains( $public, $fragment ), 'Four-device logo dimensions missing from public/preview.' );
}
expect( str_contains( $public, '--builder-logo-mobile-width:140px;' ) && ! str_contains( $public, 'data-builder-preview-item' ), 'Public rendering lost visual settings or leaked editor markers.' );
foreach ( array( 'logoWidth' => 513, 'mobileLogoHeight' => -1, 'logoHeight' => '48', 'mobileLogoWidth' => '0;display:none', 'laptopLogoWidth' => 513, 'tabletLogoHeight' => '60', 'tabletLogoWidth' => -1 ) as $key => $value ) {
	$bad = $visual;
	$bad['rows'][0]['items'][0][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Invalid logo dimensions accepted.' );
}
foreach ( array( 'buttonStyle' => 'script', 'buttonBackground' => '#fff;position:fixed', 'buttonRadius' => 65, 'buttonPaddingX' => false, 'buttonPaddingY' => 49, 'newTab' => 'true' ) as $key => $value ) {
	$bad = $visual;
	$bad['rows'][0]['items'][1][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Invalid button visual setting accepted.' );
}
$old = $visual;
$old['rows'][0]['items'] = array( array( 'id' => 'old-logo', 'type' => 'logo' ), array( 'id' => 'old-button', 'type' => 'button', 'text' => 'Contact', 'url' => '/contact/' ) );
$old = \RadmanCT\Builder\validate( $old );
expect( 0 === $old['rows'][0]['items'][0]['mobileLogoWidth'] && 'solid' === $old['rows'][0]['items'][1]['buttonStyle'] && false === $old['rows'][0]['items'][1]['newTab'], 'Existing layout defaults changed unexpectedly.' );
expect( ! str_contains( render_visual( $old, true ), 'target="_blank"' ), 'Existing links began opening new tabs.' );
echo "Passed: responsive branding, bounded button styles, safe new-tab links and legacy layout defaults.\n";
$media_layout = $visual;
$media_layout['rows'][0]['items'] = array( array( 'id' => 'logo-picture', 'type' => 'logo', 'logoImageId' => 11, 'mobileLogoImageId' => 12 ), array( 'id' => 'footer-picture', 'type' => 'image', 'imageId' => 12, 'imageWidth' => 180 ) );
$media_clean = \RadmanCT\Builder\validate( $media_layout );
expect( null !== $media_clean && 12 === $media_clean['rows'][0]['items'][1]['imageId'], 'Standalone image schema failed.' );
$pictures = render_visual( $media_clean, true );
expect( 1 === substr_count( $pictures, '<picture>' ) && str_contains( $pictures, 'media="(max-width: 767px)"' ) && str_contains( $pictures, 'image-12.svg' ), 'Responsive source missing or an unnecessary single-image picture was emitted.' );
expect( 4 === substr_count( $pictures, '<div ' ), 'Logo/image must reuse the item wrapper, leaving only one row, one layout and two items.' );
expect( preg_match( '/<div class="[^"]*rdct-bld__item [^"]*rdct-bld__logo[^"]*"[^>]*><a class="custom-logo-link"/', $pictures ), 'Logo layout and visual attributes were not merged on the same wrapper.' );
expect( 1 === substr_count( $pictures, 'rel="home"' ) && str_contains( $pictures, 'rdct-bld__image' ), 'Standalone image gained a home link.' );
$media_layout['rows'][0]['items'][0]['logoImageId'] = '11';
expect( null === \RadmanCT\Builder\validate( $media_layout ), 'Logo media ID did not retain strict typing.' );
$media_layout['rows'][0]['items'][0] = array( 'id' => 'missing-picture', 'type' => 'logo', 'logoImageId' => 999 );
expect( str_contains( render_visual( \RadmanCT\Builder\validate( $media_layout ), true ), '/logo.png' ), 'Missing media did not fall back to the site logo.' );
echo "Passed: per-element image selection, responsive picture sources, standalone images and missing-media fallback.\n";
