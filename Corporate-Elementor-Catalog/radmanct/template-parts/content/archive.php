<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/integrations/content.php';
get_header();
if ( \RadmanCT\Renderers\render() ) { get_footer(); return; }
?>
<?php \RadmanCT\Sidebars\render( 'template-parts/' . ( ! empty( $args['catalog'] ) ? 'catalog' : 'portfolio' ) . '/archive', $args ); ?>
<?php get_footer(); ?>
