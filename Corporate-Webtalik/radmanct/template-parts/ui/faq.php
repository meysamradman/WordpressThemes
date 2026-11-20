<?php
defined('ABSPATH')||exit;
$questions=$args['items']??array();
if(!$questions)return;
$group=wp_unique_id('rdc-faq-');
$heading=$group.'-ttl';
?>
<section class="rdc-faq rdc-ctr rdc-ctr--full" aria-labelledby="<?php echo esc_attr($heading); ?>">
 <header class="rdc-heading rdc-heading--stack">
  <div class="rdc-heading__inr">
   <p class="rdc-heading__lbl"><?php echo esc_html($args['label']??__('FAQ','radmanct')); ?><?php echo \RadmanCT\icon('arrow-up-right'); ?></p>
   <h2 class="rdc-heading__ttl" id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($args['title']??__('Frequently asked questions','radmanct')); ?></h2>
  </div>
  <?php $description=$args['desc']??__('Answers about our process, services and working together.','radmanct');if($description!==''): ?>
   <p class="rdc-heading__desc"><?php echo esc_html($description); ?></p>
  <?php endif; ?>
 </header>
 <div class="rdc-faq__inr">
  <?php foreach(array_values($questions) as $index=>$item): ?>
   <details class="rdc-faq__itm" name="<?php echo esc_attr($group); ?>"<?php if($index===0)echo ' open'; ?>>
    <summary>
     <span class="rdc-faq__num" aria-hidden="true"><?php echo esc_html(number_format_i18n($index+1)); ?></span>
     <span><?php echo esc_html($item[0]); ?></span>
     <span class="rdc-faq__ico" aria-hidden="true"></span>
    </summary>
    <p><?php echo esc_html($item[1]); ?></p>
   </details>
  <?php endforeach; ?>
 </div>
</section>
