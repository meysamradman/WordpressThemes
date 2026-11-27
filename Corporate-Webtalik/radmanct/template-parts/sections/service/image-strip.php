<?php
defined('ABSPATH')||exit;

$images=[];
foreach(apply_filters('radmanct/services/strip',[]) as $id){
 if(!\RadmanCT\PageTitle\attachment_allowed((int)$id)||!wp_attachment_is_image($id))continue;
 $image=wp_get_attachment_image($id,'full',false,[
  'loading'=>'lazy','decoding'=>'async','draggable'=>'false',
  'sizes'=>'(min-width: 992px) calc((100vw - 30px) / 2), 100vw',
 ]);
 if($image)$images[]=$image;
}
if(!$images)return;
?>
<section class="rdc-svc-strip" id="rdc-svc-strip" tabindex="0" aria-label="<?php esc_attr_e('Gallery','radmanct'); ?>" data-rdc-svc-strip>
 <ul class="rdc-svc-strip__inr">
  <?php foreach($images as $image): ?><li class="rdc-md-frm"><?php echo $image; ?></li><?php endforeach; ?>
 </ul>
</section>
