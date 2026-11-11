<?php

namespace Elementor { class Plugin { public static $instance; } }
namespace {
define( 'ABSPATH', '/' );
function __( $text, $domain ) { return $text; }
function apply_filters( $hook, $id, ...$args ) { return 'wpml_object_id' === $hook && 41 === $id ? 42 : $id; }
function get_post_type( $id ) { return 9 === $id ? 'post' : 'elementor_library'; }
function get_post_status( $id ) { return 8 === $id ? 'draft' : 'publish'; }
function post_password_required( $id ) { return 7 === $id; }
function post_type_exists( $type ) { return true; }
function get_posts( $args ) { $GLOBALS['query'] = $args; return array( (object) array( 'ID' => 42 ) ); }
function get_the_title( $post ) { return 'Client design'; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/navigation/templates.php';

$calls = array(); $throw = false;
\Elementor\Plugin::$instance = (object) array( 'frontend' => new class {
	public function get_builder_content_for_display( $id, $css ) {
		$GLOBALS['calls'][] = array( $id, $css );
		if ( $GLOBALS['throw'] ) { throw new \RuntimeException( 'Renderer error' ); }
		return '<section>Template ' . $id . \RadmanCT\Navigation\Templates\render( $id ) . '</section>';
	}
} );
expect( '<section>Template 42</section>' === \RadmanCT\Navigation\Templates\render( 41 ), 'Template mapping, recursion guard or native renderer failed.' );
expect( array( array( 42, true ) ) === $calls, 'Selected template did not use the native renderer with its CSS.' );
foreach ( array( 0, 7, 8, 9 ) as $id ) { expect( '' === \RadmanCT\Navigation\Templates\render( $id ), 'Unpublished/protected/wrong-type template rendered.' ); }
$throw = true; try { \RadmanCT\Navigation\Templates\render( 42 ); } catch ( \RuntimeException $error ) {}
$throw = false; expect( '<section>Template 42</section>' === \RadmanCT\Navigation\Templates\render( 42 ), 'A failed render left the recursion guard locked.' );
expect( 'Client design' === \RadmanCT\Navigation\Templates\options()[42] && 100 === $query['posts_per_page'] && 'publish' === $query['post_status'], 'Template choices are unbounded or include unpublished content.' );
echo "Passed: native template rendering, published/password boundaries, bounded choices, WPML mapping and recursive/error cleanup.\n";
}
