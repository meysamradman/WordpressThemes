<?php
require __DIR__ . '/curtain-contract.php';
require __DIR__ . '/../radmanct-components/inc/components/accordion.php';
use RadmanCTComponents\Components\Accordion;
$items = array( array( 'title' => '<b>One</b>', 'content' => '<p>Answer</p>' ), array( 'title' => 'Two', 'content' => '<p>Next</p>' ) );
$html = Accordion\render( $items, array( 'style' => 'bad', 'heading_tag' => 'script', 'open_item' => 2, 'padding' => -9 ) );
expect( 2 === substr_count( $html, '<details ' ) && 2 === substr_count( $html, '<summary ' ) && 1 === substr_count( $html, ' open>' ), 'Accordion lost native toggle semantics or initial state.' );
expect( str_contains( $html, '<h3' ) && ! str_contains( $html, '<b>One' ) && str_contains( $html, '--rdct-accordion-padding:0px' ), 'Accordion heading, content or options were unsafe.' );
preg_match( '/name="([^"]+)"/', $html, $match );
expect( 2 === substr_count( $html, 'name="' . $match[1] . '"' ) && ! str_contains( Accordion\render( $items ), 'name="' . $match[1] . '"' ), 'Accordion groups leak between component instances.' );
expect( ! str_contains( Accordion\render( $items, array( 'multiple' => true, 'open_item' => 0 ) ), ' name=' ) && ! str_contains( Accordion\render( $items, array( 'open_item' => 0 ) ), ' open>' ), 'Multiple mode or all-closed state was lost.' );
expect( '' === Accordion\render( array( array( 'title' => '' ), 'invalid' ) ), 'Invalid accordion renders scaffolding.' );
expect( ! str_contains( Accordion\render( $items, array( 'indicator' => 'none' ) ), 'rdct-accordion__indicator' ), 'Disabled decoration still renders.' );
expect( 100 === substr_count( Accordion\render( array_fill( 0, 110, $items[0] ) ), '<details ' ), 'Accordion rows are unbounded.' );
foreach ( array( 'lined', 'boxed' ) as $style ) {
 $assets = \RadmanCTComponents\Components\requirements( 'accordion', array( 'style' => $style ) );
 expect( array( 'accordion', 'accordion-' . $style ) === $assets['styles'] && ! $assets['script'], 'Accordion leaks another style or JavaScript.' );
}
echo "Passed: accordion native semantics, isolated groups, open/multiple state, bounded and escaped content, optional indicators and selected no-JS assets.\n";
