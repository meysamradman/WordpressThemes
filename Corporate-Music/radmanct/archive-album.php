<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection" data-rdc-arc tabindex="-1">
 <h1>گالری تصاویر</h1><p class="rdc-collection__lead">آلبوم‌ها را باز کنید و تصاویر هر مجموعه را ببینید.</p>
 <p class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></p>
 <div data-rdc-res tabindex="-1" aria-label="آلبوم‌های عکس"><div class="rdc-album-grid"><?php if(have_posts()):while(have_posts()):the_post();\RadmanCT\Albums\card(get_post());endwhile;else: ?><p>هنوز آلبومی منتشر نشده است.</p><?php endif; ?></div><?php get_template_part('template-parts/ui/archive-pagination'); ?></div>
</main><?php get_footer(); ?>
