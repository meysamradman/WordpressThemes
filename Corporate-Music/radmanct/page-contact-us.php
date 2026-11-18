<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-artist" tabindex="-1">
 <?php while(have_posts()):the_post(); ?>
  <article>
   <h1 data-rdc-en="heading"><?php the_title(); ?></h1>
   <?php if(post_password_required()):echo get_the_password_form();else:$booking=\RadmanCT\Client\booking(); ?>
    <div class="rdc-reading" data-rdc-en="intro"><?php the_content();wp_link_pages(); ?></div>
    <div class="rdc-artist__channels">
     <?php if($booking['phone']): ?>
      <a class="rdc-artist__channel" href="<?php echo esc_url('tel:'.$booking['phone']); ?>">
       <?php echo \RadmanCT\icon('phone'); ?>
       <span class="rdc-artist__channel-copy"><small>هماهنگی اجرا · <?php echo esc_html($booking['name']); ?></small><bdi dir="ltr"><?php echo esc_html($booking['phone']); ?></bdi></span>
      </a>
     <?php endif; ?>
     <div class="rdc-artist__channel">
      <?php $handle=rawurlencode($booking['handle']); ?>
      <a href="<?php echo esc_url('https://t.me/'.$handle); ?>" aria-label="هماهنگی اجرا در تلگرام"><?php echo \RadmanCT\icon('social-telegram'); ?></a>
      <a href="<?php echo esc_url('https://eitaa.com/'.$handle); ?>" aria-label="هماهنگی اجرا در ایتا"><?php echo \RadmanCT\icon('social-messenger'); ?></a>
      <span class="rdc-artist__channel-copy" dir="ltr"><small><?php echo esc_html($booking['label']); ?></small><bdi><?php echo esc_html('@'.$booking['handle']); ?></bdi></span>
     </div>
    </div>
    <?php if(has_excerpt()): ?><p class="rdc-reading"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
   <?php endif; ?>
  </article>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
