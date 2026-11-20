<?php
defined('ABSPATH')||exit;
$data=apply_filters('radmanct/service/process',[]);
if(empty($data['title'])||empty($data['steps']))return;
?>
<section class="rdc-ctr rdc-svc-process" id="rdc-svc-process" aria-labelledby="rdc-svc-process-ttl">
 <header class="rdc-heading">
  <div class="rdc-heading__inr">
   <span class="rdc-heading__lbl"><?php echo \RadmanCT\icon('arrow-up-right'); ?><?php esc_html_e('قدم‌به‌قدم','radmanct'); ?></span>
   <h2 class="rdc-heading__ttl" id="rdc-svc-process-ttl"><?php echo esc_html($data['title']); ?></h2>
  </div>
  <p class="rdc-heading__desc"><?php echo esc_html($data['desc']); ?></p>
 </header>
 <ol class="rdc-svc-process__list" role="list">
  <?php foreach(array_values($data['steps']) as $index=>$step):
   $image=(int)($step['image']??0);
   $media=\RadmanCT\PageTitle\attachment_allowed($image)?wp_get_attachment_image($image,'medium',false,[
    'alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 479px) 160px, (max-width: 767px) 112px, 192px',
   ]):'';
  ?>
   <li class="rdc-svc-process__itm">
    <?php if($media): ?><div class="rdc-svc-process__im"><?php echo $media; ?></div><?php endif; ?>
    <div class="rdc-svc-process__copy">
     <span class="rdc-svc-process__num" aria-hidden="true"><?php printf('%02d',$index+1); ?></span>
     <h3><?php echo esc_html($step['title']); ?></h3>
     <p><?php echo esc_html($step['desc']); ?></p>
    </div>
    <p class="rdc-svc-process__result">
     <span><?php esc_html_e('خروجی مرحله','radmanct'); ?></span>
     <?php echo esc_html($step['result']); ?>
    </p>
   </li>
  <?php endforeach; ?>
 </ol>
</section>
