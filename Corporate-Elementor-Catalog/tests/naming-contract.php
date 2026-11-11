<?php

define( 'ABSPATH', __DIR__ );
$options = array();
$stylesheet = 'radmanct';
function add_action( ...$args ) {}
function get_stylesheet() { return $GLOBALS['stylesheet']; }
function get_option( $key, $default = false ) { return $GLOBALS['options'][ $key ] ?? $default; }
function update_option( $key, $value, ...$args ) { $GLOBALS['options'][ $key ] = $value; }
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
require __DIR__ . '/../radmanct/inc/legacy-settings.php';
$options['theme_mods_radman'] = array( 'custom_logo' => 8, 'nav_menu_locations' => array( 'primary' => 4 ), 'radman_builder_header' => true );
$options['theme_mods_radmanct'] = array( 'custom_logo' => 12 );
\RadmanCT\LegacySettings\inherit();
expect( 12 === $options['theme_mods_radmanct']['custom_logo'], 'New settings must win.' );
expect( 4 === $options['theme_mods_radmanct']['nav_menu_locations']['primary'], 'Preserve menu assignments.' );
expect( true === $options['theme_mods_radmanct']['radman_builder_header'], 'Preserve builder enablement.' );
unset( $options['theme_mods_radmanct']['radman_builder_header'] );
\RadmanCT\LegacySettings\inherit();
expect( ! isset( $options['theme_mods_radmanct']['radman_builder_header'] ), 'Never reintroduce intentionally removed settings.' );
$stylesheet = 'radmanct-child';
$options['theme_mods_radman-client'] = array( 'custom_logo' => 30 );
\RadmanCT\LegacySettings\inherit();
expect( ! isset( $options['theme_mods_radmanct-child'] ), 'Fresh child must not migrate experimental identifiers.' );
expect( 8 === $options['theme_mods_radman']['custom_logo'], 'Original settings remain untouched.' );
echo "Naming compatibility contracts passed.\n";
