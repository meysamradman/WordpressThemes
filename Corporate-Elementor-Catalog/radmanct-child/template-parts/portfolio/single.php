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
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-cnt-detail rdct-ctr rdct-client-portfolio-single' ); ?>>
        <?php if ( $rdct_archive ) : ?><a class="rdct-cnt-archive-link" href="<?php echo esc_url( $rdct_archive ); ?>"><?php esc_html_e( 'Back to projects', 'radmanct' ); ?></a><?php endif; ?>
        <header class="rdct-cnt-detail__header"><?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php the_title(); ?></h1><?php endif; ?>
            <?php get_template_part( 'template-parts/content/meta' ); ?>
            <?php if ( has_excerpt() ) : ?><div class="rdct-cnt-detail__intro"><?php the_excerpt(); ?></div><?php endif; ?>
        </header>
        <?php get_template_part( 'template-parts/content/project-media' ); ?>
        <div class="rdct-cnt"><?php the_content(); ?></div>
        <?php \RadmanCT\Templates\content_pagination(); ?>
    </article>
<?php endwhile; ?>
