<?php

require __DIR__ . '/builder-contract.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/icons.php';

$linked = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'links-row', 'items' => array(
	array( 'id' => 'contact-item', 'type' => 'contact', 'text' => 'Phone', 'url' => 'tel:+9821000000', 'icon' => 'phone', 'direction' => 'ltr' ),
	array( 'id' => 'icon-item', 'type' => 'icon', 'text' => 'Location', 'url' => '/contact/', 'icon' => 'map-pin' ),
	array( 'id' => 'social-item', 'type' => 'social', 'links' => array( array( 'id' => 'profile-one', 'text' => 'Profile', 'url' => 'https://example.test/', 'icon' => 'external-link' ) ) ),
) ) ) );
$clean = \RadmanCT\Builder\validate( $linked );
expect( null !== $clean, 'Linked element schema rejected.' );
foreach ( array( '#', '#contact', '/contact/' ) as $local_url ) {
	$local = $linked;
	$local['rows'][0]['items'][2]['links'][0]['url'] = $local_url;
	expect( null !== \RadmanCT\Builder\validate( $local ), 'Safe social placeholder or internal link was rejected.' );
}
$contact = $clean['rows'][0]['items'][0];
$icon = $clean['rows'][0]['items'][1];
$social = $clean['rows'][0]['items'][2];
$html = \RadmanCT\Builder\Elements\render( 'header', $contact, 'default', true );
expect( str_contains( $html, 'href="tel:+9821000000"' ) && str_contains( $html, 'dir="ltr"' ) && str_contains( $html, '<svg' ), 'Phone direction, local icon or phone link lost.' );
$html = \RadmanCT\Builder\Elements\render( 'header', $icon, 'default', true );
expect( str_contains( $html, 'aria-label="Location"' ) && str_contains( $html, 'aria-hidden="true"' ), 'Icon-only link lacks accessible label.' );
$html = \RadmanCT\Builder\Elements\render( 'header', $social, 'default', false );
expect( str_contains( $html, '&lt;translated&gt;' ) && ! str_contains( $html, '<translated>' ), 'Nested translated label was not escaped.' );
$missing = $contact;
$missing['icon'] = 'custom-removed';
expect( str_contains( \RadmanCT\Builder\Elements\render( 'header', $missing, 'default', true ), 'Phone' ), 'Removed icon hid contact text.' );
$missing = $icon;
$missing['icon'] = 'custom-removed';
expect( '' === \RadmanCT\Builder\Elements\render( 'header', $missing, 'default', true ), 'Removed icon left an empty link.' );
foreach ( array( 'javascript:alert(1)', '//untrusted.test', 'tel:123' ) as $url ) {
	$bad = $linked;
	$bad['rows'][0]['items'][2]['links'][0]['url'] = $url;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Unsafe social URL accepted.' );
}
foreach ( array( array( 'size', 65 ), array( 'direction', 'invalid' ), array( 'text', '' ) ) as [ $key, $value ] ) {
	$bad = $linked;
	$bad['rows'][0]['items'][0][ $key ] = $value;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Invalid contact property accepted.' );
}
$bad = $linked;
$bad['rows'][0]['items'][2]['links'][0]['id'] = 'contact-item';
expect( null === \RadmanCT\Builder\validate( $bad ), 'Nested identifier collided with another item.' );
$bad = $linked;
$bad['rows'][0]['items'][2]['links'] = array_fill( 0, 9, $social['links'][0] );
expect( null === \RadmanCT\Builder\validate( $bad ), 'Social link limit bypassed.' );
$empty = $social;
$empty['links'][0]['url'] = '';
expect( '' === \RadmanCT\Builder\Elements\render( 'header', $empty, 'default', true ), 'Empty profile placeholder rendered.' );
$registered = array();
\RadmanCT\Builder\Storage\register_strings( 'header', 'default', $clean );
expect( in_array( array( 'wpml_register_single_string', array( 'Radman Builder', 'header/social-item/link-profile-one-text', 'Profile' ) ), $registered, true ), 'Stable nested WPML string key missing.' );
echo "Passed: linked builder element validation, nested translation identities, accessible local SVG and direction, safe URL limits and missing-icon fallback.\n";
