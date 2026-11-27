<?php
defined('ABSPATH')||exit;
$preview=\RadmanCT\media_id('portfolio-archive-preview-a');
if(!\RadmanCT\PageTitle\attachment_allowed($preview)||!wp_attachment_is_image($preview))$preview=0;
$logo=\RadmanCT\media_id('logo');
?>
<?php if(have_posts()): ?>
 <h2 class="screen-reader-text"><?php esc_html_e('فهرست نمونه‌کارها','radmanct'); ?></h2>
 <div class="rdc-prjs" data-rdc-arc-items>
  <?php while(have_posts()):the_post();
   get_template_part('template-parts/cards/project',null,['archive'=>true,'preview_id'=>$preview,'logo_id'=>$logo]);
  endwhile; ?>
 </div>
 <?php get_template_part('template-parts/ui/pagination',null,['mode'=>'more']); ?>
<?php else: ?>
 <p><?php esc_html_e('No content found.','radmanct'); ?></p>
<?php endif; ?>
