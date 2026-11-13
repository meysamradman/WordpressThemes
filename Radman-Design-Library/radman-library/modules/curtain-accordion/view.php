<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$data=\RadmanLibrary\Designs\CurtainAccordion\data();$group=wp_unique_id('rdl-crt-');$background=\RadmanLibrary\Designs\CurtainAccordion\allowed((int)$data['background'])?wp_get_attachment_image_url((int)$data['background'],'full'):''; ?>
<section class="rdl-crt" data-rdl-crt aria-labelledby="<?php echo esc_attr($group); ?>"<?php if($background): ?> style="--rdl-crt-art:url('<?php echo esc_url($background); ?>')"<?php endif; ?>>
 <h1 class="screen-reader-text" id="<?php echo esc_attr($group); ?>"><?php echo esc_html($data['title']); ?></h1>
 <div class="rdl-crt__row">
 <?php foreach(array_values($data['items']) as $index=>$item): ?>
  <details class="rdl-crt__itm" name="<?php echo esc_attr($group); ?>"<?php if(!$index)echo ' open'; ?>>
   <summary class="rdl-crt__tgl"><?php echo \RadmanLibrary\Designs\CurtainAccordion\media((int)$item['icon'],'full',array('class'=>'rdl-crt__ico','alt'=>'','aria-hidden'=>'true','loading'=>'eager')); ?><span><?php echo esc_html($item['title']); ?></span></summary>
   <div class="rdl-crt__cnt"><div class="rdl-crt__inr">
    <?php echo \RadmanLibrary\Designs\CurtainAccordion\media((int)$item['image'],'medium',array('class'=>'rdl-crt__md','alt'=>'','aria-hidden'=>'true','loading'=>'lazy','decoding'=>'async','sizes'=>'101px')); ?>
    <h2><?php echo esc_html($item['title']); ?></h2><p><?php echo esc_html($item['desc']); ?></p>
   </div></div>
  </details>
 <?php endforeach; ?>
 </div>
</section>
