<?php

namespace RadmanCT\Admin\Typography;

defined( 'ABSPATH' ) || exit;

function css(): string {
	$font = \RadmanCT\Fonts\registry()['peyda']['files'][0];
	$url = \RadmanCT\Typography\file_url( $font[0] );
	return '@font-face{font-family:"Radman CT Admin Peyda";src:url("' . $url . '") format("woff2");font-weight:' . $font[1] . ';font-style:normal;font-display:swap;unicode-range:U+0600-06FF,U+0750-077F,U+08A0-08FF,U+200C-200F,U+FB50-FDFF,U+FE70-FEFF;}';
}

function enqueue(): void {
	foreach ( array( 'rdct-admin-settings', 'rdct-admin-builder', 'rdct-admin-requirements', 'rdct-core-settings' ) as $handle ) {
		if ( wp_style_is( $handle, 'enqueued' ) ) {
			if ( \RadmanCT\Assets\style( 'rdct-admin-typography', 'src/css/admin/typography.css', 'parent', array( $handle ) ) ) {
				wp_add_inline_style( 'rdct-admin-typography', css() );
			}
			return;
		}
	}
}

function body_class( string $classes ): string {
	return $classes . ( wp_style_is( 'rdct-admin-typography', 'enqueued' ) ? ' rdct-adm-interface' : '' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
add_filter( 'admin_body_class', __NAMESPACE__ . '\\body_class' );
