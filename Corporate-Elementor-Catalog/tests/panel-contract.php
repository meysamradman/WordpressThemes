<?php

require __DIR__ . '/builder-menu-contract.php';
require dirname( __DIR__ ) . '/radmanct/inc/navigation/panel.php';

use RadmanCT\Navigation\Panel;

$calls = count( $menu_calls );
ob_start(); Panel\render(); $empty = ob_get_clean();
expect( str_contains( $empty, 'data-panel-only' ) && str_contains( $empty, 'data-rdct-panel-body' ) && preg_match( '/data-rdct-panel-body[^>]*><\/div>/', $empty ), 'New panel has default content or no independent body.' );
expect( ! str_contains( $empty, '<nav' ) && ! str_contains( $empty, '<ul' ) && count( $menu_calls ) === $calls, 'Empty panel rendered or queried a navigation menu.' );
preg_match( '/<button class="rdct-menu-toggle[^>]*>(.*?)<\/button>/s', $empty, $trigger );
expect( isset( $trigger[1] ) && '' === trim( strip_tags( $trigger[1] ) ) && str_contains( $trigger[1], '<svg' ) && str_contains( $empty, 'aria-label="Open panel"' ), 'Default panel trigger must have an icon and accessible name without visible fallback text.' );
$changed = Panel\settings( array( 'triggerIcon' => 'search', 'triggerLabel' => 'Explore', 'triggerStyle' => 'plain' ) );
expect( null !== $changed && 'search' === $changed['triggerIcon'], 'A local custom trigger icon was rejected.' );
expect( null === Panel\settings( array( 'triggerIcon' => 'https://remote/icon.svg' ) ), 'External trigger icon accepted.' );
$close = Panel\settings( array( 'panelCloseStyle' => 'circle', 'panelCloseIcon' => 'circle-x', 'panelCloseColor' => '#ffffff', 'panelCloseHoverColor' => '#ee8844', 'panelCloseBorderColor' => '#ffffff' ) );
expect( null !== $close && str_contains( \RadmanCT\Navigation\Presentation\close_markup( $close ), '<svg' ) && str_contains( \RadmanCT\Navigation\Presentation\style( $close ), '--rdct-panel-close-hover:#ee8844' ), 'Close icon and circle settings did not reach the renderer.' );
expect( null === Panel\settings( array( 'panelCloseIcon' => 'remote.svg' ) ) && null === Panel\settings( array( 'panelCloseColor' => 'url(remote)' ) ), 'Unsafe close icon or color accepted.' );
ob_start(); Panel\render( array( 'panelMotion' => 'wave', 'drawerSide' => 'end' ), static fn() => '<section class="client-design"><h2>Custom design</h2><button>Action</button></section>' ); $custom = ob_get_clean();
expect( str_contains( $custom, 'client-design' ) && str_contains( $custom, '<button>Action</button>' ) && str_contains( $custom, 'data-drawer-side="end"' ) && ! str_contains( $custom, '<nav' ), 'Trusted PHP panel content was replaced or coupled to a menu.' );
expect( array( 'menu-drawer', 'menu-panel-sidebar' ) === Panel\asset_names( Panel\settings( array() ) ), 'Independent panel requests dropdown or menu content assets.' );
expect( null === Panel\settings( array( 'panelTemplateId' => '7' ) ) && null === Panel\settings( array( 'panelContentType' => 'script' ) ), 'Panel source/id validation failed.' );
expect( str_contains( $empty, 'rdct-panel-1' ) && str_contains( $custom, 'rdct-panel-2' ), 'Independent panel IDs collided.' );
$layout = array( 'version' => 1, 'enabled' => true, 'rows' => array( array( 'id' => 'row-panel', 'items' => array( array( 'id' => 'item-panel', 'type' => 'panel', 'panelContentType' => 'empty', 'panelMotion' => 'curtain' ) ) ) ) );
$clean = \RadmanCT\Builder\validate( $layout );
expect( null !== $clean && 'empty' === $clean['rows'][0]['items'][0]['panelContentType'], 'Builder cannot store an empty independent panel.' );
echo "Passed: empty independent panels, arbitrary trusted PHP content, no menu fallback, scoped assets and builder storage.\n";
