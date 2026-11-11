<?php

define( 'ABSPATH', '/' );
$options = array(); $hooks = array(); $meta = array(); $writes = 0;
$types = array( 1 => 'radman_product', 2 => 'radman_product', 3 => 'radman_portfolio', 4 => 'post', 5 => 'radman_product' );
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
function get_option( $key, $default = false ) { return $GLOBALS['options']; }
function is_admin() { return false; }
function add_action( $name, $callback, ...$args ) { $GLOBALS['hooks'][ $name ] = $callback; }
function add_filter( ...$args ) { add_action( ...$args ); }
function apply_filters( $name, $value, ...$args ) { return $value; }
function __( $value, $domain ) { return $value; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return strip_tags( $value ); }
function get_post_type( $id ) { return $GLOBALS['types'][ $id ] ?? false; }
function metadata_exists( $type, $id, $key ) { return isset( $GLOBALS['meta'][ $id ][ $key ] ); }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function wp_slash( $value ) { return is_array( $value ) ? array_map( 'wp_slash', $value ) : ( is_string( $value ) ? addslashes( $value ) : $value ); }
function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : ( is_string( $value ) ? stripslashes( $value ) : $value ); }
function update_post_meta( $id, $key, $value ) { ++$GLOBALS['writes']; $GLOBALS['meta'][ $id ][ $key ] = wp_unslash( $value ); }
require dirname( __DIR__ ) . '/radmanct-content/inc/modules.php';
\RadmanCTContent\Modules\boot();
expect( array() === $hooks, 'Disabled Content modules registered integration hooks.' );
$options = array( 'catalog' => true );
\RadmanCTContent\Modules\boot();
expect( isset( $hooks['wpml_pre_save_pro_translation'], $hooks['wpml_pro_translation_completed'], $hooks['wpml_tm_adjust_translation_fields'] ), 'Active Content modules did not register native WPML integration.' );
use RadmanCTContent\Model;
use RadmanCTContent\WPML;
$row = static fn( $id, $text ) => array( 'id' => $id, 'label' => $text, 'value' => 'Value ' . $text, 'unit' => 'Unit ' . $text );
$previous = array_replace( Model\defaults(), array( 'code' => 'LOCAL-EN', 'gallery' => array( 21, 22 ), 'pricing' => array( 'mode' => 'contact', 'text' => 'Local price' ), 'specifications' => array( $row( 'weight', 'Old weight' ), $row( 'local', 'Local only' ) ) ) );
$translated = array_replace( Model\defaults(), array( 'code' => 'SOURCE-FA', 'gallery' => array( 7 ), 'pricing' => array( 'mode' => 'price', 'text' => 'Translated price' ), 'specifications' => array( $row( 'new', 'New translated row' ), $row( 'weight', 'Translated weight' ) ) ) );
$job = (object) array( 'original_doc_id' => 1, 'language_code' => 'en' );
$post = array( 'ID' => 2, 'post_type' => 'radman_product', 'post_title' => 'Translation' );
$meta[1][Model\META] = $translated; $meta[2][Model\META] = $previous;
expect( $post === WPML\capture( $post, $job ), 'WPML capture modified native post data.' );
$meta[2][Model\META] = $translated;
WPML\complete( 2, array(), $job );
$saved = $meta[2][Model\META];
expect( 'LOCAL-EN' === $saved['code'] && array( 21, 22 ) === $saved['gallery'] && 'contact' === $saved['pricing']['mode'], 'Translation overwrote language-specific nontext data.' );
expect( 'Translated price' === $saved['pricing']['text'] && array( 'weight', 'local', 'new' ) === array_column( $saved['specifications'], 'id' ), 'Translation lost new texts, independent row order or local rows.' );
expect( 'Translated weight' === $saved['specifications'][0]['label'] && 'Local only' === $saved['specifications'][1]['label'], 'Specification matching depended on array position instead of stable IDs.' );
WPML\complete( 2, array(), $job ); expect( 1 === $writes, 'Completed job retained a snapshot or wrote twice.' );
WPML\capture( $post, $job ); $meta[2][Model\META] = array( 'invalid' => true ); WPML\complete( 2, array(), $job );
expect( $saved === $meta[2][Model\META], 'Malformed job metadata erased previously valid data.' );
WPML\capture( $post, $job ); $before = $writes; WPML\complete( 2, array(), (object) array( 'original_doc_id' => 3, 'language_code' => 'en' ) );
expect( $before === $writes, 'Unrelated job consumed a snapshot for a different original.' );
WPML\capture( array( 'ID' => 5, 'post_type' => 'radman_product' ), $job );
expect( ! isset( WPML\pending()[5] ), 'First translation captured a default metadata value as an independent gallery.' );
WPML\capture( array( 'ID' => 4, 'post_type' => 'post' ), $job );
expect( array() === WPML\pending(), 'Unrelated post type was captured.' );
$options = array(); WPML\capture( $post, $job ); expect( array() === WPML\pending(), 'Disabled module was captured.' ); $options = array( 'catalog' => true );
$options = array( 'portfolio' => true ); $types[6] = 'radman_portfolio'; $meta[6][Model\META] = $previous;
$portfolio_job = (object) array( 'original_doc_id' => 3, 'language_code' => 'en' );
WPML\capture( array( 'ID' => 6, 'post_type' => 'radman_portfolio' ), $portfolio_job );
$meta[6][Model\META] = $translated; WPML\complete( 6, array(), $portfolio_job );
expect( 'LOCAL-EN' === $meta[6][Model\META]['code'] && 'Translated weight' === $meta[6][Model\META]['specifications'][0]['label'], 'Standalone portfolio translation depends on the catalog module.' );
WPML\capture( $post, $job ); expect( array() === WPML\pending(), 'Portfolio-only integration captured a disabled product.' );
$options = array( 'catalog' => true );
$escaped = $translated; $escaped['pricing']['text'] = 'C:\\price "quoted"';
WPML\capture( $post, $job ); $meta[2][Model\META] = $escaped; WPML\complete( 2, array(), $job );
expect( $escaped['pricing']['text'] === $meta[2][Model\META]['pricing']['text'], 'Translation save stripped meaningful quotes or backslashes.' );
$full = $previous; $full['specifications'] = array_map( static fn( $index ) => $row( 'local' . $index, 'Local' ), range( 1, 40 ) );
expect( 40 === count( WPML\merge( $full, $translated )['specifications'] ), 'Translation exceeded the validated specification limit.' );
$titles = WPML\titles( array( array( 'field_type' => 'field-_radman_content_data-0-specifications-2-label' ), array( 'field_type' => 'field-_radman_content_data-0-pricing-text' ), array( 'field_type' => 'field-other-0-pricing-text', 'title' => 'Other' ) ) );
expect( 'Specification label (3)' === $titles[0]['title'] && 'Displayed price' === $titles[1]['title'] && 'Other' === $titles[2]['title'], 'Native translation field titles were not scoped to Content metadata.' );

$config = simplexml_load_file( dirname( __DIR__ ) . '/radmanct-content/wpml-config.xml' );
expect( 'translate' === (string) $config->{'custom-fields'}->{'custom-field'}['action'], 'Content texts are still copy-once rather than translatable.' );
function xml_keys( $node ): array {
	$result = array();
	foreach ( $node->key as $child ) {
		$item = array( 'attr' => array( 'name' => (string) $child['name'], 'label' => (string) $child['label'] ) );
		if ( count( $child->key ) ) { $item['key'] = xml_keys( $child ); }
		$result[] = $item;
	}
	return $result;
}
$reference = dirname( __DIR__ ) . '/themes/sitepress-multilingual-cms/';
require $reference . 'classes/settings/class-wpml-custom-field-xml-settings-import.php';
$importer = new WPML_Custom_Field_XML_Settings_Import( null, new class { public function hasTranslatableIds( $key ) { return false; } }, array() );
$whitelist = ( new ReflectionMethod( $importer, 'get_custom_field_texts_keys' ) )->invoke( $importer, xml_keys( $config->{'custom-fields-texts'}->key ) );
class WPML_Custom_Field_Setting_Factory { public array $whitelist = array(); public function post_meta_setting( $key ) { return new class( $this->whitelist ) { public function __construct( public array $value ) {} public function get_attributes_whitelist() { return $this->value; } }; } }
require $reference . 'classes/settings/class-wpml-tm-serialized-custom-field-package-handler.php';
require $reference . 'classes/translation-jobs/class-wpml-tm-field-type-encoding.php';
$factory = new WPML_Custom_Field_Setting_Factory(); $factory->whitelist = $whitelist;
$handler = new WPML_TM_Serialized_Custom_Field_Package_Handler( $factory );
foreach ( array( array( 'specifications', 0, 'label' ), array( 'specifications', 2, 'value' ), array( 'specifications', 2, 'unit' ), array( 'pricing', 'text' ) ) as $path ) {
	[$field_type] = WPML_TM_Field_Type_Encoding::encode( Model\META, array_merge( array( 0 ), $path ) );
	expect( 1 === $handler->translate_only_whitelisted_attributes( 1, $field_type ), 'Real WPML matcher rejected a translatable Content text.' );
}
foreach ( array( array( 'version' ), array( 'code' ), array( 'gallery', 0 ), array( 'specifications', 0, 'id' ), array( 'pricing', 'mode' ) ) as $path ) {
	[$field_type] = WPML_TM_Field_Type_Encoding::encode( Model\META, array_merge( array( 0 ), $path ) );
	expect( 0 === $handler->translate_only_whitelisted_attributes( 1, $field_type ), 'Real WPML matcher exposed nontext Content data for translation.' );
}
class WPML_Translation_Job_Helper {}
require $reference . 'classes/translation-jobs/class-wpml-element-translation-package.php';
$raw = $translated; $raw['version'] = '1'; $raw['gallery'] = array( '7' );
$restored = WPML_Element_Translation_Package::preserve_numerics( $raw, Model\META, 1, true );
expect( null !== Model\validate( $restored ) && 1 === $restored['version'] && 7 === $restored['gallery'][0], 'Real WPML numeric restoration did not preserve the existing Content model.' );
echo "Passed: native WPML XML/matcher/numeric restoration, translated texts, independent media/code/mode/rows, module boundaries and bounded safe saves.\n";
