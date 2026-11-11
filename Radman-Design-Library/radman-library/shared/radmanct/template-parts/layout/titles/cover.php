<?php defined('ABSPATH')||exit; ?>
<section class="rdc-pt rdc-pt--cover rdc-inv"<?php if($args['video'])echo ' data-rdc-pt-vid'; ?>>
 <?php \RadmanCT\PageTitle\media($args); ?>
 <div class="rdc-pt__cnt"><div class="rdc-pt__copy">
  <?php \RadmanCT\PageTitle\trail($args['trail']); ?>
  <?php if($args['label']!==''): ?><p class="rdc-pt__lbl"><?php echo esc_html($args['label']); ?></p><?php endif; ?>
  <?php \RadmanCT\PageTitle\heading($args); ?>
  <?php if($args['desc']!==''||!empty($args['button']['url'])||!empty($args['link']['url'])): ?>
  <div class="rdc-pt__lead">
   <?php if($args['desc']!==''): ?><p class="rdc-pt__desc"><?php echo esc_html($args['desc']); ?></p><?php endif; ?>
   <?php \RadmanCT\PageTitle\actions($args); ?>
  </div>
  <?php endif; ?>
 </div></div>
</section>
