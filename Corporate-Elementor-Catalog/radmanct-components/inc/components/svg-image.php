<?php

namespace RadmanCTComponents\Components\SvgImage;

defined( 'ABSPATH' ) || exit;

function render( int $id ): string {
	if ( ! class_exists( '\\DOMDocument' ) || 'image/svg+xml' !== get_post_mime_type( $id ) ) { return ''; }
	$uploads = wp_get_upload_dir();
	$base = realpath( $uploads['basedir'] );
	$file = get_attached_file( $id );
	$file = is_string( $file ) ? realpath( $file ) : false;
	if ( ! $base || ! $file || ! str_starts_with( wp_normalize_path( $file ), rtrim( wp_normalize_path( $base ), '/' ) . '/' ) || ! is_readable( $file ) || filesize( $file ) > 131072 ) { return ''; }
	static $sources = array();
	$source = $sources[ $file ] ?? ( $sources[ $file ] = file_get_contents( $file ) );
	if ( ! is_string( $source ) || preg_match( '/<!DOCTYPE|<!ENTITY/i', $source ) ) { return ''; }
	$xml = new \DOMDocument();
	$previous = libxml_use_internal_errors( true );
	try { $valid = $xml->loadXML( $source, LIBXML_NONET | LIBXML_NOBLANKS ); }
	finally { libxml_clear_errors(); libxml_use_internal_errors( $previous ); }
	if ( ! $valid || ! $xml->documentElement || 'svg' !== $xml->documentElement->localName ) { return ''; }
	$tags = array( 'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon', 'defs', 'linearGradient', 'radialGradient', 'stop', 'clipPath' );
	$attributes = array( 'viewBox', 'preserveAspectRatio', 'd', 'points', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'fill-rule', 'clip-rule', 'opacity', 'fill-opacity', 'stroke-opacity', 'transform', 'gradientTransform', 'gradientUnits', 'offset', 'stop-color', 'stop-opacity', 'clip-path', 'clipPathUnits', 'id', 'pathLength' );
	$output = new \DOMDocument(); $count = 0; $prefix = wp_unique_id( 'rdct-svg-' );
	$copy = static function ( \DOMNode $node, int $depth = 0 ) use ( &$copy, &$count, $output, $tags, $attributes, $prefix ): ?\DOMElement {
		if ( ! $node instanceof \DOMElement || $depth > 32 || ++$count > 2000 || ! in_array( $node->localName, $tags, true ) || ( $node->namespaceURI && 'http://www.w3.org/2000/svg' !== $node->namespaceURI ) ) { return null; }
		$element = $output->createElementNS( 'http://www.w3.org/2000/svg', $node->localName );
		foreach ( $node->attributes as $attribute ) {
			$name = $attribute->name; $value = $attribute->value;
			if ( ! in_array( $name, $attributes, true ) || strlen( $value ) > 64000 || preg_match( '/[<>;]|javascript|expression|@import|data:/i', $value ) ) { continue; }
			if ( 'id' === $name ) { if ( ! preg_match( '/^[a-z_][a-z0-9_.-]{0,79}$/iD', $value ) ) { continue; } $value = $prefix . '-' . $value; }
			elseif ( str_contains( strtolower( $value ), 'url' ) ) { if ( ! preg_match( '/^url\(#([a-z_][a-z0-9_.-]{0,79})\)$/iD', $value, $match ) ) { continue; } $value = 'url(#' . $prefix . '-' . $match[1] . ')'; }
			elseif ( ! preg_match( '/^[a-z0-9#.,%+()\s\/-]*$/iD', $value ) ) { continue; }
			$element->setAttribute( $name, $value );
		}
		foreach ( $node->childNodes as $child ) { $safe = $copy( $child, $depth + 1 ); if ( $safe ) { $element->appendChild( $safe ); } }
		return $element;
	};
	$root = $copy( $xml->documentElement );
	if ( ! $root || $count > 2000 ) { return ''; }
	$root->setAttribute( 'class', 'rdct-ib__svg' );
	$root->setAttribute( 'aria-hidden', 'true' );
	$root->setAttribute( 'focusable', 'false' );
	return $output->saveXML( $root ) ?: '';
}
