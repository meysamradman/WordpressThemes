<?php defined('ABSPATH')||exit; ?>
<?php if(have_posts()): ?><h2 class="screen-reader-text"><?php esc_html_e('فهرست نمونه‌کارها','radmanct'); ?></h2><?php endif; ?>
<div class="rdc-prjs"><?php while(have_posts()):the_post();get_template_part('template-parts/cards/project');endwhile; ?></div>
<?php if(get_next_posts_page_link()): ?><nav class="rdc-prjs-pg"><?php echo \RadmanCT\button(__('Load more','radmanct'),get_next_posts_page_link(),array('data-rdc-load-more'=>true)); ?></nav><?php endif; ?>
