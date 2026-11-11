<?php

define( 'ABSPATH', '/' );
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
$options = array();
$hooks = array();
$styles = array();
$allowed = true;
$preview = false;
$cursor_allowed = true;
$progress_allowed = true;
$preloader_allowed = true;
$elementor_checks = 0;
$page = false;
$meta = array();
$meta_reads = 0;
$can_edit = true;
$toolbar = false;

function get_option( $key, $default = false ) { return $GLOBALS['options']; }
function is_admin() { return false; }
function is_admin_bar_showing() { return $GLOBALS['toolbar']; }
function is_singular( $type ) { return $GLOBALS['page']; }
function get_queried_object_id() { return 12; }
function get_post_meta( $id, $key, $single ) { ++$GLOBALS['meta_reads']; return $GLOBALS['meta'][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['meta'][ $key ] = $value; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['meta'][ $key ] ); }
function wp_is_post_revision( $id ) { return false; }
function wp_is_post_autosave( $id ) { return false; }
function wp_verify_nonce( $nonce, $action ) { return 'valid' === $nonce; }
function current_user_can( ...$args ) { return $GLOBALS['can_edit']; }
function is_customize_preview() { return $GLOBALS['preview']; }
function apply_filters( $hook, $value ) {
	$flags = array( 'radmanct/motion/allowed' => 'allowed', 'radmanct/cursor/allowed' => 'cursor_allowed', 'radmanct/scroll_progress/allowed' => 'progress_allowed', 'radmanct/preloader/allowed' => 'preloader_allowed' );
	return isset( $flags[ $hook ] ) ? $GLOBALS[ $flags[ $hook ] ] : $value;
}
function did_action( $hook ) { ++$GLOBALS['elementor_checks']; return false; }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function add_action( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ] = $callback; }
function add_filter( $hook, $callback, ...$args ) { $GLOBALS['hooks'][ $hook ] = $callback; }
function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://radman.test/wp-content/themes/radmanct'; }
function wp_enqueue_style( $handle, ...$args ) { $GLOBALS['styles'][] = $handle; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require dirname( __DIR__ ) . '/radmanct/inc/settings.php';
require dirname( __DIR__ ) . '/radmanct/inc/page-effects.php';
require dirname( __DIR__ ) . '/radmanct/inc/assets.php';
require dirname( __DIR__ ) . '/radmanct/inc/modules.php';
require dirname( __DIR__ ) . '/radmanct/inc/admin/page-effects.php';
$hooks = array();
\RadmanCT\Modules\register();
expect( ! function_exists( '\RadmanCT\Motion\classes' ) && $hooks === array(), 'Disabled motion loaded its PHP module or hooks.' );
expect( ! function_exists( '\RadmanCT\Cursor\markup' ) && ! function_exists( '\RadmanCT\ScrollProgress\markup' ) && ! function_exists( '\RadmanCT\Preloader\markup' ), 'Disabled effects loaded PHP modules.' );
expect( 0 === $elementor_checks, 'Disabled effects initialized Elementor checks.' );
expect( 0 === $meta_reads, 'A non-page request read page effect metadata.' );
$page = true;
$options = array_fill_keys( \RadmanCT\PageEffects\KEYS, true );
$meta['_radman_page_effects'] = array_fill_keys( \RadmanCT\PageEffects\KEYS, 'off' );
\RadmanCT\Modules\register();
expect( $hooks === array() && ! function_exists( '\RadmanCT\Motion\classes' ) && 0 === $elementor_checks, 'A disabled page loaded module code, hooks or editor checks.' );
expect( 1 === $meta_reads, 'Page overrides required unrelated metadata reads.' );
$resolved = \RadmanCT\PageEffects\resolve( \RadmanCT\Settings\defaults(), array( 'smooth_scroll' => 'on', 'custom_cursor' => 'invalid', 'foreign' => 'on' ) );
expect( true === $resolved['smooth_scroll'] && false === $resolved['custom_cursor'] && ! isset( $resolved['foreign'] ), 'Page overrides allowed unknown values or failed to enable a local module.' );
$page = false;
$options = array();
$_GET['elementor-preview'] = '12';
$options = array( 'custom_cursor' => true, 'scroll_progress' => true, 'preloader' => true );
\RadmanCT\Modules\register();
expect( $hooks === array(), 'Effects loaded into Elementor preview.' );
unset( $_GET['elementor-preview'] );
$cursor_allowed = $progress_allowed = $preloader_allowed = false;
\RadmanCT\Modules\register();
expect( $hooks === array(), 'Per-page effect opt-outs were ignored.' );
$options = array( 'smooth_scroll' => true );
$toolbar = true;
expect( \RadmanCT\PageEffects\values()['smooth_scroll'] && $options['smooth_scroll'], 'Visible toolbar suppressed selected smoothing.' );
$page = true;
$meta['_radman_page_effects'] = array( 'smooth_scroll' => 'on' );
expect( \RadmanCT\PageEffects\values()['smooth_scroll'], 'Page smoothing override was suppressed by the toolbar.' );
$page = false;
$toolbar = false;
expect( \RadmanCT\PageEffects\values()['smooth_scroll'], 'Hidden toolbar suppressed normal visitor smoothing.' );
$allowed = false;
\RadmanCT\Modules\register();
expect( ! function_exists( '\RadmanCT\Motion\classes' ), 'Per-page opt-out was ignored.' );
$allowed = true;
$preview = true;
\RadmanCT\Modules\register();
expect( ! function_exists( '\RadmanCT\Motion\classes' ), 'Motion loaded in Customizer preview.' );
$preview = false;
$page = true;
$options = array();
$meta['_radman_page_effects'] = array( 'scroll_reveal' => 'on' );
\RadmanCT\Modules\register();
expect( function_exists( '\RadmanCT\Motion\classes' ) && isset( $hooks['body_class'] ), 'Active module did not register.' );
expect( \RadmanCT\Motion\classes( array() ) === array( 'rdct-reveal-enabled' ), 'Disabled smooth scrolling produced an activation flag.' );
$page = false;
$options = array( 'scroll_reveal' => true, 'smooth_scroll' => false );
ob_start();
\RadmanCT\Motion\open();
\RadmanCT\Motion\close();
expect( ob_get_clean() === '', 'Disabled smoothing produced wrappers.' );
\RadmanCT\Motion\enqueue();
expect( $styles === array(), 'Disabled smoothing enqueued CSS.' );
$options['smooth_scroll'] = true;
$toolbar = true;
ob_start();
\RadmanCT\Motion\open();
\RadmanCT\Motion\close();
expect( str_contains( ob_get_clean(), 'data-smooth-duration="0.8"' ), 'Selected smoothing did not expose its validated duration with a toolbar.' );
expect( in_array( 'rdct-smooth-scroll-enabled', \RadmanCT\Motion\classes( array() ), true ), 'Toolbar prevented the selected frontend smooth-scroll import.' );
\RadmanCT\Motion\enqueue();
expect( array( 'rdct-smooth-scroll' ) === $styles, 'Toolbar prevented isolated smooth-scroll CSS.' );
$toolbar = false;
$styles = array();
\RadmanCT\Motion\enqueue();
expect( $styles === array( 'rdct-smooth-scroll' ), 'Active smooth-scroll CSS was not isolated.' );

$options = array( 'custom_cursor' => true, 'scroll_progress' => true, 'preloader' => true );
$cursor_allowed = $progress_allowed = $preloader_allowed = true;
$allowed = false;
\RadmanCT\Modules\register();
expect( function_exists( '\RadmanCT\Cursor\markup' ) && function_exists( '\RadmanCT\ScrollProgress\markup' ) && function_exists( '\RadmanCT\Preloader\markup' ), 'Independent effects did not activate when GSAP motion was disallowed.' );
\RadmanCT\Cursor\enqueue();
\RadmanCT\ScrollProgress\enqueue();
\RadmanCT\Preloader\enqueue();
expect( array_slice( $styles, 1 ) === array( 'rdct-cursor', 'rdct-scroll-progress', 'rdct-preloader' ), 'Effect styles were not isolated.' );
$_POST = array( 'radman_effects_nonce' => 'valid', 'radman_page_effects' => array( 'smooth_scroll' => 'on', 'preloader' => 'off' ) );
$meta = array();
\RadmanCT\Admin\PageEffects\save( 12 );
expect( $meta['_radman_page_effects'] === array( 'smooth_scroll' => 'on', 'preloader' => 'off' ), 'Valid page overrides did not save.' );
$_POST['radman_page_effects'] = array( 'smooth_scroll' => 'invalid', 'preloader' => '', 'unknown' => 'on' );
\RadmanCT\Admin\PageEffects\save( 12 );
expect( $meta['_radman_page_effects'] === array( 'smooth_scroll' => 'on' ), 'Invalid fields overwrote prior values, inheritance failed, or unknown data survived.' );
$can_edit = false;
$_POST['radman_page_effects'] = array( 'smooth_scroll' => 'off' );
\RadmanCT\Admin\PageEffects\save( 12 );
expect( $meta['_radman_page_effects']['smooth_scroll'] === 'on', 'Unauthorized effect change was saved.' );
$can_edit = true;
$_POST['radman_effects_nonce'] = 'invalid';
\RadmanCT\Admin\PageEffects\save( 12 );
expect( $meta['_radman_page_effects']['smooth_scroll'] === 'on', 'Invalid nonce changed effects.' );
$_POST['radman_effects_nonce'] = 'valid';
$_POST['radman_page_effects'] = array( 'smooth_scroll' => '' );
\RadmanCT\Admin\PageEffects\save( 12 );
expect( ! isset( $meta['_radman_page_effects'] ), 'All-inherited page kept unnecessary metadata.' );
echo "Passed: inactive module isolation, independent opt-outs, editor preview and conditional effect CSS.\n";
