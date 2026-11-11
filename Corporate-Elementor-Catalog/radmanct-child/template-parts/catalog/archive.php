<?php defined( 'ABSPATH' ) || exit; ?>
<?php $rdct_ajax = 'catalog' === \RadmanCT\Archives\scope(); ?>
<section class="rdct-ctr rdct-cnt-listing rdct-client-catalog-archive" <?php if ( $rdct_ajax ) { echo \RadmanCT\Archives\attributes( 'catalog' ); } ?>>
    <?php get_template_part( 'template-parts/catalog/archive/header' ); ?>
    <?php if ( $rdct_ajax ) { \RadmanCT\Archives\status(); } ?>
    <?php \RadmanCT\Sidebars\layout( static function (): void {
        get_template_part( 'template-parts/catalog/archive/section' );
    }, $args['sidebar'] ?? array(), false ); ?>
</section>
