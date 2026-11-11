<?php

namespace RadmanCT\Navigation\Presentation;

defined( 'ABSPATH' ) || exit;

function submenu_fields(): array {
	return array(
		'submenuBackground' => array( 'group' => 'submenu', 'label' => __( 'Submenu background', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true ),
		'submenuColor' => array( 'group' => 'submenu', 'label' => __( 'Submenu text color', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true ),
		'submenuHoverColor' => array( 'group' => 'submenu', 'label' => __( 'Submenu hover color', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true ),
		'submenuDuration' => array( 'group' => 'submenu', 'label' => __( 'Submenu animation duration (ms)', 'radmanct' ), 'default' => 500, 'min' => 0, 'max' => 800 ),
	);
}

function fields(): array {
	require_once dirname( __DIR__ ) . '/icons.php';
	return array(
		'panelCloseIcon' => array( 'group' => 'style', 'label' => __( 'Close button icon', 'radmanct' ), 'default' => 'x', 'icon' => true, 'options' => \RadmanCT\Icons\labels() ),
		'panelCloseStyle' => array( 'group' => 'style', 'label' => __( 'Close button appearance', 'radmanct' ), 'default' => 'plain', 'options' => array( 'plain' => __( 'Plain', 'radmanct' ), 'circle' => __( 'Circle outline', 'radmanct' ) ) ),
		'panelCloseColor' => array( 'group' => 'style', 'label' => __( 'Close icon color', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true ),
		'panelCloseHoverColor' => array( 'group' => 'style', 'label' => __( 'Close icon hover color', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true ),
		'panelCloseBorderColor' => array( 'group' => 'style', 'label' => __( 'Close circle color', 'radmanct' ), 'default' => '', 'color' => true, 'inheritColor' => true, 'condition' => array( 'panelCloseStyle' => array( 'circle' ) ) ),
		'panelMotion' => array( 'label' => __( 'Panel opening effect', 'radmanct' ), 'default' => 'standard', 'options' => array( 'standard' => __( 'Standard', 'radmanct' ), 'curtain' => __( 'Curved curtain', 'radmanct' ), 'wave' => __( 'Wave cover and reveal', 'radmanct' ) ) ),
		'panelLinkLayout' => array( 'label' => __( 'Full screen menu links', 'radmanct' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Automatic', 'radmanct' ), 'vertical' => __( 'Vertical', 'radmanct' ), 'horizontal' => __( 'Horizontal', 'radmanct' ) ), 'condition' => array( 'panelMode' => array( 'fullscreen' ) ) ),
		'panelMotionDuration' => array( 'group' => 'style', 'label' => __( 'Panel effect duration (milliseconds)', 'radmanct' ), 'default' => 800, 'min' => 200, 'max' => 2000, 'condition' => array( 'panelMotion' => array( 'curtain', 'wave' ) ) ),
		'panelMotionColor' => array( 'group' => 'style', 'label' => __( 'Panel transition color', 'radmanct' ), 'default' => '#390507', 'color' => true, 'condition' => array( 'panelMotion' => array( 'wave' ) ) ),
		'panelMode' => array( 'label' => __( 'Panel layout', 'radmanct' ), 'default' => 'sidebar', 'options' => array( 'sidebar' => __( 'Sidebar', 'radmanct' ), 'fullscreen' => __( 'Full screen', 'radmanct' ), 'split' => __( 'Menu and content columns', 'radmanct' ) ) ),
		'panelBlockId' => array( 'label' => __( 'Panel content block', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'choices' => 'blocks' ),
		'panelHeaderBlockId' => array( 'label' => __( 'Panel header block', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX, 'choices' => 'blocks' ),
		'panelImageId' => array( 'label' => __( 'Panel background image', 'radmanct' ), 'default' => 0, 'min' => 0, 'max' => PHP_INT_MAX ),
		'panelWidth' => array( 'group' => 'style', 'label' => __( 'Sidebar width', 'radmanct' ), 'default' => 420, 'min' => 280, 'max' => 960, 'condition' => array( 'panelMode' => array( 'sidebar' ) ) ),
		'panelFontSize' => array( 'group' => 'style', 'label' => __( 'Panel link size', 'radmanct' ), 'default' => 48, 'min' => 16, 'max' => 128 ),
		'panelMobileFontSize' => array( 'group' => 'style', 'label' => __( 'Panel mobile link size', 'radmanct' ), 'default' => 30, 'min' => 16, 'max' => 80 ),
		'panelBackground' => array( 'group' => 'style', 'label' => __( 'Panel background', 'radmanct' ), 'default' => '#ffffff', 'color' => true ),
		'panelColor' => array( 'group' => 'style', 'label' => __( 'Panel text color', 'radmanct' ), 'default' => '#172b3a', 'color' => true ),
		'triggerLabel' => array( 'label' => __( 'Menu button label', 'radmanct' ), 'default' => '' ),
	);
}

function skins(): array {
	return array( 'plain' => __( 'Plain', 'radmanct' ), 'soft' => __( 'Soft highlight', 'radmanct' ), 'underline' => __( 'Underline', 'radmanct' ), 'line-fill' => __( 'Animated underline', 'radmanct' ), 'color' => __( 'Hover color', 'radmanct' ), 'pill' => __( 'Rounded highlight', 'radmanct' ) );
}

function validate( array $input, ?array $fields = null ): ?array {
	$result = array();
	foreach ( $fields ?? fields() as $key => $field ) {
		$value = $input[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { if ( ! is_string( $value ) || ! isset( $field['options'][ $value ] ) ) { return null; } }
		elseif ( isset( $field['min'] ) ) { if ( ! is_int( $value ) || $value < $field['min'] || $value > $field['max'] ) { return null; } }
		elseif ( ! empty( $field['color'] ) ) { if ( ! is_string( $value ) || ( ! ( '' === $value && ! empty( $field['inheritColor'] ) ) && ! preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ) ) { return null; } }
		elseif ( ! is_string( $value ) || strlen( $value ) > 120 ) { return null; }
		$result[ $key ] = 'triggerLabel' === $key ? sanitize_text_field( $value ) : $value;
	}
	return $result;
}

function blocks(): array {
	static $cache;
	if ( is_array( $cache ) ) { return $cache; }
	require_once dirname( __DIR__ ) . '/content-blocks.php';
	$posts = get_posts( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false, 'update_post_meta_cache' => false, 'update_post_term_cache' => false ) );
	$result = array( 0 => __( 'None', 'radmanct' ) );
	foreach ( $posts as $post ) { if ( '' === $post->post_password && null !== \RadmanCT\ContentBlocks\inspect( (int) $post->ID ) ) { $result[ (int) $post->ID ] = $post->post_title ?: '#' . $post->ID; } }
	return $cache = $result;
}

function style( array $options ): string {
	$css = '--rdct-panel-bg:' . $options['panelBackground'] . ';--rdct-panel-color:' . $options['panelColor'] . ';--rdct-panel-width:' . $options['panelWidth'] . 'px;--rdct-panel-font:' . $options['panelFontSize'] . 'px;--rdct-panel-mobile-font:' . $options['panelMobileFontSize'] . 'px;';
	$css .= '--rdct-panel-close-color:' . ( $options['panelCloseColor'] ?: $options['panelColor'] ) . ';--rdct-panel-close-hover:' . ( $options['panelCloseHoverColor'] ?: ( $options['panelCloseColor'] ?: $options['panelColor'] ) ) . ';--rdct-panel-close-border:' . ( 'circle' === $options['panelCloseStyle'] ? ( $options['panelCloseBorderColor'] ?: ( $options['panelCloseColor'] ?: $options['panelColor'] ) ) : 'transparent' ) . ';';
	$url = $options['panelImageId'] ? wp_get_attachment_image_url( $options['panelImageId'], 'large' ) : false;
	return $url ? $css . '--rdct-panel-image:url("' . esc_url( $url ) . '");' : $css;
}

function close_markup( array $options ): string {
	return '<template data-rdct-panel-close>' . \RadmanCT\Icons\render( $options['panelCloseIcon'], array( 'size' => 24 ) ) . '</template>';
}

function components( array $options ): array {
	$result = array();
	$skin = $options['menuSkin'] ?? 'plain';
	if ( in_array( $skin, array( 'line-fill', 'color', 'pill' ), true ) ) { $result[] = 'menu-skin-' . $skin; }
	if ( ! empty( $options['drawerDevices'] ) || 'drawer' === ( $options['mobileDisplay'] ?? '' ) ) {
		$result[] = 'menu-panel-' . ( $options['panelMode'] ?? 'sidebar' );
		$motion = $options['panelMotion'] ?? 'standard';
		if ( in_array( $motion, array( 'curtain', 'wave' ), true ) ) { $result[] = 'menu-panel-motion'; }
		if ( 'wave' === $motion ) { $result[] = 'menu-panel-wave'; }
	}
	return $result;
}

function submenu_style( array $options ): string {
	$css = '';
	foreach ( array( 'submenuBackground' => 'background', 'submenuColor' => 'color', 'submenuHoverColor' => 'hover' ) as $key => $property ) {
		$value = $options[ $key ] ?? '';
		if ( is_string( $value ) && preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$/iD', $value ) ) { $css .= '--rdct-submenu-' . $property . ':' . $value . ';'; }
	}
	$duration = $options['submenuDuration'] ?? 500;
	if ( is_int( $duration ) && $duration >= 0 && $duration <= 800 ) { $css .= '--rdct-submenu-duration:' . $duration . 'ms;'; }
	return $css;
}
