<?php
defined('ABSPATH')||exit;
require_once get_template_directory().'/inc/ui/text-marquee.php';
$summary=$args['summary'];
$topics=apply_filters('radmanct/service/topic-links',array());
?>
<section class="rdc-ctr rdc-ctr--wide rdc-work-summary" aria-labelledby="rdc-work-summary-ttl">
 <div class="rdc-heading rdc-work-summary__hdg">
  <div class="rdc-heading__inr">
   <p class="rdc-heading__lbl"><?php echo \RadmanCT\icon('arrow-up-right'); ?>کارنامهٔ وبتالیک</p>
   <h2 class="rdc-heading__ttl" id="rdc-work-summary-ttl">نمونه‌هایی از طراحی و توسعه</h2>
  </div>
  <dl class="rdc-work-summary__stats">
   <?php foreach($summary['metrics'] as $label=>$value): ?>
    <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html(number_format_i18n($value)); ?></dd></div>
   <?php endforeach; ?>
  </dl>
 </div>
 <?php if($topics): ?>
  <div class="rdc-work-summary__row">
   <?php \RadmanCT\UI\text_marquee(array_keys($topics),static function(string $label)use($topics):void { ?>
    <a class="rdc-work-summary__link" href="<?php echo esc_url($topics[$label]); ?>"><?php echo esc_html($label); ?></a>
   <?php },'موضوعات طراحی سایت'); ?>
  </div>
 <?php endif; ?>
</section>
