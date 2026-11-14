<?php defined('ABSPATH')||exit; ?>
<section class="rdc-pt rdc-pt--split rdc-inv">
 <div class="rdc-pt__top"><?php \RadmanCT\PageTitle\trail($args['trail']); ?><?php if($args['label']!==''): ?><p class="rdc-pt__lbl"><?php echo esc_html($args['label']); ?></p><?php endif; ?></div>
 <div class="rdc-pt__grid">
  <?php \RadmanCT\PageTitle\heading($args); ?>
  <div class="rdc-pt__copy"><?php if($args['desc']!==''): ?><p><?php echo esc_html($args['desc']); ?></p><?php endif; ?><?php \RadmanCT\PageTitle\actions($args); ?></div>
 </div>
</section>
