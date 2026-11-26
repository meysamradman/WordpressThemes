<?php
/**
 * Liquid SVG sanitizer.
 *
 * Cleans SVG markup before it is stored in the media library or printed inline in a page.
 * The same file ships in the theme, the core plugin and the Elementor addons; the first
 * copy that loads defines the class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Liquid_SVG_Sanitizer' ) ) :

	class Liquid_SVG_Sanitizer {

		/**
		 * Elements that may stay in an SVG. Anything else is removed with its children.
		 * Filter primitives (fe*) are allowed by prefix.
		 *
		 * @var array
		 */
		private static $elements = array(
			'svg', 'g', 'path', 'circle', 'ellipse', 'line', 'polyline', 'polygon', 'rect',
			'defs', 'clippath', 'mask', 'pattern', 'lineargradient', 'radialgradient', 'stop',
			'symbol', 'use', 'title', 'desc', 'text', 'tspan', 'textpath', 'image', 'style',
			'filter', 'marker', 'animate', 'animatetransform', 'animatemotion', 'set', 'mpath',
			'switch', 'a', 'view',
		);

		/**
		 * Returns clean SVG markup, or an empty string when the input is not an SVG.
		 *
		 * @param string $markup Raw file or response contents.
		 * @return string
		 */
		public static function sanitize( $markup ) {

			$markup = (string) $markup;

			// Gzipped SVG (.svgz).
			if ( 0 === strpos( $markup, "\x1f\x8b" ) ) {
				$markup = function_exists( 'gzdecode' ) ? (string) @gzdecode( $markup ) : '';
			}

			$markup = trim( $markup );

			if ( '' === $markup || ! class_exists( 'DOMDocument' ) ) {
				return '';
			}

			// Drop the BOM, XML prolog, doctype (with any entity definitions) and leading comments,
			// then require the document to open with <svg>.
			$markup = preg_replace( '#<!DOCTYPE\b[^>\[]*(\[.*?\])?\s*>#is', '', $markup );
			$markup = preg_replace( '#<!ENTITY\b[^>]*>#is', '', (string) $markup );
			$markup = preg_replace( '#^(?:\xEF\xBB\xBF|\s+|<\?xml\b[^>]*\?>|<!--.*?-->)+#is', '', (string) $markup );
			$markup = (string) $markup;

			if ( 0 !== stripos( $markup, '<svg' ) ) {
				return '';
			}

			$previous = libxml_use_internal_errors( true );

			$dom          = new DOMDocument();
			$dom->recover = true;
			$loaded       = $dom->loadXML( $markup, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );

			libxml_clear_errors();
			libxml_use_internal_errors( $previous );

			$root = $loaded ? $dom->documentElement : null;

			if ( ! $root || 'svg' !== strtolower( $root->localName ) ) {
				return '';
			}

			self::clean_node( $root );

			$out = $dom->saveXML( $root );

			return is_string( $out ) ? $out : '';
		}

		/**
		 * Removes unknown elements and unsafe attributes below a node.
		 *
		 * @param DOMElement $node Element to clean.
		 */
		private static function clean_node( $node ) {

			self::clean_attributes( $node );

			$name = strtolower( $node->localName );

			if ( 'style' === $name ) {
				$css = preg_replace( '#@import[^;]*;?#i', '', $node->textContent );
				$css = preg_replace( '#(expression\s*\(|javascript\s*:|vbscript\s*:|behaviou?r\s*:|-moz-binding)#i', '', (string) $css );
				$css = preg_replace( '#url\(\s*(?![\'"]?\s*\#)[^)]*\)#i', 'none', (string) $css );
				while ( $node->firstChild ) {
					$node->removeChild( $node->firstChild );
				}
				$node->appendChild( $node->ownerDocument->createTextNode( str_replace( array( '<', '>' ), '', (string) $css ) ) );
				return;
			}

			$children = array();
			foreach ( $node->childNodes as $child ) {
				$children[] = $child;
			}

			foreach ( $children as $child ) {

				if ( XML_ELEMENT_NODE === $child->nodeType ) {
					$child_name = strtolower( $child->localName );
					if ( in_array( $child_name, self::$elements, true ) || 0 === strpos( $child_name, 'fe' ) ) {
						self::clean_node( $child );
					} else {
						$node->removeChild( $child );
					}
					continue;
				}

				// CDATA becomes plain text, so it is written out escaped. Browsers read an inline SVG as
				// HTML, where a CDATA section is not a safe container.
				if ( XML_CDATA_SECTION_NODE === $child->nodeType ) {
					$node->replaceChild( $node->ownerDocument->createTextNode( $child->data ), $child );
					continue;
				}

				// Keep text; drop comments, processing instructions, entity references and anything else.
				if ( XML_TEXT_NODE !== $child->nodeType ) {
					$node->removeChild( $child );
				}
			}
		}

		/**
		 * Removes event handlers and attributes that carry scripts or outside documents.
		 *
		 * @param DOMElement $node Element to clean.
		 */
		private static function clean_attributes( $node ) {

			if ( ! $node->hasAttributes() ) {
				return;
			}

			$element = strtolower( $node->localName );
			$remove  = array();

			foreach ( $node->attributes as $attribute ) {

				$name  = strtolower( $attribute->localName );
				$value = strtolower( preg_replace( '/[\s\x00-\x20]+/', '', (string) $attribute->value ) );

				if ( 0 === strpos( $name, 'on' ) ) {
					$remove[] = $attribute;
					continue;
				}

				if (
					false !== strpos( $value, 'javascript:' ) ||
					false !== strpos( $value, 'vbscript:' ) ||
					false !== strpos( $value, 'expression(' ) ||
					( 0 === strpos( $value, 'data:' ) && ! preg_match( '#^data:image/(png|gif|jpe?g|webp);base64,#', $value ) )
				) {
					$remove[] = $attribute;
					continue;
				}

				if ( 'href' === $name || 'src' === $name ) {
					$local = ( '' !== $value && '#' === $value[0] );
					$image = ( 'image' === $element && preg_match( '#^(https?://|data:image/(png|gif|jpe?g|webp);base64,)#', $value ) );
					$link  = ( 'a' === $element && preg_match( '#^(https?://|/|mailto:)#', $value ) );
					if ( ! $local && ! $image && ! $link ) {
						$remove[] = $attribute;
					}
					continue;
				}

				// Animations may not rewrite links or event handlers.
				if ( 'attributename' === $name && ( false !== strpos( $value, 'href' ) || 0 === strpos( $value, 'on' ) ) ) {
					$remove[] = $attribute;
				}
			}

			foreach ( $remove as $attribute ) {
				$node->removeAttributeNode( $attribute );
			}
		}

		/**
		 * Returns clean inline SVG markup for an SVG that belongs to this site or sits on a public host.
		 *
		 * Files of this site are read from disk. Other addresses are fetched with the WordPress
		 * safe HTTP functions, which refuse private and loopback hosts, and the result is cached.
		 *
		 * @param string $url           SVG address, as stored in the widget, shortcode or menu item.
		 * @param int    $attachment_id Attachment ID, when known.
		 * @return string
		 */
		public static function inline( $url, $attachment_id = 0 ) {

			$url = is_string( $url ) ? trim( $url ) : '';

			if ( ! empty( $attachment_id ) ) {
				$file = get_attached_file( (int) $attachment_id );
				if ( ! empty( $file ) && is_readable( $file ) && self::is_svg_name( $file ) ) {
					return self::sanitize( (string) @file_get_contents( $file ) );
				}
			}

			if ( '' === $url ) {
				return '';
			}

			$path = self::local_path( $url );

			if ( '' !== $path ) {
				return ( is_readable( $path ) && self::is_svg_name( $path ) ) ? self::sanitize( (string) @file_get_contents( $path ) ) : '';
			}

			$host = wp_parse_url( $url, PHP_URL_HOST );
			$home = wp_parse_url( home_url(), PHP_URL_HOST );

			// Our own host but no such file on disk: do not request our own pages.
			if ( empty( $host ) || ( $home && strtolower( $host ) === strtolower( $home ) ) ) {
				return '';
			}

			if ( ! preg_match( '#^https?://#i', $url ) ) {
				return '';
			}

			$cache_key = 'lqd_svg_' . md5( $url );
			$cached    = get_transient( $cache_key );

			if ( false !== $cached ) {
				return is_string( $cached ) ? $cached : '';
			}

			$response = wp_safe_remote_get( $url, array( 'timeout' => 5, 'redirection' => 2, 'limit_response_size' => 1048576 ) );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				set_transient( $cache_key, '', HOUR_IN_SECONDS );
				return '';
			}

			$svg = self::sanitize( wp_remote_retrieve_body( $response ) );

			set_transient( $cache_key, $svg, DAY_IN_SECONDS );

			return $svg;
		}

		/**
		 * Maps an address of this site (uploads, wp-content or the site root) to its file.
		 * Callers only read files whose name ends in .svg or .svgz.
		 *
		 * @param string $url Address to map.
		 * @return string Absolute path, or an empty string.
		 */
		private static function local_path( $url ) {

			$url  = rawurldecode( (string) strtok( $url, '?#' ) );
			$url  = preg_replace( '#^(https?:)?//#i', '//', $url );
			$maps = array();

			$uploads = wp_upload_dir();
			if ( empty( $uploads['error'] ) && ! empty( $uploads['baseurl'] ) && ! empty( $uploads['basedir'] ) ) {
				$maps[] = array( $uploads['baseurl'], $uploads['basedir'] );
			}
			$maps[] = array( content_url(), WP_CONTENT_DIR );
			$maps[] = array( site_url(), ABSPATH );

			foreach ( $maps as $map ) {

				$base_url = untrailingslashit( preg_replace( '#^(https?:)?//#i', '//', $map[0] ) );

				if ( 0 !== strpos( $url, $base_url . '/' ) ) {
					continue;
				}

				$base = realpath( $map[1] );
				$path = realpath( untrailingslashit( $map[1] ) . substr( $url, strlen( $base_url ) ) );

				// Both sides are resolved, so the file cannot sit outside the folder it was mapped to.
				if ( ! empty( $base ) && ! empty( $path ) && 0 === strpos( $path, trailingslashit( $base ) ) ) {
					return $path;
				}

				return '';
			}

			return '';
		}

		/**
		 * @param string $file File name or path.
		 * @return bool
		 */
		private static function is_svg_name( $file ) {
			return (bool) preg_match( '#\.svgz?$#i', (string) $file );
		}

		/**
		 * Cleans an SVG as it is uploaded to the media library.
		 *
		 * Hooked to `wp_handle_upload_prefilter` and `wp_handle_sideload_prefilter`.
		 *
		 * @param array $file Uploaded file entry.
		 * @return array
		 */
		public static function filter_upload( $file ) {

			if ( empty( $file['name'] ) || empty( $file['tmp_name'] ) || ! self::is_svg_name( $file['name'] ) ) {
				return $file;
			}

			$raw   = (string) @file_get_contents( $file['tmp_name'] );
			$gzip  = ( 0 === strpos( $raw, "\x1f\x8b" ) );
			$clean = self::sanitize( $raw );

			if ( '' === $clean ) {
				$file['error'] = __( 'This file could not be read as an SVG image, so it was not uploaded.', 'default' );
				return $file;
			}

			if ( $gzip && function_exists( 'gzencode' ) ) {
				$clean = gzencode( $clean );
			}

			if ( false === @file_put_contents( $file['tmp_name'], $clean ) ) {
				$file['error'] = __( 'This SVG image could not be checked, so it was not uploaded.', 'default' );
				return $file;
			}

			$file['size'] = strlen( $clean );

			return $file;
		}
	}

endif;
