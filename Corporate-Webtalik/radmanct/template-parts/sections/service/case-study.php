<?php
defined('ABSPATH')||exit;
$study=$args['case_study']??[];
if(!$study)return;
$project=$study['project'];
$image=wp_get_attachment_image($study['image'],'large',false,[
 'loading'=>'lazy',
 'decoding'=>'async',
 'sizes'=>'(min-width: 900px) min(46vw, 55rem), 94vw',
]);
if(!$image)return;
?>
<section class="rdc-ctr rdc-svc-case" aria-labelledby="rdc-svc-case-ttl">
 <header class="rdc-heading rdc-heading--stack">
  <div class="rdc-heading__inr">
   <p class="rdc-heading__lbl"><?php esc_html_e('نگاهی به یک نمونه‌کار','radmanct'); ?></p>
   <h2 class="rdc-heading__ttl" id="rdc-svc-case-ttl"><?php esc_html_e('از نیاز پروژه تا طراحی سایت','radmanct'); ?></h2>
  </div>
  <p class="rdc-heading__desc"><?php esc_html_e('طراحی سایت با شناخت نیاز مخاطب شروع می‌شود. از ساختار صفحه‌ها تا مسیر دسترسی به محتوا، هر تصمیم کمک می‌کند کاربر سریع‌تر به چیزی برسد که دنبال آن است.','radmanct'); ?></p>
 </header>
 <div class="rdc-svc-case__im"><?php echo $image; ?></div>
 <div class="rdc-svc-case__cnt">
  <h3 class="rdc-svc-case__ttl"><?php echo esc_html(get_the_title($project)); ?></h3>
  <p><?php echo esc_html($study['excerpt']); ?></p>
  <?php echo \RadmanCT\button(__('مشاهدهٔ جزئیات پروژه','radmanct'),get_permalink($project)); ?>
 </div>
</section>
