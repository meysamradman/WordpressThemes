<?php
namespace Elementor {
	class Widget_Base {
		public function __construct( public array $values = array(), public bool $type = false ) {}
		public function is_type_instance() { return $this->type; }
		public function get_settings_for_display() { return $this->values; }
	}
}
namespace {
require __DIR__ . '/components-contract.php';
function wp_kses_post( $value ) { return strip_tags( $value, '<strong><em>' ); }
function esc_url_raw( $value, $protocols = null ) { return $value; }
function wp_attachment_is_image( $id ) { return 7 === $id; }
function wp_get_attachment_image( $id, $size, $icon, $attributes ) { $GLOBALS['box_image_attributes'] = $attributes; return '<img src="/local.jpg" width="1200" height="800">'; }
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/widgets/box.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/widgets/image.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/widgets/text-reveal.php';
use RadmanCTComponents\Components;
foreach ( array( 's01', 's01a', 's01b', 's04', 's05', 's07', 's09', 's10', 's11' ) as $variant ) {
	expect( array() === ( new \RadmanCTComponents\Elementor\Widgets\Box( array( 'template' => $variant ) ) )->get_script_depends(), 'Static Classic Box requests JS.' );
}
foreach ( array( 's02', 's03', 's06', 's08' ) as $variant ) {
	expect( array( 'rdct-box', 'rdct-box-' . $variant ) === ( new \RadmanCTComponents\Elementor\Widgets\Box( array( 'template' => $variant ) ) )->get_style_depends(), 'Classic variant styles differ from the shared registry.' );
	$expected_scripts = 's06' === $variant ? array( 'rdct-elementor-components' ) : array();
	expect( $expected_scripts === ( new \RadmanCTComponents\Elementor\Widgets\Box( array( 'template' => $variant ) ) )->get_script_depends(), 'Box requested JavaScript for a CSS hover or lost its pointer-tilt engine.' );
}
expect( array() === ( new \RadmanCTComponents\Elementor\Widgets\Image() )->get_script_depends(), 'Static Image requests JS.' );
foreach ( array( 'float', 'animate_shadow', 'tilt', 'reveal', 'lightbox' ) as $feature ) {
	expect( array( 'rdct-elementor-components' ) === ( new \RadmanCTComponents\Elementor\Widgets\Image( array( $feature => true ) ) )->get_script_depends(), 'Image behavior lost JS.' );
}
foreach ( array( 'rich', 'static', 'hover' ) as $mode ) {
	expect( array() === ( new \RadmanCTComponents\Elementor\Widgets\TextReveal( array( 'mode' => $mode ) ) )->get_script_depends(), 'Static Text requests JS.' );
}
expect( Components\needs_script( 'text-reveal', array( 'mode' => 'invalid' ) ), 'Invalid Text mode lost default scroll behavior.' );
foreach ( array( 'icon', 'table', 'icon-box' ) as $type ) { expect( array() === Components\script_depends( $type, array(), true ), 'CSS-only Atomic metadata requests JS.' ); }
expect( array( 'rdct-elementor-components' ) === ( new \RadmanCTComponents\Elementor\Widgets\Image( array(), true ) )->get_script_depends(), 'Editor type-instance lost dynamic behavior adapter.' );
\RadmanCTComponents\Components\Box\render( array( 'title' => 'Hero', 'image_id' => 7 ) );
expect( ! array_key_exists( 'loading', $GLOBALS['box_image_attributes'] ) && ! array_key_exists( 'fetchpriority', $GLOBALS['box_image_attributes'] ) && 'async' === $GLOBALS['box_image_attributes']['decoding'], 'Box overrides native WordPress image prioritization.' );
echo "Passed: static versus interactive dependencies, shared PHP/Atomic predicate, and editor type-instance fallback.\n";
$rich_box = \RadmanCTComponents\Components\Box\render( array( 'title' => 'Box', 'text' => '<strong>Formatted</strong>' ) );
expect( str_contains( $rich_box, '<div class="rdct-box__text"><strong>Formatted</strong></div>' ), 'Box discarded native editor formatting or nested rich content inside a paragraph.' );
}
