<?php

namespace RadmanCT\Builder\Visuals;

defined( 'ABSPATH' ) || exit;

function validate( array $item ): ?array {
	if ( 'image' === $item['type'] ) {
		require_once __DIR__ . '/logo.php';
		$values = validate( \RadmanCT\Builder\Logo\image_item( $item ) );
		if ( null === $values ) { return null; }
		$result = array();
		foreach ( \RadmanCT\Builder\Logo\image_map() as $logo => $image ) { $result[ $image ] = $values[ $logo ]; }
		return $result;
	}
	$limits = 'logo' === $item['type']
		? array( 'logoWidth' => 512, 'logoHeight' => 256, 'laptopLogoWidth' => 512, 'laptopLogoHeight' => 256, 'tabletLogoWidth' => 512, 'tabletLogoHeight' => 256, 'mobileLogoWidth' => 512, 'mobileLogoHeight' => 256 )
		: array( 'buttonRadius' => 64, 'buttonPaddingX' => 64, 'buttonPaddingY' => 48 );
	$result = array();
	if ( 'logo' === $item['type'] ) {
		require_once __DIR__ . '/logo.php';
		$images = \RadmanCT\Builder\Logo\validate( $item );
		if ( null === $images ) { return null; }
		$result = $images;
	}
	$defaults = array( 'buttonRadius' => null, 'buttonPaddingX' => 19, 'buttonPaddingY' => 10 );
	foreach ( $limits as $key => $maximum ) {
		$value = array_key_exists( $key, $item ) ? $item[ $key ] : ( array_key_exists( $key, $defaults ) ? $defaults[ $key ] : 0 );
		if ( ! ( 'buttonRadius' === $key && null === $value ) && ( ! is_int( $value ) || $value < 0 || $value > $maximum ) ) { return null; }
		$result[ $key ] = $value;
	}
	if ( 'button' === $item['type'] ) {
		if ( array_key_exists( 'buttonOptions', $item ) ) {
			require_once __DIR__ . '/button-component.php';
			$options = \RadmanCT\Builder\ButtonComponent\validate( $item['buttonOptions'] );
			if ( null === $options ) { return null; }
			$result['buttonOptions'] = $options;
		}

		$style = $item['buttonStyle'] ?? 'solid';
		$background = $item['buttonBackground'] ?? '';
		$new_tab = $item['newTab'] ?? false;
		if ( ! in_array( $style, array( 'solid', 'outline', 'plain' ), true ) || ! is_bool( $new_tab ) || ! is_string( $background ) || ( '' !== $background && ! preg_match( '/^#[a-f0-9]{6}$/iD', $background ) ) ) { return null; }
		$result += array( 'buttonStyle' => $style, 'buttonBackground' => strtolower( $background ), 'newTab' => $new_tab );
	}
	return $result;
}

function button( array $item, string $text, string $url ): string {
	if ( '' === trim( $text ) ) { return ''; }
	require_once __DIR__ . '/button-component.php';
	if ( \RadmanCT\Builder\ButtonComponent\available() ) {
		$options = \RadmanCT\Builder\ButtonComponent\options( $item, $text, $url );
		if ( '' === trim( $url ) && 'a' === ( $options['tag'] ?? 'a' ) && in_array( $options['action'] ?? 'link', array( 'link', 'lightbox' ), true ) ) { $options['disabled'] = true; }
		\RadmanCT\Builder\ButtonComponent\assets( $item );
		return \RadmanCTComponents\Components\Button\render( $options );
	}
	$url = esc_url( trim( $url ), array( 'http', 'https', 'mailto', 'tel' ) );
	if ( '' === $url ) { return '<span ' . attributes( $item ) . ' aria-disabled="true">' . esc_html( $text ) . '</span>'; }
	return '<a ' . attributes( $item ) . ' href="' . $url . '"' . ( ! empty( $item['newTab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '' ) . '>' . esc_html( $text ) . '</a>';
}

function attributes( array $item ): string {
	$parts = presentation( $item );
	return 'class="' . esc_attr( $parts['class'] ) . '"' . ( $parts['style'] ? ' style="' . esc_attr( $parts['style'] ) . '"' : '' );
}

function presentation( array $item ): array {
	if ( 'image' === $item['type'] ) {
		require_once __DIR__ . '/logo.php';
		$parts = presentation( \RadmanCT\Builder\Logo\image_item( $item ) );
		$parts['class'] .= ' rdct-bld__image';
		return $parts;
	}
	$style = '';
	if ( 'logo' === $item['type'] ) {
		foreach ( array( 'logoWidth' => 'width', 'logoHeight' => 'height', 'laptopLogoWidth' => 'laptop-width', 'laptopLogoHeight' => 'laptop-height', 'tabletLogoWidth' => 'tablet-width', 'tabletLogoHeight' => 'tablet-height', 'mobileLogoWidth' => 'mobile-width', 'mobileLogoHeight' => 'mobile-height' ) as $key => $name ) {
			if ( ( $item[ $key ] ?? 0 ) > 0 ) { $style .= '--builder-logo-' . $name . ':' . $item[ $key ] . 'px;'; }
		}
		$class = 'rdct-bld__logo';
	} else {
		$class = 'rdct-bld__button rdct-bld__button--' . ( $item['buttonStyle'] ?? 'solid' );
		foreach ( array( 'buttonRadius' => 'radius', 'buttonPaddingX' => 'padding-x', 'buttonPaddingY' => 'padding-y' ) as $key => $name ) {
			if ( isset( $item[ $key ] ) ) { $style .= '--builder-button-' . $name . ':' . $item[ $key ] . 'px;'; }
		}
		if ( ! empty( $item['buttonBackground'] ) ) { $style .= '--builder-button-accent:' . $item['buttonBackground'] . ';'; }
	}
	return array( 'class' => $class, 'style' => $style );
}
