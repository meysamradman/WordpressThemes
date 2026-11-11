<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$media=get_option('rdl_wellness_media',[]); ?>
<section class="rdl-well" data-rdl-well aria-label="کارت‌های افقی با اسکرول"><h1 class="screen-reader-text">مسیر همراهی و تندرستی</h1>
 <div class="rdl-well__win" tabindex="0" aria-label="کارت‌ها؛ در نمایشگر بزرگ با اسکرول صفحه حرکت می‌کنند"><div class="rdl-well__track">
  <?php foreach(\RadmanLibrary\Designs\Wellness\data() as $index=>$item): ?><article class="rdl-well__itm rdl-well__itm--<?php echo (int)$index; ?>">
   <figure class="rdl-well__im"><?php $id=(int)($media[$item['image']]??0);if(\RadmanCT\PageTitle\attachment_allowed($id))echo wp_get_attachment_image($id,'large',false,['alt'=>'','loading'=>$index?'lazy':'eager','sizes'=>'(min-width:960px) 45vw, 100vw','decoding'=>'async']); ?></figure>
   <div class="rdl-well__copy" dir="rtl"><h2><?php echo esc_html($item['title']); ?></h2><p><?php echo esc_html($item['description']); ?></p><?php echo \RadmanCT\button($item['button'],$item['url']); ?></div>
  </article><?php endforeach; ?>
 </div></div>
</section>
