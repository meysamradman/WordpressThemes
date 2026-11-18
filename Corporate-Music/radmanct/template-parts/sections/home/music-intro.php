<?php defined('ABSPATH')||exit;$data=\RadmanCT\MusicHome\data();$tracks=\RadmanCT\MusicHome\featured();if(!$tracks)return;$multiple=count($tracks)>1; ?>
<section class="rdc-feature" aria-label="جدیدترین آثار" data-rdc-feature>
<?php if($multiple): ?>
 <div class="rdc-feature__nav" data-rdc-feature-nav hidden>
  <button type="button" data-rdc-feature-prev aria-label="اثر قبلی"><?php echo \RadmanCT\icon('chevron-right'); ?></button>
  <button type="button" data-rdc-feature-next aria-label="اثر بعدی"><?php echo \RadmanCT\icon('chevron-left'); ?></button>
 </div>
<?php endif; ?>
<div class="rdc-feature__track" data-rdc-feature-track<?php if($multiple): ?> tabindex="0" role="group" aria-label="اسلایدهای آهنگ"<?php endif; ?>>
<?php foreach($tracks as $index=>$track):$meta=\RadmanCT\Music\meta($track->ID);$audio=\RadmanCT\Music\file((int)$meta['audio']);$download=\RadmanCT\Music\file((int)($meta['download']?:$meta['audio'])); ?>
 <article class="rdc-feature__slide"><div class="rdc-feature__copy"><p class="rdc-feature__badge"><?php echo \RadmanCT\icon('music'); ?> <?php echo esc_html($data['label']); ?></p><h2<?php if($index===0): ?> data-rdc-en="heading"<?php endif; ?>><?php if($index===0)\RadmanCT\entrance_words(get_the_title($track));else echo esc_html(get_the_title($track)); ?></h2><p class="rdc-feature__desc"<?php if($index===0): ?> data-rdc-en="text"<?php endif; ?>><?php echo esc_html($data['hero_desc']??get_the_excerpt($track)); ?></p><div class="rdc-feature__actions">
 <?php if($audio)echo \RadmanCT\button('پخش آنلاین','#',array('class'=>'rdc-feature__play','icon'=>'music','audio'=>\RadmanCT\Music\playback($track->ID,$audio,$meta)));if($download)echo \RadmanCT\button('دانلود',$download['url'],array('variant'=>'outline','icon'=>'download','download'=>true));echo \RadmanCT\button('شناسنامه',get_permalink($track),array('variant'=>'outline','icon'=>'sliders')); ?>
 </div></div><figure class="rdc-feature__cover"><?php echo \RadmanCT\MusicHome\image((int)get_post_thumbnail_id($track),'large',array('loading'=>$index===0?'eager':'lazy','fetchpriority'=>$index===0?'high':'auto','sizes'=>'(max-width:650px) 70vw,320px','alt'=>get_the_title($track))); ?></figure></article>
<?php endforeach; ?></div></section>
