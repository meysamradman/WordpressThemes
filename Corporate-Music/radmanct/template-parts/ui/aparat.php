<?php defined('ABSPATH')||exit;
$hash=\RadmanCT\Videos\aparat_id($args['aparat']??'');if($hash==='')return;
$title=(string)($args['title']??'ویدئو');$poster=(int)($args['image']??0); ?>
<div class="rdc-aparat" data-rdc-aparat="<?php echo esc_attr($hash); ?>">
 <a class="rdc-aparat__cover" href="<?php echo esc_url('https://www.aparat.com/v/'.$hash); ?>" data-rdc-aparat-play aria-label="<?php echo esc_attr('پخش '.$title.' از آپارات'); ?>">
  <?php if(\RadmanCT\PageTitle\attachment_allowed($poster))echo wp_get_attachment_image($poster,'large',false,['loading'=>'lazy','sizes'=>'(max-width:700px) 90vw,800px']); ?>
  <span class="rdc-video-card__play" aria-hidden="true"><?php echo \RadmanCT\icon('play'); ?></span>
 </a>
</div>
