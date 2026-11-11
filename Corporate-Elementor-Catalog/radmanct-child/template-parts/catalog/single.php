<?php
 defined( 'ABSPATH' ) || exit;
 while ( have_posts() ) : the_post();
    if ( post_password_required() ) {
        echo '<div class="rdct-ctr rdct-cnt-detail">' . get_the_password_form() . '</div>';
        continue;
    }
    require_once get_template_directory() . '/inc/integrations/content-meta.php';
    $rdct_archive = \RadmanCT\Content\Meta\archive( get_the_ID() );
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-cnt-detail rdct-ctr rdct-client-catalog-single' ); ?>>
        <?php if ( $rdct_archive ) : ?><a class="rdct-cnt-archive-link" href="<?php echo esc_url( $rdct_archive ); ?>"><?php esc_html_e( 'Back to products', 'radmanct' ); ?></a><?php endif; ?>
        <?php get_template_part( 'template-parts/catalog/single/overview' ); ?>
        <?php get_template_part( 'template-parts/catalog/single/content' ); ?>
        <?php \RadmanCT\Content\specifications( get_the_ID() ); ?>
    </article>
<?php endwhile; ?>
