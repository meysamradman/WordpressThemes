<?php

require __DIR__ . '/carousel-profiles-contract.php';
require_once __DIR__ . '/../radmanct-components/inc/components/sticky-sections.php';
use RadmanCTComponents\Components as Components;
use RadmanCTComponents\Components\StickySections as Sticky;

$items = array( array( 'title' => 'First', 'content' => '<p>Card</p>' ), array( 'title' => 'Second', 'image_id' => array( 'id' => 7 ) ) );
foreach ( array( 'stack' => 'stack', 'zoom' => 'stack', 'services' => 'panels', 'split' => 'split' ) as $mode => $asset ) {
	expect( array( 'sticky-sections', 'sticky-sections-' . $asset ) === Components\requirements( 'sticky-sections', array( 'mode' => $mode ) )['styles'], 'Sticky mode loaded unrelated styles.' );
	$html = Sticky\render( $items, array( 'mode' => $mode ) );
	expect( str_contains( $html, 'data-mode="' . $mode . '"' ) && str_contains( $html, 'data-id="7"' ), 'Sticky renderer lost its mode or native media value.' );
}
expect( array( 'sticky-sections' ) === Components\requirements( 'sticky-sections', array( 'mode' => 'overlap' ) )['styles'], 'Overlap loads an unused design stylesheet.' );
expect( 12 === count( Sticky\items( array_fill( 0, 20, $items[0] ) ) ), 'Sticky item count is unbounded.' );
$html = Sticky\render( $items, array( 'mode' => 'split', 'aside_position' => 'end' ), static function (): void { echo '<div data-custom-fixed>Custom fixed content</div>'; } );
expect( str_contains( $html, 'rdct-ss__aside--end' ) && 1 === substr_count( $html, 'data-custom-fixed' ) && 2 === substr_count( $html, 'class="rdct-ss__item"' ), 'Split renderer duplicates or loses custom content.' );
expect( 'stack' === Sticky\settings( array( 'mode' => 'invalid' ) )['mode'], 'Unknown sticky mode escaped the registry.' );
$html = Sticky\render( $items, array( 'mode' => 'services' ), null, static function ( int $index ): void { echo '<custom-card data-card="' . $index . '">Custom card</custom-card>'; } );
expect( 2 === substr_count( $html, '<custom-card ' ) && str_contains( $html, 'data-card="1"' ), 'Trusted PHP item callbacks were lost or sanitized as editor input.' );
$level = ob_get_level();
try { Sticky\render( $items, array( 'mode' => 'services' ), null, static function (): void { echo 'Discard'; throw new RuntimeException( 'Callback failed' ); } ); } catch ( RuntimeException $error ) {}
expect( $level === ob_get_level(), 'Failed custom card callback leaked an output buffer.' );
echo "Passed: sticky conditional styles, native media, bounded items, arbitrary fixed PHP content and independent render modes.\n";
