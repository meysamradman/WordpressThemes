<?php
namespace RadmanLibrary\UI\Carousel;
defined('ABSPATH')||exit;
/** Items are rendered by a trusted caller; this owner knows no card types. */
function render(array $items,callable $item_view,array $args=array()):void {
 if(!$items)return;
 $direction=in_array($args['direction']??'',array('rtl','ltr'),true)?$args['direction']:(is_rtl()?'rtl':'ltr');
 $layout=($args['layout']??'bleed')==='boxed'?'boxed':'bleed';
 $id=wp_unique_id('rdl-cr-');$total=count($items);
 ?>
 <div class="rdl-cr" data-rdl-cr data-rdl-cr-layout="<?php echo esc_attr($layout); ?>" dir="<?php echo esc_attr($direction); ?>" role="region" aria-roledescription="کاروسل" aria-label="<?php echo esc_attr($args['label']??'کارت‌های طراحی'); ?>">
  <div class="rdl-cr__win" id="<?php echo esc_attr($id); ?>" tabindex="0" data-rdl-cr-win>
   <ul class="rdl-cr__trk">
    <?php foreach(array_values($items) as $index=>$item): ?><li class="rdl-cr__itm" aria-label="<?php echo esc_attr(($index+1).' از '.$total); ?>"><?php $item_view($item,$index); ?></li><?php endforeach; ?>
   </ul>
  </div>
  <div class="rdl-cr__nav" data-rdl-cr-nav hidden>
   <button class="rdl-cr__prev" type="button" data-rdl-cr-prev aria-label="کارت‌های قبلی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
   <button class="rdl-cr__next" type="button" data-rdl-cr-next aria-label="کارت‌های بعدی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
  </div>
  <div class="rdl-cr__dots" data-rdl-cr-dots aria-label="صفحه‌های کاروسل" hidden></div>
  <p class="rdl-cr__sr" data-rdl-cr-status aria-live="polite" aria-atomic="true"></p>
 </div>
 <?php
}
