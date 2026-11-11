<?php

namespace Elementor { class Plugin { public static $instance; } }
namespace {
define( 'ABSPATH', '/' );
$calls = array(); $queries = 0; $language = 'fa';
function __( $value, $domain ) { return $value; }
function apply_filters( $name, $value, ...$args ) { if ( 'wpml_current_language' === $name ) { return $GLOBALS['language']; } return 'wpml_object_id' === $name && 3 === $value ? 4 : $value; }
function get_post_type( $id ) { return 99 === $id ? 'page' : 'elementor_library'; }
function get_post_status( $id ) { return 2 === $id ? 'draft' : 'publish'; }
function post_password_required( $id ) { return 5 === $id; }
function post_type_exists( $type ) { return true; }
function get_posts( $args ) { ++$GLOBALS['queries']; if ( 100 !== $args['posts_per_page'] || 'publish' !== $args['post_status'] || false !== $args['suppress_filters'] || false !== $args['has_password'] ) { throw new \RuntimeException( 'Unbounded or unfiltered template query.' ); } return array( (object) array( 'ID' => 'en' === $GLOBALS['language'] ? 2 : 1 ) ); }
function get_the_title( $post ) { return 'Template'; }
function expect( $value, $message ) { if ( ! $value ) { throw new \RuntimeException( $message ); } }
require __DIR__ . '/../radmanct-components/inc/components/template-library.php';
require __DIR__ . '/../radmanct-components/inc/components/flipbox.php';
\Elementor\Plugin::$instance = (object) array( 'frontend' => new class {
	public function get_builder_content_for_display( $id, $css ) {
		$GLOBALS['calls'][] = $id;
		if ( 1 === $id ) { return '<p>One</p>' . \RadmanCTComponents\Components\FlipBox\template( 1 ); }
		if ( $id >= 10 && $id <= 15 ) { return '<p>Level</p>' . \RadmanCTComponents\Components\TemplateLibrary\render( $id + 1 ); }
		return '<p>Translated</p>';
	}
} );
expect( '<p>One</p>' === \RadmanCTComponents\Components\TemplateLibrary\render( 1 ) && array( 1 ) === $calls, 'Cross-component cycle was not blocked.' );
foreach ( array( 0, 2, 5, 99 ) as $id ) { expect( '' === \RadmanCTComponents\Components\TemplateLibrary\render( $id ), 'Nonpublic or invalid template rendered.' ); }
expect( '<p>Translated</p>' === \RadmanCTComponents\Components\TemplateLibrary\render( 3 ) && 4 === end( $calls ), 'Translated template ID was ignored.' );
$calls = array();
\RadmanCTComponents\Components\TemplateLibrary\render( 10 );
expect( array( 10, 11, 12, 13 ) === $calls, 'Template depth guard failed.' );
\RadmanCTComponents\Components\TemplateLibrary\options();
\RadmanCTComponents\Components\FlipBox\templates();
expect( 1 === $queries, 'Template list query was duplicated.' );
$language = 'en';
expect( isset( \RadmanCTComponents\Components\TemplateLibrary\options()[2] ) && ! isset( \RadmanCTComponents\Components\TemplateLibrary\options()[1] ), 'Template options reused another language cache.' );
$language = 'fa';
expect( isset( \RadmanCTComponents\Components\TemplateLibrary\options()[1] ) && 2 === $queries, 'Language switch discarded the request-local cache or mixed languages.' );
echo "Passed: shared template publication/password/type checks, WPML mapping, cross-component cycle/depth protection and bounded cached options.\n";
}
