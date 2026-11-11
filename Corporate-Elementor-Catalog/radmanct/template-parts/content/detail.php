<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/integrations/content.php';
get_header();
if ( \RadmanCT\Renderers\render() ) { get_footer(); return; }
\RadmanCT\Sidebars\render( 'template-parts/' . ( ! empty( $args['catalog'] ) ? 'catalog' : 'portfolio' ) . '/single', $args );
get_footer();
