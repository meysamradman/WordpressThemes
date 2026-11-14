<?php defined('ABSPATH')||exit; ?>
<section class="rdc-trk-arc rdc-ctr rdc-main" data-rdc-arc data-rdc-blog-url="<?php echo esc_url(\RadmanCT\Music\url()); ?>">
 <h1 class="screen-reader-text"><?php if(is_tax('music_category'))single_term_title();elseif(is_search())printf('نتایج جستجو: %s',esc_html(get_search_query()));else echo 'آهنگ‌ها'; ?></h1>
 <p class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></p>
 <div data-rdc-res tabindex="-1" aria-label="فهرست آهنگ‌ها">
  <div class="rdc-trk-grid"><?php if(have_posts()):while(have_posts()):the_post();\RadmanCT\Music\card(get_post(),'h2',false,'list');endwhile;else: ?><p>آهنگی پیدا نشد.</p><?php endif; ?></div>
  <?php get_template_part('template-parts/ui/archive-pagination'); ?>
 </div>
</section>
