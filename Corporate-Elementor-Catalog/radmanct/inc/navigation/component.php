<?php

namespace RadmanCT\Navigation\Component;

defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__ ) . '/navigation.php';
require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/hover.php';

function settings( array $input ): ?array {
	$result = array( 'mobileMenuId' => $input['mobileMenuId'] ?? 0, 'mobileDisplay' => $input['mobileDisplay'] ?? 'dropdown', 'drawerSide' => $input['drawerSide'] ?? 'start', 'menuLayout' => $input['menuLayout'] ?? 'horizontal', 'submenuStyle' => $input['submenuStyle'] ?? 'dropdown', 'menuSkin' => $input['menuSkin'] ?? 'plain', 'megaColumns' => $input['megaColumns'] ?? 3, 'megaWidth' => $input['megaWidth'] ?? 960 );
	foreach ( array( 'menuGap', 'laptopMenuGap', 'tabletMenuGap', 'mobileMenuGap' ) as $key ) {
		$value = $input[ $key ] ?? null;
		if ( null !== $value && ( ! is_int( $value ) || $value < 0 || $value > 128 ) ) { return null; }
		$result[ $key ] = $value;
	}
	foreach ( array( 'mobileDisplay' => array( 'dropdown', 'drawer', 'expanded' ), 'drawerSide' => array( 'start', 'end' ), 'menuLayout' => array( 'horizontal', 'vertical' ), 'submenuStyle' => \RadmanCT\Navigation\SUBMENU_STYLES, 'menuSkin' => array_keys( \RadmanCT\Navigation\Presentation\skins() ) ) as $key => $allowed ) {
		if ( ! in_array( $result[ $key ], $allowed, true ) ) { return null; }
	}
	if ( ! is_int( $result['mobileMenuId'] ) || $result['mobileMenuId'] < 0 || ! is_int( $result['megaColumns'] ) || $result['megaColumns'] < 2 || $result['megaColumns'] > 6 || ! is_int( $result['megaWidth'] ) || $result['megaWidth'] < 320 || $result['megaWidth'] > 1440 ) { return null; }
	$devices = $input['drawerDevices'] ?? ( 'drawer' === $result['mobileDisplay'] ? array( 'mobile' ) : array() );
	if ( ! is_array( $devices ) || array_values( $devices ) !== $devices || count( $devices ) > 4 ) { return null; }
	foreach ( $devices as $device ) { if ( ! is_string( $device ) || ! in_array( $device, array( 'desktop', 'laptop', 'tablet', 'mobile' ), true ) ) { return null; } }
	$result['drawerDevices'] = array_values( array_unique( $devices ) );
	if ( 'drawer' === $result['mobileDisplay'] && ! in_array( 'mobile', $result['drawerDevices'], true ) ) { $result['drawerDevices'][] = 'mobile'; }
	if ( in_array( 'mobile', $result['drawerDevices'], true ) ) { $result['mobileDisplay'] = 'drawer'; }
	$presentation = \RadmanCT\Navigation\Presentation\validate( $input, \RadmanCT\Navigation\Presentation\fields() + \RadmanCT\Navigation\Presentation\submenu_fields() );
	$trigger = $input['submenuTrigger'] ?? 'click';
	if ( ! in_array( $trigger, array( 'click', 'hover' ), true ) ) { return null; }
	$result['submenuTrigger'] = $trigger;
	$hover = \RadmanCT\Navigation\Hover\settings( $input );
	return null === $presentation || null === $hover ? null : $result + $presentation + $hover;
}

function asset_names( array $options, string $kind = 'header' ): array {
	$names = array( 'menu-dropdown' );
	$mega = 'mega' === ( $options['submenuStyle'] ?? 'dropdown' );
	if ( ! $mega ) {
		$locations = get_nav_menu_locations();
		$menu_id = $options['menuId'] ?? 0;
		if ( ! $menu_id ) { $menu_id = (int) ( $locations[ 'header' === $kind ? 'primary' : 'footer' ] ?? 0 ); }
		foreach ( array_unique( array( $menu_id, $options['mobileMenuId'] ?? 0 ) ) as $candidate ) {
			if ( ! $candidate ) { continue; }
			$id = (int) apply_filters( 'wpml_object_id', $candidate, 'nav_menu', true );
			if ( \RadmanCT\Navigation\menu_has_mega( $id ) ) { $mega = true; break; }
		}
	}
	if ( $mega ) { $names[] = 'menu-mega'; }
	if ( ! empty( $options['drawerDevices'] ) || 'drawer' === ( $options['mobileDisplay'] ?? 'dropdown' ) ) { $names[] = 'menu-drawer'; }
	return array_merge( $names, \RadmanCT\Navigation\Presentation\components( $options ), \RadmanCT\Navigation\Hover\assets( $options ) );
}

function spacing_style( array $options ): string {
	$style = '';
	foreach ( array( 'desktop' => 'menuGap', 'laptop' => 'laptopMenuGap', 'tablet' => 'tabletMenuGap', 'mobile' => 'mobileMenuGap' ) as $device => $key ) {
		if ( isset( $options[ $key ] ) && is_int( $options[ $key ] ) && $options[ $key ] >= 0 && $options[ $key ] <= 128 ) { $style .= '--rdct-menu-gap-' . $device . ':' . $options[ $key ] . 'px;'; }
	}
	return $style;
}

function assets( array $options, string $kind = 'header' ): bool {
	$names = asset_names( $options, $kind );
	foreach ( $names as $name ) { \RadmanCT\Components\use_component( $name ); }
	return in_array( 'menu-mega', $names, true );
}

function render( string $kind, string $id, int $menu_id, array $options ): void {
	$options = settings( $options );
	if ( null === $options ) { return; }
	$mobile = $options['mobileMenuId'] ?? 0;
	$direction = 'vertical' === ( $options['menuLayout'] ?? 'horizontal' ) ? 'rdct-bld-menu--vertical' : '';
	if ( $mobile && $mobile !== $menu_id ) {
		render_one( $kind, $id . '-desktop', $menu_id, 'dropdown', $options['drawerSide'], 'rdct-bld--desktop ' . $direction, array_replace( $options, array( 'drawerDevices' => array_values( array_diff( $options['drawerDevices'], array( 'mobile' ) ) ) ) ) );
		render_one( $kind, $id . '-mobile', $mobile, $options['mobileDisplay'], $options['drawerSide'], 'rdct-bld--mobile ' . $direction, array_replace( $options, array( 'drawerDevices' => array_values( array_intersect( $options['drawerDevices'], array( 'mobile' ) ) ) ) ) );
		return;
	}
	render_one( $kind, $id, $menu_id, $options['mobileDisplay'], $options['drawerSide'], $direction, $options );
}

function render_one( string $kind, string $id, int $menu_id, string $display, string $side, string $class = '', array $options = array() ): void {
	$location = 'header' === $kind ? 'primary' : 'footer';
	if ( $menu_id ) {
		$menu_id = (int) apply_filters( 'wpml_object_id', $menu_id, 'nav_menu', true );
		if ( ! $menu_id || ! wp_get_nav_menu_object( $menu_id ) ) { return; }
	} elseif ( ! has_nav_menu( $location ) ) { return; }
	$dom_id = 'rdct-' . $kind . '-' . $id;
	ob_start();
	try {
		\RadmanCT\Navigation\menu( array( 'menu' => $menu_id, 'theme_location' => $menu_id ? '' : $location, 'container' => false, 'menu_class' => 'rdct-menu' . ( 'mega' === ( $options['submenuStyle'] ?? '' ) ? ' rdct-menu--mega' : '' ), 'rdct_menu_hover' => $options['menuHover'] ?? 'none', 'rdct_submenu_style' => $options['submenuStyle'] ?? 'dropdown', 'fallback_cb' => false, 'depth' => 0, 'menu_id' => $dom_id . '-list' ) );
		$markup = (string) ob_get_contents();
	} finally { ob_end_clean(); }
	if ( '' === trim( $markup ) ) { return; }
	$mega = assets( array_replace( $options, array( 'menuId' => $menu_id, 'mobileMenuId' => 0 ) ), $kind );
	foreach ( array( 'menu-dropdown', 'menu-mega', 'menu-drawer' ) as $component ) {
		if ( 'menu-mega' === $component && ! $mega ) { continue; }
		if ( 'menu-drawer' === $component && empty( $options['drawerDevices'] ) && 'drawer' !== $display ) { continue; }
		\RadmanCT\Components\print_late_style( $component );
	}
	foreach ( array_merge( \RadmanCT\Navigation\Presentation\components( $options ), \RadmanCT\Navigation\Hover\assets( $options ) ) as $component ) { \RadmanCT\Components\print_late_style( $component ); }
	$label = 'header' === $kind ? __( 'Primary menu', 'radmanct' ) : __( 'Footer menu', 'radmanct' );
	$devices = $options['drawerDevices'] ?? ( 'drawer' === $display ? array( 'mobile' ) : array() );
	$drawer = $devices ? ' data-panel-link-layout="' . esc_attr( $options['panelLinkLayout'] ) . '" data-panel-motion="' . esc_attr( $options['panelMotion'] ) . '" data-panel-duration="' . (int) $options['panelMotionDuration'] . '" data-panel-motion-color="' . esc_attr( $options['panelMotionColor'] ) . '" data-panel-mode="' . esc_attr( $options['panelMode'] ) . '" data-rdct-drawer data-drawer-devices="' . esc_attr( implode( ' ', $devices ) ) . '" data-drawer-side="' . esc_attr( $side ) . '" data-close-label="' . esc_attr__( 'Close menu', 'radmanct' ) . '"' : '';
	foreach ( $devices as $device ) { $class .= ' rdct-menu--drawer-' . $device; }
	$class .= ' rdct-menu--skin-' . ( $options['menuSkin'] ?? 'plain' );
	$class .= ' rdct-menu--indicator-' . ( $options['menuIndicator'] ?? 'none' );
	$drawer .= ' data-menu-hover="' . esc_attr( $options['menuHover'] ?? 'none' ) . '"';
	if ( 'expanded' === $display ) { $drawer .= ' data-mobile-expanded'; }
	$drawer .= ' data-submenu-trigger="' . esc_attr( $options['submenuTrigger'] ) . '"';
	$spacing = spacing_style( $options );
	if ( '' !== $spacing ) { $drawer .= ' data-menu-gap'; }
	echo '<div class="' . esc_attr( $class ) . '" data-rdct-menu' . $drawer . ' data-submenu-style="' . esc_attr( $options['submenuStyle'] ?? 'dropdown' ) . '" style="--rdct-mega-columns:' . (int) ( $options['megaColumns'] ?? 3 ) . ';--rdct-mega-width:' . (int) ( $options['megaWidth'] ?? 960 ) . 'px;' . esc_attr( $spacing . \RadmanCT\Navigation\Presentation\submenu_style( $options ) . ( $devices ? \RadmanCT\Navigation\Presentation\style( $options ) : '' ) ) . '" data-submenu-label="' . esc_attr__( 'Toggle submenu', 'radmanct' ) . '"><button class="rdct-menu-toggle" type="button" aria-expanded="false" aria-controls="' . esc_attr( $dom_id ) . '" hidden>' . esc_html( $options['triggerLabel'] ?: __( 'Menu', 'radmanct' ) ) . '</button><nav class="rdct-nav" id="' . esc_attr( $dom_id ) . '" aria-label="' . esc_attr( $label ) . '">';
	echo $markup;
	echo '</nav>';
	if ( $devices ) { echo \RadmanCT\Navigation\Presentation\close_markup( $options ); }
	foreach ( array( 'panelHeaderBlockId' => 'header', 'panelBlockId' => 'content' ) as $key => $area ) {
		if ( ! $devices || ! $options[ $key ] ) { continue; }
		require_once __DIR__ . '/blocks.php';
		echo '<div class="rdct-menu-panel-' . $area . '" hidden>' . \RadmanCT\Navigation\Blocks\render( $options[ $key ], $dom_id . '-panel-' . $area . '-' ) . '</div>';
	}
	echo '</div>';
}
