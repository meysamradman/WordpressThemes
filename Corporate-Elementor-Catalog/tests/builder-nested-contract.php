<?php

require __DIR__ . '/builder-visuals-contract.php';

$nested = $visual;
$nested['rows'][0]['items'] = array( array(
	'id' => 'brand-group', 'type' => 'container',
	'appearance' => array( 'width' => 'custom', 'customWidth' => 35, 'mobileWidth' => 'custom', 'mobileCustomWidth' => 70 ),
	'layout' => array( 'rowLayout' => 'flex', 'wrap' => 'nowrap', 'gap' => 12, 'padding' => 0, 'mobileLayout' => 'inline' ),
	'items' => $visual['rows'][0]['items'],
) );
$clean = \RadmanCT\Builder\validate( $nested );
expect( null !== $clean, 'Nested group did not validate.' );
$html = render_visual( $clean, true );
foreach ( array( 'data-builder-preview-row="brand-group"', 'data-builder-preview-item="visual-logo"', '--builder-custom-width:35%', '--builder-mobile-custom-width:70%', '--builder-wrap:nowrap' ) as $part ) {
	expect( str_contains( $html, $part ), 'Missing nested render setting: ' . $part );
}
expect( ! str_contains( render_visual( $clean, false ), 'data-builder-preview-' ), 'Editor markers leaked to frontend.' );
expect( array( 'brand-group', 'visual-logo', 'visual-button' ) === array_column( iterator_to_array( \RadmanCT\Builder\Tree\items( $clean['rows'] ), false ), 'id' ), 'Nested asset traversal missed children.' );
$bad = $nested;
$bad['rows'][0]['items'][0]['items'][1]['id'] = 'brand-group';
expect( null === \RadmanCT\Builder\validate( $bad ), 'Duplicate IDs across nesting were accepted.' );
foreach ( array( -1, 0, 101, '50', 50.5 ) as $width ) {
	$bad = $nested; $bad['rows'][0]['items'][0]['appearance']['customWidth'] = $width;
	expect( null === \RadmanCT\Builder\validate( $bad ), 'Invalid width accepted.' );
}
$bad = $nested;
$bad['rows'][0]['items'][0]['items'] = array( array( 'id' => 'level-two', 'type' => 'container', 'items' => array( array( 'id' => 'level-three', 'type' => 'container', 'items' => array() ) ) ) );
expect( null === \RadmanCT\Builder\validate( $bad ), 'Excess nesting was accepted.' );
echo "Passed: nested schema/rendering, responsive widths, traversal and identity/depth bounds.\n";
