<?php
defined('ABSPATH')||exit;
$study=$args['case_study']??[];
if(!$study)return;
$project=$study['project'];
$copy=apply_filters('radmanct/service/case-copy',array());
$image=wp_get_attachment_image($study['image'],'large',false,[
 'loading'=>'lazy',
 'decoding'=>'async',
 'sizes'=>'(min-width: 900px) min(46vw, 55rem), 94vw',
]);
if(!$image)return;
?>
<section class="rdc-ctr rdc-ctr--wide rdc-svc-case" aria-labelledby="rdc-svc-case-ttl">
 <header class="rdc-heading rdc-heading--stack">
  <div class="rdc-heading__inr">
   <p class="rdc-heading__lbl"><?php echo esc_html($copy?'مثال کاربردی':__('نگاهی به یک نمونه‌کار','radmanct')); ?></p>
   <h2 class="rdc-heading__ttl" id="rdc-svc-case-ttl"><?php echo esc_html($copy['heading']??__('از نیاز پروژه تا طراحی سایت','radmanct')); ?></h2>
  </div>
  <p class="rdc-heading__desc"><?php echo esc_html($copy['intro']??__('طراحی سایت با شناخت نیاز مخاطب شروع می‌شود. از ساختار صفحه‌ها تا مسیر دسترسی به محتوا، هر تصمیم کمک می‌کند کاربر سریع‌تر به چیزی برسد که دنبال آن است.','radmanct')); ?></p>
 </header>
 <div class="rdc-svc-case__im rdc-md-frm"><?php echo $image; ?></div>
 <div class="rdc-svc-case__cnt">
  <h3 class="rdc-svc-case__ttl"><?php echo esc_html($copy['title']??get_the_title($project)); ?></h3>
  <p><?php echo esc_html($copy['desc']??$study['excerpt']); ?></p>
  <?php echo $copy
   ? \RadmanCT\button('درباره پروژه گفت‌وگو کنیم',home_url('/contact-us/'))
   : \RadmanCT\button(__('مشاهدهٔ جزئیات پروژه','radmanct'),get_permalink($project)); ?>
 </div>
</section>
