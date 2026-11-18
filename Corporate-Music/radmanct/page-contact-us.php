<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-artist" tabindex="-1">
 <?php while(have_posts()):the_post(); ?>
  <article>
   <h1><?php the_title(); ?></h1>
   <?php if(post_password_required()):echo get_the_password_form();else:$booking=\RadmanCT\Client\booking(); ?>
    <div class="rdc-artist__content"><?php the_content();wp_link_pages(); ?></div>
    <div class="rdc-artist__channels">
     <?php if($booking['phone']): ?>
      <a class="rdc-artist__channel" href="<?php echo esc_url('tel:'.$booking['phone']); ?>"><?php echo \RadmanCT\icon('phone'); ?><bdi><?php echo esc_html($booking['phone']); ?></bdi></a>
     <?php endif; ?>
     <div class="rdc-artist__channel" aria-label="شناسهٔ هماهنگی در تلگرام و ایتا">
      <?php echo \RadmanCT\icon('social-telegram').\RadmanCT\icon('social-messenger'); ?><bdi dir="ltr"><?php echo esc_html('@'.$booking['handle']); ?></bdi>
     </div>
    </div>
    <?php if(has_excerpt()): ?><p class="rdc-artist__content"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
   <?php endif; ?>
  </article>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
