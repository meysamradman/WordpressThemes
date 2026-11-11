<?php

namespace RadmanCT\Builder\Logo;

defined( 'ABSPATH' ) || exit;

function keys(): array {
	return array( 'logoImageId', 'laptopLogoImageId', 'tabletLogoImageId', 'mobileLogoImageId' );
}

function image_map(): array {
	$map = array();
	foreach ( array( '', 'laptop', 'tablet', 'mobile' ) as $device ) {
		$logo = $device ? $device . 'Logo' : 'logo';
		$image = $device ? $device . 'Image' : 'image';
		foreach ( array( 'ImageId' => 'Id', 'Width' => 'Width', 'Height' => 'Height' ) as $from => $to ) { $map[ $logo . $from ] = $image . $to; }
	}
	return $map;
}

function image_item( array $item ): array {
	$result = array( 'type' => 'logo' );
	foreach ( image_map() as $logo => $image ) { if ( array_key_exists( $image, $item ) ) { $result[ $logo ] = $item[ $image ]; } }
	return $result;
}

function validate( array $item ): ?array {
	$result = array();
	foreach ( array_merge( keys(), array( 'lightSectionLogoId', 'darkSectionLogoId', 'stickyLogoId' ) ) as $key ) {
		$id = $item[ $key ] ?? 0;
		if ( ! is_int( $id ) || $id < 0 || $id > 2147483647 ) { return null; }
		$result[ $key ] = $id;
	}
	return $result;
}

function attachment( int $id ): ?array {
	if ( $id < 1 ) { return null; }
	$id = (int) apply_filters( 'wpml_object_id', $id, 'attachment', true );
	if ( ! wp_attachment_is_image( $id ) ) { return null; }
	$source = wp_get_attachment_image_src( $id, 'full' );
	if ( ( ! $source || empty( $source[1] ) || empty( $source[2] ) ) && function_exists( 'get_post_mime_type' ) && 'image/svg+xml' === get_post_mime_type( $id ) ) {
		require_once dirname( __DIR__ ) . '/media-svg.php';
		$metadata = \RadmanCT\MediaSvg\metadata( array(), $id );
		if ( ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) { $source = array( wp_get_attachment_url( $id ), $metadata['width'], $metadata['height'] ); }
	}
	return $source ? array( 'id' => $id, 'url' => $source[0], 'width' => $source[1], 'height' => $source[2] ) : null;
}

function render( array $item, string $kind, bool $logo = true ): void {
	if ( ! $logo ) { $item = image_item( $item ); }
	$ids = array_intersect_key( $item, array_flip( keys() ) );
	if ( ! array_filter( $ids ) ) {
		$site_logo = $logo ? (int) get_theme_mod( 'custom_logo', 0 ) : 0;
		if ( $site_logo ) { $item['logoImageId'] = $site_logo; }
		else { if ( $logo ) { \RadmanCT\Templates\brand(); } return; }
	}
	$base = attachment( (int) ( $item['logoImageId'] ?? 0 ) ) ?? ( $logo ? attachment( (int) get_theme_mod( 'custom_logo', 0 ) ) : null );
	$variants = array();
	foreach ( array( 'mobile' => '(max-width: 767px)', 'tablet' => '(min-width: 768px) and (max-width: 1023px)', 'laptop' => '(min-width: 1024px) and (max-width: 1439px)' ) as $device => $media ) {
		$image = attachment( (int) ( $item[ $device . 'LogoImageId' ] ?? 0 ) );
		if ( $image ) { $variants[ $device ] = $image + array( 'media' => $media ); }
	}
	if ( ! $base && $variants ) { $base = reset( $variants ); }
	if ( ! $base ) { if ( $logo ) { \RadmanCT\Templates\brand(); } return; }
	$width = (int) ( $item['logoWidth'] ?? 0 ) ?: 208;
	$sizes = array();
	$sources = '';
	foreach ( array( 'mobile', 'tablet', 'laptop' ) as $device ) {
		$size = (int) ( $item[ $device . 'LogoWidth' ] ?? 0 ) ?: $width;
		$maximum = array( 'mobile' => 767, 'tablet' => 1023, 'laptop' => 1439 )[ $device ];
		$sizes[] = '(max-width: ' . $maximum . 'px) ' . $size . 'px';
		if ( ! isset( $variants[ $device ] ) ) { continue; }
		$image = $variants[ $device ];
		$srcset = wp_get_attachment_image_srcset( $image['id'], 'full' );
		$sources .= '<source media="' . esc_attr( $image['media'] ) . '" srcset="' . ( $srcset ? esc_attr( $srcset ) : esc_url( $image['url'] ) ) . '" sizes="' . $size . 'px" width="' . (int) $image['width'] . '" height="' . (int) $image['height'] . '">';
	}
	$sizes[] = $width . 'px';
	$attributes = array( 'class' => 'custom-logo', 'sizes' => implode( ', ', $sizes ), 'loading' => 'header' === $kind ? 'eager' : 'lazy', 'decoding' => 'async' );
	if ( $logo ) { $attributes['alt'] = get_bloginfo( 'name' ); }
	$image = wp_get_attachment_image( $base['id'], 'full', false, $attributes );
	if ( function_exists( 'get_post_mime_type' ) && 'image/svg+xml' === get_post_mime_type( $base['id'] ) ) {
		$alt = $logo ? get_bloginfo( 'name' ) : get_post_meta( $base['id'], '_wp_attachment_image_alt', true );
		$image = '<img class="custom-logo" src="' . esc_url( $base['url'] ) . '" width="' . (int) $base['width'] . '" height="' . (int) $base['height'] . '" alt="' . esc_attr( $alt ) . '" sizes="' . esc_attr( $attributes['sizes'] ) . '" loading="' . $attributes['loading'] . '" decoding="async">';
	}
	if ( ! $image ) { if ( $logo ) { \RadmanCT\Templates\brand(); } return; }
	$picture = $sources ? '<picture>' . $sources . $image . '</picture>' : $image;
	$state_images = '';
	if ( $logo && 'header' === $kind ) {
		foreach ( array( 'light' => 'lightSectionLogoId', 'dark' => 'darkSectionLogoId', 'sticky' => 'stickyLogoId' ) as $state => $key ) {
			$variant = attachment( (int) ( $item[ $key ] ?? 0 ) );
			if ( $variant ) { $state_images .= '<img data-rdct-logo-state="' . esc_attr( $state ) . '" data-rdct-logo-src="' . esc_url( $variant['url'] ) . '" width="' . (int) $variant['width'] . '" height="' . (int) $variant['height'] . '" alt="" aria-hidden="true" decoding="async" hidden>'; }
		}
		if ( $state_images ) { $picture = preg_replace( '/^<(picture|img)\b/', '<$1 data-rdct-logo-base', $picture, 1 ); }
	}
	echo $logo ? '<a class="custom-logo-link"' . ( $state_images ? ' data-rdct-header-logo' : '' ) . ' href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . $picture . $state_images . '</a>' : '<span class="custom-logo-link">' . $picture . '</span>';
}
