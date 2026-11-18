<?php defined('ABSPATH')||exit;
$url=\RadmanCT\Videos\embed_url($args['aparat']??'');if($url==='')return;
$title=(string)($args['title']??'ویدئو'); ?>
<div class="rdc-aparat">
 <iframe src="<?php echo esc_url($url); ?>" title="<?php echo esc_attr('پخش '.$title.' از آپارات'); ?>" width="1920" height="1080" loading="lazy" allow="fullscreen; encrypted-media; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>
