<?php

define( 'ABSPATH', '/' );
$hooks = array(); $valid_image = true; $removed = array(); $template = 'templates/client-home.php';
function __( $value, $domain ) { return $value; }
function add_action( $name, $callback, ...$args ) { $GLOBALS['hooks'][ $name ] = $callback; }
function add_filter( ...$args ) {}
function is_admin() { return true; }
function absint( $value ) { return abs( (int) $value ); }
function get_page_template_slug( $id ) { return $GLOBALS['template']; }
function remove_post_type_support( $type, $feature ) { $GLOBALS['removed'][] = array( $type, $feature ); }
function get_post_meta( ...$args ) { throw new RuntimeException( 'Code-owned home must not query post metadata.' ); }
function get_post_thumbnail_id( ...$args ) { throw new RuntimeException( 'Hero must not depend on Featured image.' ); }
function wp_attachment_is_image( $id ) { return $GLOBALS['valid_image'] && 12 === $id; }
function rdct_image( $options ) { $GLOBALS['image_options'] = $options; }
function rdct_text_reveal( $text, $options ) { $GLOBALS['text_render'] = array( $text, $options ); }
function expect( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
require dirname( __DIR__ ) . '/radmanct-child/inc/home.php';
expect( ! array_intersect( array_keys( $hooks ), array( 'add_meta_boxes_page', 'save_post_page', 'init' ) ), 'Home editor/storage hooks remained.' );
expect( 12 === \RadmanCTChild\Home\data( 4 )['image_id'], 'Code-owned attachment selection changed.' );
\RadmanCTChild\Home\media( 4 );
expect( 12 === $image_options['image_id'] && 'high' === $image_options['image_loading'], 'Native media or hero priority was lost.' );
$valid_image = false;
expect( 0 === \RadmanCTChild\Home\image_id( 4 ), 'Missing media must not fall back to a child URL.' );
$_GET['post'] = 4; $hooks['load-post.php']();
expect( array( array( 'page', 'thumbnail' ) ) === $removed, 'Custom home Featured image panel was not disabled.' );
$removed = array(); $template = 'default'; $hooks['load-post.php']();
expect( ! $removed, 'An ordinary page lost Featured image support.' );
\RadmanCTChild\Home\heading( "First phrase\nSecond phrase", 'h2', '#c59032', true );
expect( 'rotate' === $text_render[1]['mode'] && count( $text_render[1]['items'] ) === 2, 'Shared Text rotator was lost.' );
echo "Passed: code-owned home, native media, no editor storage and scoped Featured image support.\n";
