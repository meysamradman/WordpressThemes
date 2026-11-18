<?php defined('ABSPATH')||exit;$rtl=is_rtl(); ?>
<header class="rdc-music-section__hdr">
 <h2 id="<?php echo esc_attr($args['heading_id']); ?>">
  <span class="rdc-music-section__badge" aria-hidden="true"><?php echo isset($args['icon']) ? \RadmanCT\icon($args['icon']) : esc_html($args['badge']); ?></span>
  <?php if(!empty($args['url'])): ?>
   <a data-rdc-en="viewport" href="<?php echo esc_url($args['url']); ?>"><?php echo esc_html($args['title']); ?></a>
  <?php else: ?>
   <span data-rdc-en="viewport"><?php echo esc_html($args['title']); ?></span>
  <?php endif; ?>
 </h2>
 <div class="rdc-music-section__arrows" data-rdc-rail-nav hidden>
  <button type="button" data-rdc-rail-prev aria-label="<?php echo esc_attr($args['previous']); ?>"><?php echo \RadmanCT\icon($rtl?'chevron-right':'chevron-left'); ?></button>
  <button type="button" data-rdc-rail-next aria-label="<?php echo esc_attr($args['next']); ?>"><?php echo \RadmanCT\icon($rtl?'chevron-left':'chevron-right'); ?></button>
 </div>
</header>
