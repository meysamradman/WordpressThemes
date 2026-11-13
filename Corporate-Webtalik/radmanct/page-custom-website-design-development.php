<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-inv" tabindex="-1">
 <?php get_template_part('template-parts/layout/page-title'); ?>
 <?php while(have_posts()):the_post(); ?>
  <?php if(post_password_required()): ?>
   <div class="rdc-ctr rdc-main"><?php echo get_the_password_form(); ?></div>
  <?php else: ?>
   <?php if(trim(get_the_content())): ?><article class="rdc-ctr rdc-main rdc-cnt"><?php the_content();wp_link_pages(); ?></article><?php endif; ?>
   <?php get_template_part('template-parts/ui/faq',null,array('items'=>apply_filters('radmanct/services/faq',array()))); ?>
  <?php endif; ?>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
