<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=\RadmanLibrary\Designs\DepthCarousel\data();$title=wp_unique_id('rdl-depth-');
?>
<section class="rdl-depth" data-rdl-depth dir="<?php echo esc_attr($data['direction']); ?>" aria-roledescription="کاروسل" aria-labelledby="<?php echo esc_attr($title); ?>">
 <header class="rdl-depth__hdr"><p>طراحی، از زاویه‌ای دیگر</p><h2 id="<?php echo esc_attr($title); ?>"><?php echo esc_html($data['title']); ?></h2><p><?php echo esc_html($data['desc']); ?></p></header>
 <div class="rdl-depth__stage" data-rdl-depth-stage tabindex="0" aria-label="کارت‌ها؛ پیمایش با کلیدهای چپ و راست یا کشیدن">
 <?php foreach($data['cards'] as $i=>$card):$image=(int)($card['image']??0);$image=\RadmanCT\PageTitle\attachment_allowed($image)?$image:0; ?>
 <article class="rdl-depth__card" data-rdl-depth-card role="group" aria-roledescription="اسلاید" aria-label="<?php echo esc_attr(($i+1).' از '.count($data['cards']).'؛ '.$card['title']); ?>">
  <div class="rdl-depth__browser" aria-hidden="true"><i></i><i></i><i></i><span>RADMAN / DIGITAL EXPERIENCES</span></div>
  <div class="rdl-depth__site">
   <div class="rdl-depth__nav" aria-hidden="true"><span>خانه</span><span>مجموعه‌ها</span><span>دربارهٔ ما</span><b>R.</b></div>
   <div class="rdl-depth__hero">
   <?php if($image)echo wp_get_attachment_image($image,'large',false,['alt'=>'','loading'=>$i===2?'eager':'lazy','decoding'=>'async','sizes'=>'(max-width:600px) 72vw, 400px']); ?>
    <div><small>نگاهی تازه به فردا</small><p><?php echo esc_html($card['desc']); ?></p><span>کشف یک تجربهٔ تازه ←</span></div>
   </div>
  </div>
  <div class="rdl-depth__copy"><span class="rdl-depth__ico" aria-hidden="true"><?php echo \RadmanCT\icon($card['icon']); ?></span><div><h3><?php echo esc_html($card['title']); ?></h3><p><?php echo esc_html($card['desc']); ?></p></div></div>
 </article>
 <?php endforeach; ?>
 </div>
 <nav class="rdl-depth__ctrl" aria-label="کنترل کاروسل" hidden>
  <button type="button" data-rdl-depth-prev aria-label="کارت قبلی"><?php echo \RadmanCT\icon('chevron-right'); ?></button>
  <div class="rdl-depth__dots"><?php foreach($data['cards'] as $i=>$card): ?><button type="button" data-rdl-depth-dot="<?php echo $i; ?>" aria-label="<?php echo esc_attr('نمایش '.$card['title']); ?>"></button><?php endforeach; ?></div>
  <button type="button" data-rdl-depth-next aria-label="کارت بعدی"><?php echo \RadmanCT\icon('chevron-left'); ?></button>
 </nav>
 <p class="screen-reader-text" data-rdl-depth-status aria-live="polite" aria-atomic="true"></p>
</section>
