<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<?php if ( \RadmanCT\Renderers\render() ) { get_footer(); return; } ?>
<?php \RadmanCT\Sidebars\render( 'template-parts/blog/single' ); ?>
<?php get_footer(); ?>
