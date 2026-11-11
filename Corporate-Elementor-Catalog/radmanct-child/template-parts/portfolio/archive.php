<?php defined( 'ABSPATH' ) || exit; ?>
<?php $rdct_ajax = 'portfolio' === \RadmanCT\Archives\scope(); $rdct_animation = \RadmanCT\Assets\entry( '../radmanct-components/src/js/animation-vendor.js' ); ?>
<section class="rdct-ctr rdct-cnt-listing rdct-client-portfolio-archive" <?php if ( $rdct_animation ) { echo 'data-animation-src="' . esc_url( \RadmanCT\Assets\url( $rdct_animation['file'] ) ) . '"'; } ?> data-more-loaded="<?php esc_attr_e( 'More projects loaded.', 'radmanct-child' ); ?>" <?php if ( $rdct_ajax ) { echo \RadmanCT\Archives\attributes( 'portfolio' ); } ?>>
    <header <?php if ( \RadmanCT\PageTitle\active() && ( ! is_tax() || ! get_the_archive_description() ) ) { echo 'hidden'; } ?>><?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php if ( is_tax() ) { single_term_title(); } else { post_type_archive_title(); } ?></h1><?php endif; ?>
        <?php if ( is_tax() ) { the_archive_description( '<div class="rdct-cnt">', '</div>' ); } ?>
    </header>
    <?php if ( $rdct_ajax ) : \RadmanCT\Archives\status(); ?>
        <div data-rdct-archive-view tabindex="-1"><?php \RadmanCT\Archives\view( 'portfolio' ); ?></div>
    <?php else : get_template_part( 'template-parts/content/portfolio-results' ); endif; ?>
</section>
