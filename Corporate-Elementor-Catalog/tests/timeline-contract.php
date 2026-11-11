<?php
require __DIR__ . '/curtain-contract.php';
require __DIR__ . '/../radmanct-components/inc/components/timeline.php';
use RadmanCTComponents\Components\Timeline;
expect( '' === Timeline\render( array( array(), 'bad' ) ), 'Empty timeline renders scaffolding.' );
$html = Timeline\render( array( array( 'title' => '<b>Start</b>', 'date' => 'Milestone', 'datetime' => '2026-10-27', 'content' => '<p>Details</p>', 'image_id' => array( 'id' => 7 ) ), array( 'title' => 'Next', 'datetime' => '2026-02-31', 'date' => 'Next' ) ), array( 'style' => '../../bad', 'heading_tag' => 'script', 'first_image' => 'end', 'gap' => -8 ) );
expect( str_starts_with( $html, '<ol role="list"' ) && 2 === substr_count( $html, '<li ' ), 'Timeline lost semantic ordered content.' );
expect( str_contains( $html, 'datetime="2026-10-27"' ) && ! str_contains( $html, 'datetime="2026-02-31"' ), 'Invalid machine date reached output.' );
expect( str_contains( $html, '<h3' ) && ! str_contains( $html, '<script' ) && str_contains( $html, '--rdct-timeline-gap:0px' ) && str_contains( $html, 'data-first-image="end"' ), 'Timeline options bypassed bounds.' );
expect( 1 === substr_count( $html, '<figure' ) && str_contains( $html, 'data-id="7"' ), 'Timeline failed native media or emitted empty figures.' );
expect( 100 === substr_count( Timeline\render( array_fill( 0, 110, array( 'title' => 'Item' ) ) ), '<li ' ), 'Timeline is unbounded.' );
expect( ! str_contains( Timeline\render( array( array( 'image_id' => 7 ) ) ), 'rdct-timeline__content' ), 'Image-only milestone has an empty text wrapper.' );
$assets = \RadmanCTComponents\Components\requirements( 'timeline', array( 'style' => 'bad' ) );
expect( array( 'timeline', 'timeline-alternating' ) === $assets['styles'] && ! $assets['script'], 'Timeline loaded unrelated features or frontend JavaScript.' );
echo "Passed: timeline semantics, chronological DOM, local native media, bounded options/items, machine dates, minimal wrappers and isolated no-JS assets.\n";
