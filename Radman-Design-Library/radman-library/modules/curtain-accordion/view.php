<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$data=\RadmanLibrary\Designs\CurtainAccordion\data();$group=wp_unique_id('rdl-crt-'); ?>
<section class="rdl-crt" data-rdl-crt aria-labelledby="<?php echo esc_attr($group); ?>">
 <h1 class="screen-reader-text" id="<?php echo esc_attr($group); ?>"><?php echo esc_html($data['title']); ?></h1>
 <div class="rdl-crt__row">
 <?php foreach(array_values($data['items']) as $index=>$item): ?>
  <details class="rdl-crt__itm" name="<?php echo esc_attr($group); ?>"<?php if(!$index)echo ' open'; ?>>
   <?php echo \RadmanLibrary\Designs\CurtainAccordion\media((int)$data['background'],'full',array('class'=>'rdl-crt__bg','alt'=>'','aria-hidden'=>'true','loading'=>'eager','decoding'=>'async','sizes'=>'(max-width:991px) 100vw, 55vw')); ?>
   <summary class="rdl-crt__tgl"><?php echo \RadmanLibrary\Designs\CurtainAccordion\media((int)$item['icon'],'full',array('class'=>'rdl-crt__ico','alt'=>'','aria-hidden'=>'true','loading'=>'eager')); ?><span><?php echo esc_html($item['title']); ?></span></summary>
   <div class="rdl-crt__cnt"><div class="rdl-crt__inr">
    <?php echo \RadmanLibrary\Designs\CurtainAccordion\media((int)$item['image'],'medium',array('class'=>'rdl-crt__md','alt'=>'','aria-hidden'=>'true','loading'=>'lazy','decoding'=>'async','sizes'=>'101px')); ?>
    <h2><?php echo esc_html($item['title']); ?></h2><p><?php echo esc_html($item['desc']); ?></p>
   </div></div>
  </details>
 <?php endforeach; ?>
 </div>
</section>
