<?php

define( 'ABSPATH', '/' );
$hooks = $styles = $scripts = $enqueued = $inline = array();
function add_action( $name, $callback, $priority = 10 ) { $GLOBALS['hooks'][ $name ][ $priority ][] = $callback; }
function add_filter( ...$args ) { add_action( ...$args ); }
function apply_filters( $name, $value ) { foreach ( $GLOBALS['hooks'][ $name ] ?? array() as $callbacks ) { foreach ( $callbacks as $callback ) { $value = $callback( $value ); } } return $value; }
function plugins_url( $path, $file ) { return 'https://example.test/plugins/' . basename( dirname( $file ) ) . '/' . $path; }
function is_admin() { return false; }
function did_action( $name ) { return 0; }
function __( $text, $domain ) { return $text; }
function wp_register_style( $handle, $src, $deps = array() ) { $GLOBALS['styles'][ $handle ] = array( $src, $deps ); }
function wp_register_script( $handle, $src, $deps = array() ) { $GLOBALS['scripts'][ $handle ] = array( $src, $deps ); }
function wp_enqueue_style( $handle ) { $GLOBALS['enqueued'][ $handle ] = true; }
function wp_enqueue_script( $handle ) { $GLOBALS['enqueued'][ $handle ] = true; }
function wp_style_is( $handle, $state ) { return isset( $GLOBALS['styles'][ $handle ] ); }
function wp_script_is( $handle, $state ) { return isset( $GLOBALS['scripts'][ $handle ] ); }
function wp_add_inline_script( $handle, $source ) { $GLOBALS['inline'][ $handle ][] = $source; }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require dirname( __DIR__ ) . '/radmanct-components/radmanct-components.php';
expect( ! $enqueued && ! class_exists( '\Elementor\Widget_Base', false ), 'Plugin depends on Elementor or eagerly enqueues assets.' );
expect( ! function_exists( '\RadmanCTComponents\Components\TextReveal\render' ), 'Unused component engine loaded.' );
\RadmanCTComponents\Components\register_assets();
expect( ! $enqueued && isset( $styles['rdct-components-base'], $scripts['rdct-elementor-components'] ), 'Registration must not enqueue components.' );
expect( str_contains( $styles['rdct-curtain'][0], '/plugins/radmanct-components/dist/' ), 'Plugin asset URL depends on theme.' );
rdct_use( '../../invalid' );
expect( ! $enqueued, 'Unknown asset accepted.' );
ob_start(); rdct_text_reveal( 'A readable title', array( 'mode' => 'hover' ) ); $html = ob_get_clean();
expect( str_contains( $html, 'A readable title' ) && isset( $enqueued['rdct-text-reveal'] ) && ! isset( $enqueued['rdct-components'] ), 'Static PHP component requested animation runtime.' );
ob_start(); rdct_text_reveal( 'Animated title', array( 'mode' => 'scroll' ) ); ob_end_clean();
expect( isset( $enqueued['rdct-components'] ) && count( $inline['rdct-components'] ) === 1, 'Animation config missing or duplicated.' );
function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://example.test/themes/radmanct'; }
require dirname( __DIR__ ) . '/radmanct/inc/assets.php';
require dirname( __DIR__ ) . '/radmanct/inc/components.php';
require dirname( __DIR__ ) . '/radmanct/inc/template-functions.php';
require dirname( __DIR__ ) . '/radmanct/inc/icons.php';
require dirname( __DIR__ ) . '/radmanct/inc/integrations/components.php';
rdct_use( 'menu-dropdown' );
expect( isset( $enqueued['rdct-menu-dropdown'] ), 'Plugin helper no longer delegates theme navigation assets.' );
expect( str_contains( \RadmanCT\Icons\render( 'phone' ), '<svg' ), 'Theme icon adapter failed.' );
expect( ! isset( \RadmanCT\Components\definitions()['text-reveal'] ), 'Theme still owns duplicate component runtime.' );
$vendor = apply_filters( 'radmanct_components/animation_url', 'fallback' );
expect( str_contains( $vendor, '/themes/radmanct/dist/js/animation-vendor-' ), 'Theme GSAP sharing failed.' );
echo "Passed: standalone PHP plugin, optional Elementor, lazy engines/assets, stable helpers, theme coexistence and shared animation URL.\n";
