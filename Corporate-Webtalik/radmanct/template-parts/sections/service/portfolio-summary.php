<?php
defined('ABSPATH')||exit;
require_once get_template_directory().'/inc/pages/service.php';
require_once get_template_directory().'/inc/ui/text-marquee.php';
$summary=\RadmanCT\Service\portfolio_summary();
?>
<section class="rdc-ctr rdc-work-summary" aria-labelledby="rdc-work-summary-ttl">
 <div class="rdc-heading rdc-work-summary__hdg">
  <div class="rdc-heading__inr">
   <p class="rdc-heading__lbl"><?php echo \RadmanCT\icon('arrow-up-right'); ?>کارنامهٔ وب‌تالیک</p>
   <h2 class="rdc-heading__ttl" id="rdc-work-summary-ttl">طراحی و توسعه در عمل</h2>
  </div>
  <dl class="rdc-work-summary__stats">
   <?php foreach($summary['metrics'] as $label=>$value): ?>
    <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html(number_format_i18n($value)); ?></dd></div>
   <?php endforeach; ?>
  </dl>
 </div>
 <?php if($summary['projects']): ?>
  <div class="rdc-work-summary__row">
   <?php \RadmanCT\UI\text_marquee($summary['projects'],static function(WP_Post $project):void { ?>
    <a class="rdc-work-summary__link" href="<?php echo esc_url(get_permalink($project)); ?>"><?php echo esc_html(get_the_title($project)); ?></a>
   <?php },'نمونه‌کارها'); ?>
  </div>
 <?php endif; ?>
</section>
