<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-bio" tabindex="-1">
 <?php while(have_posts()):the_post(); ?>
 <article>
  <?php if(post_password_required()): ?><h1><?php the_title(); ?></h1><?php echo get_the_password_form(); ?>
  <?php else:$image=(int)get_post_thumbnail_id(); ?>
   <?php if(\RadmanCT\PageTitle\attachment_allowed($image)&&wp_attachment_is_image($image)): ?><figure class="rdc-bio__portrait"><?php echo wp_get_attachment_image($image,'large',false,['loading'=>'eager','fetchpriority'=>'high','sizes'=>'(max-width:650px) 90vw,740px']); ?></figure><?php endif; ?>
   <h1 class="rdc-bio__title"><?php the_title(); ?></h1>
   <div class="rdc-bio__content"><?php the_content();wp_link_pages(); ?></div>
  <?php endif; ?>
 </article>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
