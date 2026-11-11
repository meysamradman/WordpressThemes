<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=\RadmanLibrary\Designs\Process\data();
$tag=in_array($args['heading_tag']??'h2',['h1','h2'],true)?($args['heading_tag']??'h2'):'h2';
$heading=wp_unique_id('rdl-steps-ttl-');
?>
<section class="rdl-steps rdc-inv" aria-labelledby="<?php echo esc_attr($heading); ?>" data-rdl-steps>
 <div class="rdl-steps__hdr">
  <div><p class="rdl-steps__eb"><span aria-hidden="true"></span><strong><?php echo esc_html($data['label']); ?></strong><span><?php echo esc_html($data['meta']); ?></span></p>
   <<?php echo $tag; ?> id="<?php echo esc_attr($heading); ?>" class="rdl-steps__ttl"><?php foreach($data['title'] as $line): ?><span><?php echo esc_html($line); ?></span><?php endforeach; ?></<?php echo $tag; ?>>
  </div>
  <div class="rdl-steps__lead"><p><?php echo esc_html($data['description']); ?></p><?php echo \RadmanCT\button($data['button']['label'],$data['button']['url']); ?></div>
 </div>
 <ol class="rdl-steps__list">
  <?php foreach($data['items'] as $index=>$item): ?><li class="rdl-steps__itm">
   <span class="rdl-steps__dot" aria-hidden="true"></span><p class="rdl-steps__num" aria-hidden="true"><?php echo esc_html(str_pad((string)($index+1),2,'0',STR_PAD_LEFT)); ?></p>
   <h3><?php echo esc_html($item['title']); ?></h3><p class="rdl-steps__desc"><?php echo esc_html($item['description']); ?></p><p class="rdl-steps__time"><?php echo esc_html($item['time']); ?></p>
  </li><?php endforeach; ?>
 </ol>
</section>
