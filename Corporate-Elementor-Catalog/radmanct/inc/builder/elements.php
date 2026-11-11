<?php

namespace RadmanCT\Builder\Elements;

defined( 'ABSPATH' ) || exit;

function url( $value, bool $social = false ): ?string {
	if ( ! is_string( $value ) || strlen( $value ) > 2048 || preg_match( '/[\x00-\x1f\x7f]/', $value ) ) {
		return null;
	}
	$value = trim( $value );
	$pattern = $social ? '~^(https?://|/(?!/)|#)~i' : '~^(https?://|mailto:|tel:|/(?!/)|#)~i';
	if ( '' !== $value && ! preg_match( $pattern, $value ) ) {
		return null;
	}
	return esc_url_raw( $value, $social ? array( 'http', 'https' ) : array( 'http', 'https', 'mailto', 'tel' ) );
}

function icon_name( $value ): bool {
	return is_string( $value ) && ( '' === $value || preg_match( '/^[a-z][a-z0-9-]{0,70}$/D', $value ) );
}

function validate( array $item, array &$ids ): ?array {
	$size = $item['size'] ?? 20;
	if ( ! is_int( $size ) || $size < 8 || $size > 64 ) {
		return null;
	}
	if ( 'social' === $item['type'] ) {
		$links = $item['links'] ?? array();
		if ( ! is_array( $links ) || array_values( $links ) !== $links || count( $links ) > 8 ) {
			return null;
		}
		$result = array();
		foreach ( $links as $link ) {
			if ( ! is_array( $link ) || ! \RadmanCT\Builder\valid_id( $link['id'] ?? null, $ids ) || ! is_string( $link['text'] ?? null ) || strlen( $link['text'] ) > 500 || ! icon_name( $link['icon'] ?? '' ) ) {
				return null;
			}
			$text = sanitize_text_field( $link['text'] );
			$url = url( $link['url'] ?? '', true );
			if ( null === $url || ( '' !== $url && '' === trim( $text ) ) ) {
				return null;
			}
			$result[] = array( 'id' => $link['id'], 'text' => $text, 'url' => $url, 'icon' => $link['icon'] ?? '' );
		}
		return array( 'links' => $result, 'size' => $size );
	}
	if ( ! icon_name( $item['icon'] ?? '' ) || ! is_string( $item['text'] ?? null ) || strlen( $item['text'] ) > 2000 ) {
		return null;
	}
	$text = sanitize_text_field( $item['text'] );
	$url = url( $item['url'] ?? '' );
	$direction = $item['direction'] ?? 'auto';
	if ( null === $url || ( '' !== $url && '' === trim( $text ) ) || ! in_array( $direction, array( 'auto', 'rtl', 'ltr' ), true ) ) {
		return null;
	}
	return array( 'text' => $text, 'url' => $url, 'icon' => $item['icon'] ?? '', 'size' => $size, 'direction' => $direction );
}

function strings( array $item ): array {
	$strings = array();
	foreach ( $item['links'] ?? array() as $link ) {
		foreach ( array( 'text', 'url' ) as $property ) {
			$strings[ 'link-' . $link['id'] . '-' . $property ] = $link[ $property ];
		}
	}
	return $strings;
}

function assets( array $item ): void {
	\RadmanCT\Components\use_component( 'builder-links' );
	\RadmanCT\Components\print_late_style( 'builder-links' );
	$names = 'social' === $item['type'] ? array_column( $item['links'], 'icon' ) : array( $item['icon'] );
	if ( ! array_filter( $names ) ) {
		return;
	}
	require_once dirname( __DIR__ ) . '/icons.php';
	if ( array_intersect( $names, array_keys( \RadmanCT\Icons\catalog() ) ) ) {
		\RadmanCT\Components\use_component( 'icons' );
		\RadmanCT\Components\print_late_style( 'icons' );
	}
}

function render( string $kind, array $item, string $layout_id, bool $preview ): string {
	$translate = static function ( string $text, string $property ) use ( $kind, $item, $layout_id, $preview ): string {
		return $preview ? $text : \RadmanCT\Builder\translate( $text, $kind, $item['id'], $property, $layout_id );
	};
	$icon = static function ( string $name, int $size, string $label = '' ): string {
		if ( '' === $name ) { return ''; }
		require_once dirname( __DIR__ ) . '/icons.php';
		return \RadmanCT\Icons\render( $name, array( 'size' => $size, 'label' => $label ) );
	};
	if ( 'social' === $item['type'] ) {
		$links = '';
		foreach ( $item['links'] as $link ) {
			$text = $translate( $link['text'], 'link-' . $link['id'] . '-text' );
			$url = url( $translate( $link['url'], 'link-' . $link['id'] . '-url' ), true ) ?? $link['url'];
			if ( '' === $url || '' === trim( $text ) ) { continue; }
			$links .= '<li><a href="' . esc_url( $url, array( 'http', 'https' ) ) . '">' . $icon( $link['icon'], $item['size'] ) . '<bdi dir="auto">' . esc_html( $text ) . '</bdi></a></li>';
		}
		return '' === $links ? '' : '<nav class="rdct-bld__social" aria-label="' . esc_attr__( 'Social links', 'radmanct' ) . '"><ul>' . $links . '</ul></nav>';
	}
	$text = $translate( $item['text'], 'text' );
	$url = url( $translate( $item['url'], 'url' ) ) ?? $item['url'];
	$only_icon = 'icon' === $item['type'];
	$markup = $icon( $item['icon'], $item['size'], $only_icon && '' === $url ? $text : '' );
	if ( ! $only_icon ) {
		$markup .= '<bdi dir="' . esc_attr( $item['direction'] ) . '">' . esc_html( $text ) . '</bdi>';
	}
	if ( '' === $markup || ( ! $only_icon && '' === trim( $text ) ) ) { return ''; }
	if ( '' !== $url && '' !== trim( $text ) ) {
		$label = $only_icon ? ' aria-label="' . esc_attr( $text ) . '"' : '';
		return '<a class="rdct-bld__contact" href="' . esc_url( $url, array( 'http', 'https', 'mailto', 'tel' ) ) . '"' . $label . '>' . $markup . '</a>';
	}
	return '<span class="rdct-bld__contact">' . $markup . '</span>';
}
