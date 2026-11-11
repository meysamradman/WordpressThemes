<?php

define( 'ABSPATH', '/' );
$page = true; $template = 'templates/custom.php'; $admin = false;
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value ) { return 'radmanct/layout/custom_templates' === $hook ? array_merge( $value, array( 'templates/custom-page.php' ) ) : $value; }
function is_admin() { return $GLOBALS['admin']; }
function is_page() { return $GLOBALS['page']; }
function get_queried_object_id() { return 10; }
function get_page_template_slug( ...$args ) { return $GLOBALS['template']; }
function current_user_can( ...$args ) { return false; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/atomic.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/pages.php';
require dirname( __DIR__ ) . '/radmanct/inc/layout.php';
expect( ! \RadmanCT\Integrations\Elementor\Atomic\enabled(), 'Missing Atomic module was reported as active.' );
\RadmanCT\Integrations\Elementor\Atomic\transformers( new stdClass() );
$native = dirname( __DIR__ ) . '/themes/elementor/modules/atomic-widgets/props-resolver/';
require $native . 'transformer-base.php';
require $native . 'props-resolver-context.php';
$registry = new class {
	public array $items = array( 'size' => 'unchanged' );
	public function register( $key, $value ) { $this->items[ $key ] = $value; }
};
\RadmanCT\Integrations\Elementor\Atomic\transformers( $registry );
$context = \Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context::make();
expect( 'unchanged' === $registry->items['size'], 'Unrelated Atomic transformer changed.' );
foreach ( array( 'Remote Font', 'var(--custom-font)', 'Radman CT Manrope' ) as $font ) {
	expect( 'var(--rdct-elementor-font,var(--rdct-font-body))' === $registry->items['font-family']->transform( $font, $context ), 'Atomic font escaped local ownership.' );
}
expect( null === $registry->items['font-family']->transform( array(), $context ), 'Malformed Atomic font reached CSS.' );
$data = array( array( 'widgetType' => 'e-heading', 'settings' => array( 'title' => 'Atomic' ) ) );
expect( array() === \RadmanCT\Integrations\Elementor\Pages\content_data( $data, 10 ), 'Explicit custom page was replaced by Elementor.' );
$template = 'templates/custom-page.php';
expect( array() === \RadmanCT\Integrations\Elementor\Pages\content_data( $data, 10 ), 'Registered child custom page was replaced by Elementor.' );
expect( $data === \RadmanCT\Integrations\Elementor\Pages\content_data( $data, 20 ), 'Unrelated document was suppressed.' );
foreach ( array( 'templates/elementor.php', '' ) as $template ) {
	expect( $data === \RadmanCT\Integrations\Elementor\Pages\content_data( $data, 10 ), 'Existing Elementor page data was changed.' );
}
$template = 'templates/custom.php'; $page = false;
expect( $data === \RadmanCT\Integrations\Elementor\Pages\content_data( $data, 10 ), 'Non-page document was suppressed.' );
echo "Passed: native Atomic transformer/context API, optional guard, local fonts and independent custom/Elementor page output.\n";
