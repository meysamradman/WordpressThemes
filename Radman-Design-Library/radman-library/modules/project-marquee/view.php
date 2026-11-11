<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=\RadmanLibrary\Designs\Marquee\data();
$tag=in_array($args['heading_tag']??'h2',['h1','h2'],true)?($args['heading_tag']??'h2'):'h2';
$heading=wp_unique_id('rdl-mq-ttl-');
?>
<section class="rdl-mq rdc-inv" aria-labelledby="<?php echo esc_attr($heading); ?>" data-rdl-mq>
 <header class="rdl-mq__hdr"><a href="<?php echo esc_url($data['url']); ?>"><<?php echo $tag; ?> class="rdl-mq__ttl" id="<?php echo esc_attr($heading); ?>"><?php foreach($data['title'] as $line): ?><span><?php echo esc_html($line); ?></span><?php endforeach; ?></<?php echo $tag; ?>></a><button class="rdl-mq__play" type="button" hidden aria-pressed="false" data-rdl-mq-play>توقف حرکت</button></header>
 <div class="rdl-mq__win" tabindex="0" aria-label="گالری حوزه‌های طراحی">
  <div class="rdl-mq__track"><ul class="rdl-mq__grp">
   <?php foreach($data['images'] as $item): $id=(int)$item['id'];if(!\RadmanCT\PageTitle\attachment_allowed($id))continue; ?>
    <li class="rdl-mq__itm"><p><?php echo esc_html($item['label']); ?></p><?php echo wp_get_attachment_image($id,'medium_large',false,['alt'=>$item['label'],'loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width:600px) 220px, 300px']); ?></li>
   <?php endforeach; ?>
  </ul></div>
 </div>
</section>
