<?php
defined('ABSPATH')||exit;
if(!\RadmanCT\Contact\visible())return;
$contact=\RadmanCT\Contact\data();
$panel=wp_unique_id('rdc-ctc-');
?>
<div class="rdc-ctc" data-rdc-ctc>
 <div class="rdc-ctc__pnl" id="<?php echo esc_attr($panel); ?>" role="region" aria-label="<?php echo esc_attr($contact['label']); ?>" hidden inert>
  <ul>
   <?php foreach($contact['channels'] as $channel): ?>
   <li><a href="<?php echo esc_url($channel['url'],array('https','http','tel','sms','mailto')); ?>">
    <span class="rdc-ctc__ico"><?php echo \RadmanCT\icon($channel['icon']); ?></span>
    <span><strong><?php echo esc_html($channel['label']); ?></strong><span class="rdc-ctc__val" dir="ltr"><?php echo esc_html($channel['value']); ?></span></span>
   </a></li>
   <?php endforeach; ?>
  </ul>
 </div>
 <button class="rdc-ctc__btn" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($panel); ?>" aria-label="<?php echo esc_attr($contact['label']); ?>">
  <?php echo \RadmanCT\icon('chat'); ?><span class="rdc-ctc__dot" aria-hidden="true"></span>
 </button>
</div>
