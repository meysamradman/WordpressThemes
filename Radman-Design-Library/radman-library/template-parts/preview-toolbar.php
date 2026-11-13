<?php
defined('ABSPATH')||exit;
$mode=\RadmanLibrary\Preview\mode();$devices=\RadmanLibrary\Preview\devices();$device=$devices[$mode];
?>
<div class="rdl-pv__toolbar">
 <nav class="rdl-pv__choices" aria-label="اندازهٔ پیش‌نمایش">
  <?php foreach($devices as $key=>$item): ?>
   <a class="rdl-pv__choice" href="<?php echo esc_url(\RadmanLibrary\Preview\url($key)); ?>" data-rdl-pv-device="<?php echo esc_attr($key); ?>" data-rdl-pv-size="<?php echo esc_attr($item['width'].'x'.$item['height']); ?>"<?php if($key===$mode)echo ' aria-current="page"'; ?> title="<?php echo esc_attr($item['width']?$item['label'].' · '.$item['width'].' × '.$item['height']:$item['label']); ?>">
    <?php echo \RadmanCT\icon($item['icon']); ?><span><?php echo esc_html($item['label']); ?></span>
   </a>
  <?php endforeach; ?>
 </nav>
 <div class="rdl-pv__readout"><span data-rdl-pv-dim aria-live="polite" dir="ltr"><?php echo esc_html($device['width']?$device['width'].' × '.$device['height']:'عرض آزاد'); ?></span>
  <?php if($mode!=='desktop'): ?><button type="button" class="rdl-pv__fit" data-rdl-pv-fit aria-pressed="true">جاگیری خودکار <span data-rdl-pv-zoom dir="ltr"></span></button><?php endif; ?>
 </div>
</div>
