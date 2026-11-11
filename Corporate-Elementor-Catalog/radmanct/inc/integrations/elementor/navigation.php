<?php

namespace RadmanCT\Integrations\Elementor\Navigation;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 2 ) . '/navigation/component.php';

function fields(): array {
	$fields = array(
		'menuId' => array( 'label' => __( 'Menu', 'radmanct' ), 'default' => 0, 'choices' => 'menus' ),
		'mobileMenuId' => array( 'label' => __( 'Mobile menu', 'radmanct' ), 'default' => 0, 'choices' => 'menus' ),
		'mobileDisplay' => array( 'label' => __( 'Mobile menu display', 'radmanct' ), 'default' => 'dropdown', 'options' => array( 'dropdown' => __( 'Dropdown', 'radmanct' ), 'drawer' => __( 'Sidebar drawer', 'radmanct' ), 'expanded' => __( 'Expanded', 'radmanct' ) ) ),
		'menuLayout' => array( 'label' => __( 'Menu orientation', 'radmanct' ), 'default' => 'horizontal', 'options' => array( 'horizontal' => __( 'Horizontal', 'radmanct' ), 'vertical' => __( 'Vertical', 'radmanct' ) ) ),
		'submenuStyle' => array( 'label' => __( 'Submenu type', 'radmanct' ), 'default' => 'dropdown', 'options' => array( 'dropdown' => __( 'Dropdown', 'radmanct' ), 'mega' => __( 'Mega menu', 'radmanct' ) ) ),
		'submenuTrigger' => array( 'label' => __( 'Submenu opens with', 'radmanct' ), 'default' => 'click', 'options' => array( 'click' => __( 'Click', 'radmanct' ), 'hover' => __( 'Hover and click', 'radmanct' ) ) ),
		'menuSkin' => array( 'label' => __( 'Menu appearance', 'radmanct' ), 'default' => 'plain', 'options' => \RadmanCT\Navigation\Presentation\skins() ),
		'megaColumns' => array( 'label' => __( 'Mega menu columns', 'radmanct' ), 'default' => 3, 'min' => 2, 'max' => 6 ),
		'megaWidth' => array( 'label' => __( 'Mega menu width', 'radmanct' ), 'default' => 960, 'min' => 320, 'max' => 1440 ),
		'drawerSide' => array( 'label' => __( 'Drawer side (follows RTL/LTR)', 'radmanct' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct' ), 'end' => __( 'End', 'radmanct' ) ) ),
		'drawerDesktop' => array( 'label' => __( 'Panel on desktop', 'radmanct' ), 'default' => false ),
		'drawerLaptop' => array( 'label' => __( 'Panel on laptop', 'radmanct' ), 'default' => false ),
		'drawerTablet' => array( 'label' => __( 'Panel on tablet', 'radmanct' ), 'default' => false ),
	);
	$fields += \RadmanCT\Navigation\Presentation\fields();
	$fields += \RadmanCT\Navigation\Presentation\submenu_fields();
	$fields += \RadmanCT\Navigation\Hover\fields();
	foreach ( $fields as $key => &$field ) { if ( 'submenu' === ( $field['group'] ?? '' ) ) { $field['group'] = 'style'; } $field['group'] ??= in_array( $key, array( 'menuSkin', 'megaColumns', 'megaWidth' ), true ) ? 'style' : ( str_starts_with( $key, 'panel' ) || str_starts_with( $key, 'drawer' ) || 'triggerLabel' === $key ? 'panel' : 'menu' ); }
	unset( $field );
	return $fields;
}

function choices( string $type ): array {
	if ( 'templates' === $type ) { require_once dirname( __DIR__, 2 ) . '/navigation/templates.php'; return \RadmanCT\Navigation\Templates\options(); }
	if ( 'blocks' === $type ) { return \RadmanCT\Navigation\Presentation\blocks(); }
	static $menus;
	if ( null === $menus ) {
		$menus = array( 0 => __( 'Use menu location', 'radmanct' ) );
		foreach ( wp_get_nav_menus( array( 'number' => 100, 'orderby' => 'name' ) ) as $menu ) { $menus[ (int) $menu->term_id ] = $menu->name; }
	}
	return $menus;
}

function field_choices( string $key, array $field ): array {
	$result = choices( $field['choices'] );
	if ( 'mobileMenuId' === $key ) { $result[0] = __( 'Use the desktop menu', 'radmanct' ); }
	return $result;
}

function options( array $settings ): ?array {
	$result = array(); $devices = array();
	foreach ( fields() as $key => $field ) {
		$value = $settings[ $key ] ?? $field['default'];
		if ( is_array( $value ) && isset( $value['$$type'] ) ) { $value = ! empty( $value['disabled'] ) ? $field['default'] : ( $value['value'] ?? $field['default'] ); }
		if ( is_bool( $field['default'] ) ) {
			if ( true === $value || 'yes' === $value ) { $devices[] = strtolower( substr( $key, 6 ) ); }
		} else {
			if ( is_int( $field['default'] ) ) {
				if ( ! is_numeric( $value ) || ! is_finite( (float) $value ) || floor( (float) $value ) !== (float) $value || $value < ( $field['min'] ?? 0 ) || $value > ( $field['max'] ?? PHP_INT_MAX ) ) { return null; }
				$value = (int) $value;
			}
			$result[ $key ] = $value;
		}
	}
	if ( isset( $settings['panelImage']['id'] ) ) { $result['panelImageId'] = (int) $settings['panelImage']['id']; }
	$result['drawerDevices'] = $devices;
	$options = \RadmanCT\Navigation\Component\settings( $result );
	return null === $options || $result['menuId'] < 0 ? null : $options + array( 'menuId' => $result['menuId'] );
}

function render( array $settings, string $scope = '' ): void {
	$options = options( $settings );
	if ( null === $options ) { return; }
	if ( '' === $scope ) { rdct_menu( $options['menuId'], $options ); return; }
	if ( ! preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', $scope ) ) { return; }
	static $instance = 0;
	\RadmanCT\Navigation\Component\render( 'header', 'elementor-' . $scope . '-' . ++$instance, $options['menuId'], $options );
}

function styles( array $settings ): array {
	$options = options( $settings );
	if ( null === $options ) { return array(); }
	$definitions = \RadmanCT\Components\definitions();
	return array_map( static fn( $name ) => $definitions[ $name ]['handle'], \RadmanCT\Navigation\Component\asset_names( $options ) );
}

function scripts(): void {
	$entry = \RadmanCT\Assets\entry( 'src/js/integrations/elementor-navigation.js' );
	if ( $entry ) { wp_register_script( 'rdct-elementor-navigation', \RadmanCT\Assets\url( $entry['file'] ), array(), null, true ); }
}

function register( $manager ): void {
	require_once __DIR__ . '/navigation/classic.php';
	$manager->register( new Classic() );
	require_once __DIR__ . '/navigation/panel.php';
	$manager->register( new PanelClassic() );
	if ( \RadmanCT\Integrations\Elementor\Atomic\enabled() ) { require_once __DIR__ . '/navigation/atomic.php'; $manager->register( new Atomic() ); require_once __DIR__ . '/navigation/panel-atomic.php'; $manager->register( new PanelAtomic() ); }
}

add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\scripts', 9 );
add_filter( 'wp_script_attributes', static function ( array $attributes ): array {
	if ( in_array( $attributes['id'] ?? '', array( 'rdct-elementor-navigation-js', 'rdct-navigation-controls-js' ), true ) ) { $attributes['type'] = 'module'; }
	return $attributes;
} );

require_once __DIR__ . '/navigation/preview.php';
