<?php

namespace RadmanCT\Navigation\Panel;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';

function fields(): array {
	require_once dirname( __DIR__ ) . '/icons.php';
	$fields = array_diff_key( \RadmanCT\Navigation\Presentation\fields(), array_flip( array( 'panelHeaderBlockId', 'panelLinkLayout', 'panelFontSize', 'panelMobileFontSize' ) ) );
	$fields['panelBlockId']['condition'] = array( 'panelContentType' => array( 'block' ) );
	return array(
		'triggerStyle' => array( 'group' => 'style', 'label' => __( 'Panel button appearance', 'radmanct' ), 'default' => 'outline', 'options' => array( 'outline' => __( 'Outline', 'radmanct' ), 'plain' => __( 'Plain', 'radmanct' ), 'solid' => __( 'Solid', 'radmanct' ) ) ),
		'triggerIcon' => array( 'label' => __( 'Panel button icon', 'radmanct' ), 'default' => 'menu', 'icon' => true, 'options' => \RadmanCT\Icons\labels() ),
		'panelContentType' => array( 'label' => __( 'Panel content source', 'radmanct' ), 'default' => 'empty', 'options' => array( 'empty' => __( 'Empty', 'radmanct' ), 'block' => __( 'Reusable block', 'radmanct' ), 'elementor' => __( 'Elementor template', 'radmanct' ) ) ),
		'panelTemplateId' => array( 'label' => __( 'Panel template', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'choices' => 'templates', 'condition' => array( 'panelContentType' => array( 'elementor' ) ) ),
		'drawerSide' => array( 'label' => __( 'Drawer side (follows RTL/LTR)', 'radmanct' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct' ), 'end' => __( 'End', 'radmanct' ) ) ),
	) + $fields;
}

function settings( array $input ): ?array {
	if ( 'custom' === ( $input['panelContentType'] ?? '' ) ) { $input['panelContentType'] = 'empty'; }
	$result = \RadmanCT\Navigation\Presentation\validate( $input );
	if ( null === $result ) { return null; }
	foreach ( fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) && ( ! is_string( $value ) || ! isset( $field['options'][ $value ] ) ) ) { return null; }
		if ( isset( $field['min'] ) && ( ! is_int( $value ) || $value < $field['min'] || $value > $field['max'] ) ) { return null; }
		$result[ $key ] = $value;
	}
	$result['triggerLabel'] = sanitize_text_field( $result['triggerLabel'] );
	return $result;
}

function asset_names( array $options ): array {
	return array_merge( array( 'menu-drawer' ), \RadmanCT\Navigation\Presentation\components( $options + array( 'drawerDevices' => array( 'desktop', 'laptop', 'tablet', 'mobile' ) ) ) );
}

function assets( array $options ): void {
	foreach ( asset_names( $options ) as $name ) { \RadmanCT\Components\use_component( $name ); \RadmanCT\Components\print_late_style( $name ); }
}

function render( array $input = array(), $content = null, string $scope = '' ): void {
	$options = settings( $input );
	if ( null === $options || ( null !== $content && ! is_string( $content ) && ! is_callable( $content ) ) ) { return; }
	static $sequence = 0; static $depth = 0;
	if ( $depth >= 4 ) { return; }
	++$depth; $id = 'rdct-panel-' . ( preg_match( '/^[a-zA-Z0-9_-]{1,80}$/D', $scope ) ? $scope . '-' : '' ) . ++$sequence;
	try {
		if ( ! is_string( $content ) && is_callable( $content ) ) { ob_start(); try { $returned = $content(); $html = (string) ob_get_contents() . ( is_string( $returned ) ? $returned : '' ); } finally { ob_end_clean(); } }
		elseif ( is_string( $content ) ) { $html = $content; }
		elseif ( 'elementor' === $options['panelContentType'] ) { require_once __DIR__ . '/templates.php'; $html = \RadmanCT\Navigation\Templates\render( $options['panelTemplateId'] ); }
		elseif ( 'block' === $options['panelContentType'] ) { require_once __DIR__ . '/blocks.php'; $html = \RadmanCT\Navigation\Blocks\render( $options['panelBlockId'], $id ); }
		else { $html = ''; }
		assets( $options );
		$trigger = \RadmanCT\Icons\render( $options['triggerIcon'], array( 'size' => 24 ) ) . ( '' !== $options['triggerLabel'] ? '<span>' . esc_html( $options['triggerLabel'] ) . '</span>' : '' );
		$label = $options['triggerLabel'] ?: __( 'Open panel', 'radmanct' );
		echo '<div class="rdct-panel rdct-menu--drawer-desktop rdct-menu--drawer-laptop rdct-menu--drawer-tablet" data-rdct-menu data-panel-only data-rdct-drawer data-drawer-devices="desktop laptop tablet mobile" data-panel-mode="' . esc_attr( $options['panelMode'] ) . '" data-panel-motion="' . esc_attr( $options['panelMotion'] ) . '" data-panel-duration="' . (int) $options['panelMotionDuration'] . '" data-panel-motion-color="' . esc_attr( $options['panelMotionColor'] ) . '" data-drawer-side="' . esc_attr( $options['drawerSide'] ) . '" data-close-label="' . esc_attr__( 'Close panel', 'radmanct' ) . '" style="' . esc_attr( \RadmanCT\Navigation\Presentation\style( $options ) ) . '"><button class="rdct-menu-toggle rdct-menu-toggle--icon rdct-menu-toggle--' . esc_attr( $options['triggerStyle'] ) . '" type="button" aria-label="' . esc_attr( $label ) . '" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '" hidden>' . $trigger . '</button>' . \RadmanCT\Navigation\Presentation\close_markup( $options ) . '<div data-rdct-panel-body id="' . esc_attr( $id ) . '" aria-label="' . esc_attr( $label ) . '" hidden>' . $html . '</div></div>';
	} finally { --$depth; }
}
