<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-bio" tabindex="-1">
 <?php while(have_posts()):the_post(); ?>
 <article>
  <?php if(post_password_required()): ?><h1><?php the_title(); ?></h1><?php echo get_the_password_form(); ?>
  <?php else: ?>
   <div class="rdc-bio__hero">
    <figure class="rdc-bio__portrait">
     <img class="rdc-bio__photo" src="<?php echo esc_url(get_theme_file_uri('images/pages/biography.webp')); ?>"
      width="402" height="357" alt="پرتره و نشان علی اسدی" fetchpriority="high" decoding="async">
    </figure>
    <div class="rdc-bio__identity" dir="ltr"><p class="rdc-bio__handle"><?php echo esc_html(\RadmanCT\Client\social_handle()); ?></p><?php get_template_part('template-parts/ui/socials',null,['items'=>\RadmanCT\Client\socials()]); ?></div>
   </div>
   <h1 class="rdc-bio__title" data-rdc-en="heading"><?php \RadmanCT\entrance_words(get_the_title()); ?></h1>
   <div class="rdc-bio__content rdc-reading" data-rdc-en="intro">
    <?php the_content();wp_link_pages();get_template_part('template-parts/sections/biography/works'); ?>
   </div>
  <?php endif; ?>
 </article>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
