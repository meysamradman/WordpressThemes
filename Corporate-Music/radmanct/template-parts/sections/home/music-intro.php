<?php
defined('ABSPATH')||exit;
$data=\RadmanCT\MusicHome\data();
$tracks=\RadmanCT\MusicHome\featured();
if(!$tracks)return;
$banners=\RadmanCT\MusicHome\banners((int)get_option('page_on_front'));
$multiple=count($tracks)>1;
?>
<section class="rdc-feature" aria-label="جدیدترین آثار" data-rdc-feature>
 <div id="rdc-feature-track" class="rdc-feature__track" data-rdc-feature-track<?php if($multiple): ?> tabindex="0" role="group" aria-label="اسلایدهای آهنگ"<?php endif; ?>>
  <?php foreach($tracks as $index=>$track):
   $title=get_the_title($track);
   $meta=\RadmanCT\Music\meta($track->ID);
   $audio=\RadmanCT\Music\file($meta['audio']);
   $download=\RadmanCT\Music\download($track->ID,$meta,$audio);
  ?>
   <article class="rdc-feature__slide">
    <?php \RadmanCT\MusicHome\banner_picture($track,(array)($banners[$track->ID]??[]),$index===0); ?>
    <div class="rdc-feature__copy">
     <p class="rdc-feature__badge"><?php echo \RadmanCT\icon('music'); ?> <?php echo esc_html($data['label']); ?></p>
     <h2<?php if($index===0): ?> data-rdc-en="heading"<?php endif; ?>><?php if($index===0)\RadmanCT\entrance_words($title);else echo esc_html($title); ?></h2>
     <p class="rdc-feature__desc"<?php if($index===0): ?> data-rdc-en="text"<?php endif; ?>><?php echo esc_html($data['hero_desc']??get_the_excerpt($track)); ?></p>
     <div class="rdc-feature__actions">
      <?php
      if($audio)echo \RadmanCT\button('پخش آنلاین','#',['class'=>'rdc-feature__play','icon'=>'music','audio'=>\RadmanCT\Music\playback($track->ID,$audio,$meta)]);
      if($download)echo \RadmanCT\button('دانلود',$download['url'],['variant'=>'outline','icon'=>'download','download'=>$download['filename']]);
      echo \RadmanCT\button('شناسنامه',get_permalink($track),['variant'=>'outline','icon'=>'sliders']);
      ?>
     </div>
    </div>
   </article>
  <?php endforeach; ?>
 </div>
 <?php if($multiple): ?>
  <div class="rdc-feature__nav" data-rdc-feature-nav role="group" aria-label="انتخاب اثر" hidden>
   <?php foreach($tracks as $index=>$track): ?>
    <button type="button" data-rdc-feature-dot="<?php echo esc_attr($index); ?>" aria-controls="rdc-feature-track" aria-label="<?php echo esc_attr(sprintf('نمایش اسلاید %s: %s',number_format_i18n($index+1),get_the_title($track))); ?>"<?php if($index===0): ?> aria-current="true"<?php endif; ?>></button>
   <?php endforeach; ?>
  </div>
 <?php endif; ?>
</section>
