<?php defined('ABSPATH')||exit;
$hash=\RadmanCT\Videos\aparat_id($args['aparat']??'');if($hash==='')return;
$title=(string)($args['title']??'ویدئو'); ?>
<div class="rdc-aparat">
 <iframe src="<?php echo esc_url('https://www.aparat.com/video/video/embed/videohash/'.$hash.'/vt/frame'); ?>" title="<?php echo esc_attr('پخش '.$title.' از آپارات'); ?>" width="1920" height="1080" loading="lazy" allow="fullscreen; encrypted-media; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>