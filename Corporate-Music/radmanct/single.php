<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" tabindex="-1">
<?php defined('ABSPATH')||exit; ?>
<?php  ?>
<div class="rdc-ctr rdc-main rdc-cnt-ly rdc-blog-sgl"><?php while(have_posts()):the_post(); ?>
<article <?php post_class('rdc-cnt'); ?>><?php get_template_part('template-parts/ui/content-heading'); ?><?php if(post_password_required()){echo get_the_password_form();}else{ ?>
<a class="rdc-sgl__back" href="<?php echo esc_url(get_permalink((int)get_option('page_for_posts'))); ?>"><?php esc_html_e('Back to articles','radmanct'); ?></a><div class="rdc-post-meta"><?php get_template_part('template-parts/blog/meta'); ?></div>
<?php if(has_excerpt())the_excerpt();the_content();wp_link_pages();} ?></article>
<?php if(!post_password_required()): ?><aside class="rdc-artc-cvr"><figure class="rdc-artc-cvr__md"><?php the_post_thumbnail('large',array('class'=>'rdc-artc-cvr__im','fetchpriority'=>'high','loading'=>'eager','sizes'=>'(max-width:1024px) 100vw,360px')); ?><time class="rdc-artc-cvr__date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><span><?php echo esc_html(get_the_date('d')); ?></span><span><?php echo esc_html(get_the_date('M')); ?></span><span><?php echo esc_html(get_the_date('Y')); ?></span></time></figure><div class="rdc-artc-cvr__dtl"><span class="rdc-artc-cvr__type-ico"><?php echo \RadmanCT\icon('file-text'); ?></span><div><p class="rdc-artc-cvr__ttl"><?php esc_html_e('Articles','radmanct'); ?></p><p class="rdc-artc-cvr__cats"><?php echo \RadmanCT\Blog\categories(get_the_ID()); ?></p></div></div></aside><?php endif;endwhile; ?></div><?php get_template_part('template-parts/layout/continuation'); ?><?php  ?>

</main><?php get_footer(); ?>
