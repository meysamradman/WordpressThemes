<?php

namespace RadmanCTTests\ContentUI;

defined( 'ABSPATH' ) || exit;

function expect( bool $condition, string $message ): void {
	if ( ! $condition ) { throw new \RuntimeException( $message ); }
}

require_once RDCT_COMPONENTS_PATH . '/inc/components/taxonomy-list.php';
require_once RDCT_COMPONENTS_PATH . '/inc/components/archive-search.php';

$query = $GLOBALS['wp_query'];
register_taxonomy( 'rdct_test_private', array( 'post' ), array( 'public' => false ) );
register_post_type( 'rdct_test_private', array( 'public' => false ) );
expect( '' === \RadmanCTComponents\Components\TaxonomyList\render( array( 'taxonomy' => 'rdct_test_private' ) ), 'Private taxonomy rendered.' );
expect( '' === \RadmanCTComponents\Components\TaxonomyList\render( array( 'taxonomy' => 'not_registered' ) ), 'Unknown taxonomy fell back to another source.' );
expect( '' === \RadmanCTComponents\Components\ArchiveSearch\render( array( 'post_type' => 'rdct_test_private' ) ), 'Private post type rendered.' );
expect( '' === \RadmanCTComponents\Components\ArchiveSearch\render( array( 'post_type' => 'not_registered' ) ), 'Unknown post type rendered.' );
$search = \RadmanCTComponents\Components\ArchiveSearch\render( array( 'post_type' => 'post' ) );
expect( str_contains( $search, 'rdct-component-search--stacked' ) && str_contains( $search, 'rdct-search-field' ) && ! str_contains( $search, '<button' ), 'Archive search must use the shared icon field without a submit button.' );
expect( str_contains( $search, '<label>' ) && ! str_contains( $search, 'rdct-form-label-hidden' ), 'Sidebar search label is missing.' );
$inline = \RadmanCTComponents\Components\ArchiveSearch\render( array( 'post_type' => '', 'layout' => 'inline', 'show_label' => false ) );
expect( str_contains( $inline, 'rdct-form-label-hidden' ), 'Inline search lost its accessible label.' );
expect( array( 'taxonomy-chips' ) === \RadmanCTComponents\Components\requirements( 'taxonomy-list', array( 'layout' => 'chips' ) )['styles'], 'Taxonomy styles are not isolated.' );

ob_start();
\RadmanCTComponents\UI\select_field( 'sample', '<Unsafe>', array( array( '0', '<script>unsafe</script>' ), array( '1', 'Disabled', true ) ), '0' );
$html = ob_get_clean();
expect( ! str_contains( $html, '<script>' ) && str_contains( $html, '&lt;Unsafe&gt;' ), 'Field markup was not escaped.' );
expect( str_contains( $html, ' selected' ) && str_contains( $html, ' disabled' ), 'Native field semantics were lost.' );
ob_start();
\RadmanCTComponents\UI\term_chips( array( (object) array( 'term_id' => 7, 'name' => '<Unsafe>', 'count' => 2 ) ), array(
	'current' => 7, 'url' => static fn(): string => '/products/?rm_tag=test',
	'link_attributes' => static fn(): array => array( 'data-rdct-tag' => 'test', 'onclick' => 'unsafe()' ),
) );
$html = ob_get_clean();
expect( str_contains( $html, 'aria-current="page"' ) && str_contains( $html, 'data-rdct-tag="test"' ), 'Shared chips lost filter state or hooks.' );
expect( str_contains( $html, '&lt;Unsafe&gt;' ) && ! str_contains( $html, 'onclick' ), 'Shared chips failed output escaping.' );
expect( $GLOBALS['wp_query'] === $query, 'Shared component changed the main query.' );
unregister_taxonomy( 'rdct_test_private' );
unregister_post_type( 'rdct_test_private' );

if ( class_exists( '\Elementor\Plugin' ) ) {
	$registry = \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
	foreach ( array( 'rdct-taxonomy-list', 'rdct-archive-search', 'rdct-atomic-taxonomy-list', 'rdct-atomic-archive-search' ) as $id ) {
		expect( isset( $registry[ $id ] ), 'Missing widget: ' . $id );
		$class = get_class( $registry[ $id ] );
		$widget = new $class( array( 'id' => 'abc1234', 'elType' => 'widget', 'widgetType' => $id, 'settings' => array() ), array() );
		$render = new \ReflectionMethod( $widget, 'render' );
		$render->setAccessible( true );
		ob_start(); $render->invoke( $widget ); $html = ob_get_clean();
		if ( str_contains( $id, 'archive-search' ) ) { expect( str_contains( $html, 'rdct-component-search' ), 'Widget does not use shared search renderer.' ); }
		if ( ! str_contains( $id, 'atomic' ) ) { expect( count( $widget->get_controls() ) > 0, 'Missing native Classic controls.' ); }
		else {
			$schema = new \ReflectionMethod( $widget, 'define_props_schema' ); $schema->setAccessible( true );
			expect( is_array( $schema->invoke( null ) ), 'Missing Atomic typed schema.' );
		}
	}
}

echo "Passed: shared content UI, public-data boundaries, native fields and installed editor adapters.\n";
