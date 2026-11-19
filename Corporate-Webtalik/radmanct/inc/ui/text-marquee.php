<?php
namespace RadmanCT\UI;
defined('ABSPATH')||exit;

function text_marquee(array $items,callable $render_item,string $label):void {
 if(!$items)return;
 ?>
 <div class="rdc-mtx" data-rdc-mtx>
  <div class="rdc-mtx__lbl">
   <span><?php echo esc_html($label); ?></span>
   <button type="button" class="rdc-mtx__pause" data-rdc-mtx-pause hidden aria-label="توقف حرکت نوار" aria-pressed="false">
    <span class="rdc-mtx__pause-ico"><?php echo \RadmanCT\icon('pause'); ?></span>
    <span class="rdc-mtx__play-ico"><?php echo \RadmanCT\icon('play'); ?></span>
   </button>
  </div>
  <div class="rdc-mtx__win" role="region" aria-label="<?php echo esc_attr($label); ?>" tabindex="0">
   <div class="rdc-mtx__trk">
    <ul class="rdc-mtx__grp" dir="<?php echo is_rtl()?'rtl':'ltr'; ?>">
     <?php foreach($items as $item): ?><li><?php $render_item($item); ?></li><?php endforeach; ?>
    </ul>
   </div>
  </div>
 </div>
 <?php
}
