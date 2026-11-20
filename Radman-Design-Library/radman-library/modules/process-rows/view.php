<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=$args['data']??\RadmanLibrary\Designs\ProcessRows\data();
if(empty($data['title'])||empty($data['steps']))return;
$heading_id=wp_unique_id('rdc-svc-process-ttl-');
$heading_tag=($args['heading_tag']??'h2')==='h1'?'h1':'h2';
$step_tag=$heading_tag==='h1'?'h2':'h3';
?>
<section class="rdc-ctr rdc-ctr--wide rdc-svc-process" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
 <header class="rdc-heading">
  <div class="rdc-heading__inr">
   <?php if(!empty($data['label'])): ?><span class="rdc-heading__lbl"><?php echo \RadmanCT\icon('arrow-up-right'); ?><?php echo esc_html($data['label']); ?></span><?php endif; ?>
   <<?php echo $heading_tag; ?> class="rdc-heading__ttl" id="<?php echo esc_attr($heading_id); ?>"><?php echo esc_html($data['title']); ?></<?php echo $heading_tag; ?>>
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
     <<?php echo $step_tag; ?> class="rdc-svc-process__ttl"><?php echo esc_html($step['title']); ?></<?php echo $step_tag; ?>>
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
