<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-artist" tabindex="-1">
 <?php while(have_posts()):the_post(); ?>
  <article>
   <h1 data-rdc-en="heading"><?php \RadmanCT\entrance_words(get_the_title()); ?></h1>
   <?php if(post_password_required()):echo get_the_password_form();else:$booking=\RadmanCT\Client\booking(); ?>
    <div class="rdc-reading">
     <div data-rdc-en="text"><?php the_content(); ?></div>
     <?php wp_link_pages(); ?>
    </div>
    <div class="rdc-artist__channels">
     <?php if($booking['phone']): $phone_label=preg_replace('/^\+98(\d{3})(\d{3})(\d{4})$/D','0$1 $2 $3',$booking['phone']); ?>
      <a class="rdc-artist__channel" href="<?php echo esc_url('tel:'.$booking['phone'],['tel']); ?>">
       <?php echo \RadmanCT\icon('phone'); ?>
       <span class="rdc-artist__channel-copy"><small>هماهنگی اجرا · <?php echo esc_html($booking['name']); ?></small><bdi dir="ltr"><?php echo esc_html($phone_label); ?></bdi></span>
      </a>
     <?php endif; ?>
     <div class="rdc-artist__channel">
      <?php $handle=rawurlencode($booking['handle']); ?>
      <a href="<?php echo esc_url('https://t.me/'.$handle); ?>" target="_blank" rel="noopener noreferrer" aria-label="هماهنگی اجرا در تلگرام (تب جدید)"><?php echo \RadmanCT\icon('social-telegram'); ?></a>
      <a href="<?php echo esc_url('https://eitaa.com/'.$handle); ?>" target="_blank" rel="noopener noreferrer" aria-label="هماهنگی اجرا در ایتا (تب جدید)"><?php echo \RadmanCT\icon('social-messenger'); ?></a>
      <span class="rdc-artist__channel-copy" dir="ltr"><small><?php echo esc_html($booking['label']); ?></small><bdi><?php echo esc_html('@'.$booking['handle']); ?></bdi></span>
     </div>
    </div>
    <?php if(has_excerpt()): ?><p class="rdc-reading"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
   <?php endif; ?>
  </article>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
