<?php
namespace RadmanCT\Settings { function values() { return array( 'block_google_fonts' => $GLOBALS['block_fonts'] ); } }
namespace {
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function get_template_directory() { return dirname( __DIR__ ) . '/radmanct'; }
function get_template_directory_uri() { return 'https://site.test/theme'; }
function wp_scripts() { return $GLOBALS['scripts']; }
function wp_styles() { return $GLOBALS['styles']; }
function wp_dequeue_style( $handle ) { $GLOBALS['dequeued'][] = $handle; }
function expect( $test, $message ) { if ( ! $test ) { throw new \RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct/inc/integrations/elementor/local-editor-assets.php';
require dirname( __DIR__ ) . '/themes/elementor/modules/assets-manager/assets.php';
use RadmanCT\Integrations\Elementor\LocalAssets;
$GLOBALS['scripts'] = (object) array( 'registered' => array( 'ace' => (object) array( 'ver' => '1.43.2' ) ) );
$assets = new \Elementor\Modules\AssetsManager\Assets();
LocalAssets\scripts( $assets );
$assets->append( 'ace', 'https://cdn.jsdelivr.net/npm/ace-builds@1.43.2/ace.min.js' );
expect( str_starts_with( $assets->assets_map()['ace']['uri'], 'https://site.test/theme/vendor/ace/ace.js' ), 'Native Assets Manager kept the remote Ace URL.' );
expect( array( 'ace', 'ace-language-tools' ) === $assets->priority_queue(), 'Ace dependency order changed.' );
expect( str_contains( LocalAssets\source( 'https://cdn.jsdelivr.net/npm/ace-builds@1.43.2/src-min-noconflict/ext-language_tools.js', 'ace-language-tools' ), '/vendor/ace/ext-language_tools.js' ), 'WordPress script fallback kept the CDN.' );
expect( 'https://example.test/ace.js' === LocalAssets\source( 'https://example.test/ace.js', 'ace' ), 'Unrelated editor source changed.' );
$GLOBALS['scripts']->registered['ace']->ver = '9.0'; $other = new \Elementor\Modules\AssetsManager\Assets(); LocalAssets\scripts( $other );
expect( array() === $other->assets_map(), 'Unknown Ace version was silently replaced.' );
$GLOBALS['styles'] = (object) array( 'registered' => array( 'elementor-editor' => (object) array( 'deps' => array( 'elementor-icons', 'google-font-roboto' ) ) ) );
$GLOBALS['block_fonts'] = true; LocalAssets\styles();
expect( array( 'elementor-icons' ) === $GLOBALS['styles']->registered['elementor-editor']->deps, 'Editor Google font dependency survived the local-font policy.' );
$GLOBALS['block_fonts'] = false;
$GLOBALS['styles']->registered['elementor-editor']->deps[] = 'google-font-roboto'; LocalAssets\styles();
expect( array( 'elementor-icons' ) === $GLOBALS['styles']->registered['elementor-editor']->deps, 'Editor font ownership must stay local independently of the generic plugin-font setting.' );
echo "Passed: native Elementor asset map, local Ace dependency order, version guard and editor Google-font policy.\n";
}
