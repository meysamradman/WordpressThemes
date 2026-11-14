<?php

defined( 'ABSPATH' ) || exit;

function rdct_theme_toggle( array $options = array() ): void { echo \RadmanCT\ColorScheme\toggle( $options ); }

function rdct_menu( int $menu_id = 0, array $options = array() ): void {
	if ( $menu_id < 0 ) { return; }
	static $instance = 0;
	require_once __DIR__ . '/navigation/component.php';
	\RadmanCT\Navigation\Component\render( 'header', 'custom-menu-' . ++$instance, $menu_id, $options );
}

function rdct_panel( $content = null, array $options = array() ): void {
	require_once __DIR__ . '/navigation/panel.php';
	\RadmanCT\Navigation\Panel\render( $options, $content );
}

function rdct_motion_attributes( array $options = array() ): void {
	require_once __DIR__ . '/components/container-motion.php';
	echo \RadmanCT\Components\ContainerMotion\attributes( $options );
}

if ( ! function_exists( 'rdct_use' ) ) {
 function rdct_use( string $component ): void { \RadmanCT\Components\use_component( $component ); }
	function rdct_icon( string $name, array $options = array() ): void {
		require_once __DIR__ . '/icons.php';
		$html = \RadmanCT\Icons\render( $name, $options );
		if ( '' !== $html ) {
			\RadmanCT\Components\use_component( 'icons' );
			\RadmanCT\Components\print_late_style( 'icons' );
			echo $html;
		}
	}
 function rdct_text_reveal( string $text, array $options = array() ): void { echo '<span>' . esc_html( $text ) . '</span>'; }
 function rdct_curtain( array $items, array $options = array() ): void {}
 function rdct_box( array $options ): void {}
 function rdct_flipbox( array $options ): void {}
 function rdct_icon_box( array $options ): void {}
 function rdct_image( array $options ): void {}
 function rdct_table( array $data, array $options = array() ): void {}
}

if ( ! function_exists( 'rdct_border_attributes' ) ) { function rdct_border_attributes( array $options = array() ): void {} }
