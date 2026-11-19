<?php
namespace RadmanCT\UI\Carousel;
defined('ABSPATH')||exit;
/** Items are rendered by a trusted caller; this owner knows no card types. */
function render(array $items,callable $item_view,array $args=array()):void {
 if(!$items)return;
 $direction=in_array($args['direction']??'',array('rtl','ltr'),true)?$args['direction']:(is_rtl()?'rtl':'ltr');
 $layout=($args['layout']??'bleed')==='boxed'?'boxed':'bleed';
 $id=wp_unique_id('rdc-cr-');$total=count($items);
 ?>
 <div class="rdc-cr" data-rdc-cr data-rdc-cr-layout="<?php echo esc_attr($layout); ?>" dir="<?php echo esc_attr($direction); ?>" role="region" aria-roledescription="کاروسل" aria-label="<?php echo esc_attr($args['label']??'کارت‌های طراحی'); ?>">
  <div class="rdc-cr__win" id="<?php echo esc_attr($id); ?>" tabindex="0" data-rdc-cr-win>
   <ul class="rdc-cr__trk">
    <?php foreach(array_values($items) as $index=>$item): ?><li class="rdc-cr__itm" aria-label="<?php echo esc_attr(($index+1).' از '.$total); ?>"><?php $item_view($item,$index); ?></li><?php endforeach; ?>
   </ul>
  </div>
  <div class="rdc-cr__nav" data-rdc-cr-nav hidden>
   <button class="rdc-cr__prev" type="button" data-rdc-cr-prev aria-label="کارت‌های قبلی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
   <button class="rdc-cr__next" type="button" data-rdc-cr-next aria-label="کارت‌های بعدی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
  </div>
  <div class="rdc-cr__dots" data-rdc-cr-dots aria-label="صفحه‌های کاروسل" hidden></div>
  <p class="rdc-cr__sr" data-rdc-cr-status aria-live="polite" aria-atomic="true"></p>
 </div>
 <?php
}
