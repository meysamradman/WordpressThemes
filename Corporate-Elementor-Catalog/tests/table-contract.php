<?php
define( 'ABSPATH', '/usr/src/wordpress/' );
function __( $value, $domain ) { return $value; }
function apply_filters( $hook, $value, ...$args ) { return $value; }
function wp_allowed_protocols() { return array( 'http', 'https', 'mailto' ); }
function sanitize_text_field( $value ) { return strip_tags( $value ); }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
spl_autoload_register( static function ( $class ) { if ( str_starts_with( $class, 'WP_HTML_' ) ) { $file = ABSPATH . 'wp-includes/html-api/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php'; if ( is_file( $file ) ) { require_once $file; } } } );
require ABSPATH . 'wp-includes/class-wp-token-map.php';
require ABSPATH . 'wp-includes/html-api/html5-named-character-references.php';
require ABSPATH . 'wp-includes/kses.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/components/table.php';
$input = array( 'columns' => array( 'Property', 'Value' ), 'rows' => array( array( 'cells' => array( 'Material', '<strong>Steel</strong><small>304</small><script>bad()</script><a href="javascript:bad()" onclick="bad()">Link</a>' ) ), array( 'cells' => array( "فارسی\nEnglish" ) ) ) );
$html = \RadmanCTComponents\Components\Table\render( $input, array( 'caption' => 'Specifications', 'row_headers' => true ) );
expect( str_contains( $html, '<caption>Specifications</caption>' ) && str_contains( $html, 'scope="col"' ) && str_contains( $html, 'scope="row"' ), 'Native table headers or caption missing.' );
expect( str_contains( $html, '<strong>Steel</strong><small>304</small>' ) && ! str_contains( $html, '<script' ) && ! str_contains( $html, 'onclick' ) && ! str_contains( $html, 'javascript:' ), 'Cell content was not sanitized by real WordPress KSES.' );
expect( str_contains( $html, '<br>' ) && str_contains( $html, '<td></td>' ), 'Empty or multiline cells disappeared.' );
expect( str_contains( $html, '<thead>' ), 'Column headers disappeared.' );
$without = \RadmanCTComponents\Components\Table\render( $input, array( 'header' => false, 'variant' => 'striped', 'min_width' => 0 ) );
expect( ! str_contains( $without, '<thead>' ) && str_contains( $without, 'rdct-tb--striped' ) && str_contains( $without, '--rdct-tb-min:0px' ), 'Optional headings, style or zero width failed.' );
$invalid = \RadmanCTComponents\Components\Table\settings( array( 'variant' => 'bad', 'padding' => '1px;background:red', 'head_background' => 'url(bad)' ) );
expect( 'grid' === $invalid['variant'] && 20 === $invalid['padding'] && '#f1f7f6' === $invalid['head_background'], 'Unsafe CSS settings accepted.' );
$large = \RadmanCTComponents\Components\Table\data( array( 'columns' => array_fill( 0, 30, 'Column' ), 'rows' => array_fill( 0, 150, array( 'cells' => array_fill( 0, 12, str_repeat( 'a', 3000 ) ) ) ) ) );
expect( 12 === count( $large['columns'] ) && count( $large['rows'] ) <= 100 && strlen( json_encode( $large ) ) < 140000, 'Table budget or shape bound failed.' );
expect( '' === \RadmanCTComponents\Components\Table\render( array() ), 'Empty table rendered.' );
echo "Table rendering, real KSES, rectangular data, limits and options passed.\n";
