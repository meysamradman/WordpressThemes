<?php
defined('ABSPATH')||exit;
$data=apply_filters('radmanct/services/marquee',array());
$images=array();
foreach($data['images']??array() as $item){
 $id=(int)($item['id']??0);
 if(!\RadmanCT\PageTitle\attachment_allowed($id)||!wp_attachment_is_image($id))continue;
 $images[]=array('id'=>$id,'label'=>(string)($item['label']??''));
}
if(!$images)return;
$heading=wp_unique_id('rdc-prj-mq-ttl-');
?>
<section class="rdc-prj-mq rdc-inv" aria-labelledby="<?php echo esc_attr($heading); ?>" data-rdc-prj-mq>
 <header class="rdc-prj-mq__hdr"><a href="<?php echo esc_url($data['url']); ?>"><h2 class="rdc-prj-mq__ttl" id="<?php echo esc_attr($heading); ?>"><?php foreach($data['title'] as $line): ?><span><?php echo esc_html($line); ?></span><?php endforeach; ?></h2></a></header>
 <div class="rdc-prj-mq__win" tabindex="0" aria-label="گالری حوزه‌های طراحی">
  <div class="rdc-prj-mq__trk"><ul class="rdc-prj-mq__grp">
   <?php foreach($images as $item): ?>
    <li class="rdc-prj-mq__itm"><p><?php echo esc_html($item['label']); ?></p><?php echo wp_get_attachment_image($item['id'],'medium_large',false,array('alt'=>$item['label'],'loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width:600px) 220px, 300px')); ?></li>
   <?php endforeach; ?>
  </ul></div>
 </div>
</section>
