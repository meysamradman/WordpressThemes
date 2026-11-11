<?php

define( 'ABSPATH', '/' );
define( 'RadmanCTContent\\FILE', __DIR__ . '/../radmanct-content/radmanct-content.php' );
$modules = array();
$caps = array();
$page_queries = $post_reads = 0;
$styles = $scripts = $errors = array();
$destination = 99;
class WP_Post {
	public $post_type = 'page';
	public $post_status = 'publish';
	public $post_password = '';
	public $post_title = 'Contact <page>';
	public function __construct( public int $ID ) {}
}
$pages = array();
foreach ( array( 1, 2, 3, 99 ) as $id ) { $pages[ $id ] = new WP_Post( $id ); }
$pages[2]->post_status = 'draft';
$pages[3]->post_password = 'protected';
function expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function get_option( $key, $default = false ) { return 'radman_core_modules' === $key ? $GLOBALS['modules'] : ( 'radman_core_inquiry_page' === $key ? $GLOBALS['destination'] : $default ); }
function current_user_can( $cap ) { return in_array( $cap, $GLOBALS['caps'], true ); }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function add_settings_error( ...$args ) { $GLOBALS['errors'][] = $args; }
function __( $text, $domain ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES ); }
function esc_attr( $text ) { return esc_html( $text ); }
function esc_url( $text ) { return esc_html( $text ); }
function esc_html__( $text, $domain ) { return esc_html( $text ); }
function esc_attr__( $text, $domain ) { return esc_attr( $text ); }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function wp_unslash( $text ) { return stripslashes( $text ); }
function admin_url( $path ) { return '/wp-admin/' . $path; }
function plugin_dir_url( $file ) { return '/wp-content/plugins/radmanct-content/'; }
function checked( $value, $expected, $echo ) { return $value === $expected ? ' checked' : ''; }
function selected( $value, $expected, $echo ) { return $value === $expected ? ' selected' : ''; }
function get_posts( $args ) {
	++$GLOBALS['page_queries'];
	expect( 20 === $args['posts_per_page'] && true === $args['no_found_rows'] && false === $args['has_password'] && false === $args['suppress_filters'] && ! $args['update_post_meta_cache'] && ! $args['update_post_term_cache'], 'Inquiry page query was unbounded or disabled language filters.' );
	return array( $GLOBALS['pages'][1], $GLOBALS['pages'][2], $GLOBALS['pages'][3] );
}
function get_post( $id ) { ++$GLOBALS['post_reads']; return $GLOBALS['pages'][ $id ] ?? null; }
function get_post_type( $id ) { return $GLOBALS['pages'][ $id ]->post_type ?? false; }
function get_post_status( $id ) { return $GLOBALS['pages'][ $id ]->post_status ?? false; }
function get_post_field( $field, $id ) { return $GLOBALS['pages'][ $id ]->$field ?? null; }
function get_the_title( $post ) { return $post->post_title; }
function wp_list_pluck( $items, $field ) { return array_map( static fn( $item ) => $item->$field, $items ); }
function post_type_exists( $type ) { return true; }
function get_post_type_object( $type ) { return (object) array( 'cap' => (object) array( 'edit_posts' => 'edit_catalog_items', 'create_posts' => 'create_catalog_items' ) ); }
function get_post_type_archive_link( $type ) { return '/' . $type . '/?lang=fa'; }
function settings_errors() {}
function settings_fields( $group ) { echo '<input type="hidden" name="option_page" value="radman_core">'; }
function submit_button() { echo '<button>Save</button>'; }
function wp_enqueue_style( $handle, $url, $deps, $version ) { $GLOBALS['styles'][ $handle ] = array( $url, $deps ); }
function wp_enqueue_script( $handle, $url, $deps, $version, $footer ) { $GLOBALS['scripts'][ $handle ] = array( $url, $deps ); }
require __DIR__ . '/../radmanct-content/inc/modules.php';
require __DIR__ . '/../radmanct-content/inc/admin/settings.php';
require __DIR__ . '/../radmanct-content/inc/admin/inquiry.php';
function render_settings(): string { ob_start(); \RadmanCTContent\Admin\Settings\render(); return ob_get_clean(); }

expect( '' === render_settings(), 'Unauthorized Core settings rendered.' );
\RadmanCTContent\Admin\Settings\enqueue( 'toplevel_page_radmanct-content' );
expect( ! $styles, 'Unauthorized Core settings loaded CSS.' );
$caps = array( 'manage_options' );
expect( str_contains( render_settings(), 'rdct-cor-modules' ) && 0 === $page_queries && 0 === $post_reads, 'Disabled catalog read inquiry destinations.' );
expect( 99 === \RadmanCTContent\Admin\Settings\inquiry( 0 ), 'Disabled inquiry field overwrote its saved destination.' );
$modules['catalog'] = true;
$html = render_settings();
expect( 1 === $page_queries && 1 === $post_reads && str_contains( $html, 'value="99" selected' ) && ! str_contains( $html, 'value="2"' ) && ! str_contains( $html, 'Contact <page>' ), 'Bounded destinations lost saved selection or exposed private/unsafe titles.' );
expect( str_contains( $html, 'View archive' ) && ! str_contains( $html, 'post-new.php' ), 'Module actions ignored custom create capabilities.' );
$caps[] = 'create_catalog_items';
expect( str_contains( render_settings(), 'post-new.php?post_type=radman_product' ), 'Native custom create capability was ignored.' );
expect( 99 === \RadmanCTContent\Admin\Settings\inquiry( null ) && ! $errors, 'Omitted inquiry field caused an error or lost saved state.' );
expect( 0 === \RadmanCTContent\Admin\Settings\inquiry( '0' ) && 1 === \RadmanCTContent\Admin\Settings\inquiry( '1' ), 'Valid inquiry destination or explicit disable was rejected.' );
expect( 99 === \RadmanCTContent\Admin\Settings\inquiry( '2' ) && count( $errors ) === 1, 'Invalid inquiry destination replaced a valid saved page.' );
$_GET['inquiry_search'] = array( 'bad' ); expect( '' === \RadmanCTContent\Admin\Inquiry\search(), 'Malformed page search accepted.' );
$_GET['inquiry_search'] = '<b>Contact</b>'; expect( 'Contact' === \RadmanCTContent\Admin\Inquiry\search(), 'Page search was not normalized.' );
\RadmanCTContent\Admin\Settings\enqueue( 'dashboard' ); expect( ! $styles, 'Core settings stylesheet leaked to another screen.' );
\RadmanCTContent\Admin\Settings\enqueue( 'toplevel_page_radmanct-content' );
expect( isset( $styles['rdct-core-settings'] ) && array( 'dashicons' ) === $styles['rdct-core-settings'][1] && ! $scripts, 'Core settings CSS or script isolation failed.' );
expect( ! \RadmanCTContent\Admin\Assets\script( 'wrong', 'src/css/admin/settings.css' ) && ! \RadmanCTContent\Admin\Assets\style( 'wrong', 'src/js/admin/content.js' ), 'Admin asset helpers allowed a mismatched asset type.' );
echo "Passed: Core module cards, custom capabilities, bounded public destination search, saved-state preservation and screen-specific minified assets.\n";
