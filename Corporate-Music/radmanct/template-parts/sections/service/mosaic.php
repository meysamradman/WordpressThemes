<?php
defined('ABSPATH')||exit;
require_once get_template_directory().'/inc/pages/service.php';
$data=$args['data']??\RadmanCT\Service\data();
if(empty($data['items']))return;
$heading=wp_unique_id('rdc-mosaic-ttl-');
?>
<section class="rdc-mosaic" aria-labelledby="<?php echo esc_attr($heading); ?>">
 <div class="rdc-mosaic__inr">
  <header class="rdc-mosaic__hdr">
   <p class="rdc-mosaic__lbl"><?php echo esc_html($data['label']); ?></p>
   <h2 id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></h2>
  </header>
  <div class="rdc-mosaic__grid">
   <?php foreach($data['items'] as $item): $image=(int)($item['image']??0);$video=(int)($item['video']??0); ?>
    <article class="rdc-mosaic__itm">
     <div class="rdc-mosaic__copy">
      <span class="rdc-mosaic__rim" aria-hidden="true"></span>
      <h3><?php echo esc_html($item['title']); ?></h3>
      <div class="rdc-mosaic__cnt"><p><?php echo esc_html($item['desc']); ?></p>
       <?php if(!empty($item['url']))echo \RadmanCT\button($item['link'],$item['url'],array('variant'=>'ghost')); ?>
      </div>
     </div>
     <div class="rdc-mosaic__md">
      <span class="rdc-mosaic__rim" aria-hidden="true"></span>
      <?php if($video&&\RadmanCT\Service\allowed($video)&&in_array(get_post_mime_type($video),array('video/mp4','video/webm'),true)):
       $url=wp_get_attachment_url($video);$uploads=wp_upload_dir();$meta=wp_get_attachment_metadata($video);
       $poster=\RadmanCT\Service\allowed($image)?wp_get_attachment_image_url($image,'large'):'';
       if($url&&str_starts_with($url,trailingslashit($uploads['baseurl']))): ?>
        <video controls playsinline preload="none" width="<?php echo (int)($meta['width']??1920); ?>" height="<?php echo (int)($meta['height']??1080); ?>"<?php if($poster): ?> poster="<?php echo esc_url($poster); ?>"<?php endif; ?> aria-label="<?php echo esc_attr($item['title']); ?>">
         <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr(get_post_mime_type($video)); ?>">
         <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($item['title']); ?></a>
        </video>
       <?php endif; ?>
      <?php elseif(\RadmanCT\Service\allowed($image)):
       echo wp_get_attachment_image($image,'large',false,array('loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width:639px) calc(100vw - 32px), (max-width:1023px) 50vw, 25vw'));
      endif; ?>
     </div>
    </article>
   <?php endforeach; ?>
  </div>
 </div>
</section>
