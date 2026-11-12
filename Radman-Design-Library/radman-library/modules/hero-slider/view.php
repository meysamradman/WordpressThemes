<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=\RadmanLibrary\Designs\HeroSlider\data();
$slides=array_values($data['slides']??array());
if(!$slides)return;
$id=wp_unique_id('rdl-hsl-');
?>
<section class="rdl-hsl rdc-inv" data-rdl-hsl role="region" aria-roledescription="اسلایدر" aria-label="<?php echo esc_attr($data['label']); ?>" tabindex="-1">
 <h1 class="rdl-hsl__sr"><?php echo esc_html($data['label']); ?></h1>
 <div class="rdl-hsl__win" id="<?php echo esc_attr($id); ?>" tabindex="0" aria-label="اسلایدهای هیرو">
  <?php foreach($slides as $index=>$slide): ?>
   <article class="rdl-hsl__itm" data-rdl-hsl-itm role="group" aria-roledescription="اسلاید" aria-label="<?php echo esc_attr(($index+1).' از '.count($slides)); ?>">
    <div class="rdl-hsl__md" data-rdl-hsl-md>
     <?php if(!empty($slide['image']))echo wp_get_attachment_image((int)$slide['image'],'full',false,array('class'=>'rdl-hsl__im','alt'=>'','sizes'=>'100vw','loading'=>$index===0?'eager':'lazy','fetchpriority'=>$index===0?'high':'low','draggable'=>'false')); ?>
     <div class="rdl-hsl__shade" aria-hidden="true"></div>
    </div>
    <div class="rdl-hsl__cnt">
     <p class="rdl-hsl__lbl" data-rdl-hsl-tx><?php echo esc_html($slide['label']); ?></p>
     <h2 class="rdl-hsl__ttl" data-rdl-hsl-tx><?php echo nl2br(esc_html(str_replace('\\n',"\n",$slide['title']))); ?></h2>
     <p class="rdl-hsl__desc" data-rdl-hsl-tx><?php echo esc_html($slide['desc']); ?></p>
     <div class="rdl-hsl__action" data-rdl-hsl-tx><?php echo \RadmanCT\button($slide['button'],$slide['url']); ?></div>
    </div>
   </article>
  <?php endforeach; ?>
 </div>
 <div class="rdl-hsl__nav" data-rdl-hsl-nav hidden>
  <div class="rdl-hsl__arrows">
   <button type="button" class="rdl-hsl__prev" data-rdl-hsl-prev aria-label="اسلاید قبلی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
   <button type="button" class="rdl-hsl__next" data-rdl-hsl-next aria-label="اسلاید بعدی" aria-controls="<?php echo esc_attr($id); ?>"><?php echo \RadmanCT\icon('arrow-right'); ?></button>
  </div>
  <p class="rdl-hsl__num" aria-hidden="true"><span data-rdl-hsl-num>01</span><span class="rdl-hsl__line"></span><span><?php echo esc_html(str_pad((string)count($slides),2,'0',STR_PAD_LEFT)); ?></span></p>
  <p class="rdl-hsl__hint">برای کشف بیشتر، بکشید <span aria-hidden="true">↔</span></p>
 </div>
 <p class="rdl-hsl__sr" data-rdl-hsl-status aria-live="polite" aria-atomic="true"></p>
</section>
