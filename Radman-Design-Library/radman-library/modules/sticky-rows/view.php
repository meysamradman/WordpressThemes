<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=\RadmanLibrary\Designs\StickyRows\data();
if(empty($data['items']))return;
$title=wp_unique_id('rdl-stk-rows-ttl-');
?>
<section class="rdl-stk-rows" data-rdl-stk-rows aria-labelledby="<?php echo esc_attr($title); ?>">
 <h1 class="rdl-stk-rows__ttl" id="<?php echo esc_attr($title); ?>"><?php echo esc_html($data['title']); ?></h1>
 <div class="rdl-stk-rows__list">
  <?php foreach(array_values($data['items']) as $index=>$item):
   $image=(int)($item['image']??0);
   $media=$image&&\RadmanCT\PageTitle\attachment_allowed($image)
    ?wp_get_attachment_image($image,'large',false,['loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 767px) 100vw, (max-width: 991px) 50vw, min(38vw, 40rem)']):'';
  ?>
   <article class="rdl-stk-rows__itm">
    <span class="rdl-stk-rows__num" aria-hidden="true"><?php printf('%02d',$index+1); ?></span>
    <div class="rdl-stk-rows__cnt">
     <h2><?php if(!empty($item['title_ltr'])): ?><bdi dir="ltr"><?php echo esc_html($item['title']); ?></bdi><?php else: ?><?php echo esc_html($item['title']); ?><?php endif; ?></h2>
     <p><?php echo esc_html($item['desc']); ?></p>
     <?php if(!empty($item['url'])&&!empty($item['link']))echo \RadmanCT\button($item['link'],$item['url'],['variant'=>'outline']); ?>
    </div>
    <div class="rdl-stk-rows__im"<?php if(!$media)echo ' aria-hidden="true"'; ?>>
     <?php if($media)echo $media; ?>
    </div>
   </article>
  <?php endforeach; ?>
 </div>
</section>
