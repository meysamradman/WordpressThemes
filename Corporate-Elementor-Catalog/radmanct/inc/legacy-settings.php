<?php

namespace RadmanCT\LegacySettings;

defined( 'ABSPATH' ) || exit;

function inherit(): void {
	$stylesheet = get_stylesheet();
	$previous = array( 'radmanct' => 'radman' );
	if ( ! isset( $previous[ $stylesheet ] ) || get_option( 'rdct_settings_inherited_' . $stylesheet, false ) ) {
		return;
	}
	$old = get_option( 'theme_mods_' . $previous[ $stylesheet ], array() );
	$current = get_option( 'theme_mods_' . $stylesheet, array() );
	if ( is_array( $old ) && $old ) {
		$merged = array_replace( $old, is_array( $current ) ? $current : array() );
		if ( ! update_option( 'theme_mods_' . $stylesheet, $merged ) && get_option( 'theme_mods_' . $stylesheet ) !== $merged ) {
			return;
		}
	}
	update_option( 'rdct_settings_inherited_' . $stylesheet, true, false );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\\inherit', -100 );
