<?php defined('ABSPATH')||exit;$data=$args['data']??\RadmanCT\Videos\data();$tag=($args['heading_tag']??'h3')==='h2'?'h2':'h3';$inline=!empty($args['inline']);$rail=!empty($args['rail']);
$download_class='rdc-video-card__download';
$download_appearance=[];
if($rail){
 $download_class.=' rdc-video-card__download--compact';
 $download_appearance=['variant'=>'solid','icon'=>'download','icon_only'=>true];
} ?>
<div class="rdc-video-grid<?php if($rail)echo ' rdc-music-rail rdc-music-rail--video'; ?>"<?php if(!empty($args['archive_items']))echo ' data-rdc-arc-items'; ?><?php if($rail): ?> data-rdc-rail-track tabindex="0" role="group" aria-label="ویدئوها؛ جابه‌جایی با کلیدهای چپ و راست"<?php endif; ?>>
<?php foreach($data['items'] as $item):$file=empty($item['aparat'])?\RadmanCT\Videos\file((int)$item['video']):[];$poster=(int)($item['image']??0);$url=$item['url']??''; ?>
 <article class="rdc-video-card"><div class="rdc-video-card__media">
 <?php if($inline&&empty($item['aparat'])&&$file): ?><video controls playsinline preload="none" width="1920" height="1080"<?php if(\RadmanCT\PageTitle\attachment_allowed($poster)): ?> poster="<?php echo esc_url(wp_get_attachment_image_url($poster,'medium_large')); ?>"<?php endif; ?> aria-label="<?php echo esc_attr($item['title']); ?>"><source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['type']); ?>"></video><button class="rdc-video-card__toggle" type="button" data-rdc-video-toggle hidden aria-label="<?php echo esc_attr('پخش '.$item['title']); ?>"><?php echo \RadmanCT\icon('play'); ?></button>
 <?php else: $aparat=$inline&&!empty($item['aparat']);$observe=$aparat&&$rail; ?>
  <a class="rdc-video-card__cover rdc-media-frame" href="<?php echo esc_url($url); ?>"<?php if($observe): ?> data-rdc-aparat-visible<?php elseif($aparat): ?> data-rdc-aparat-open<?php endif; ?> aria-label="<?php echo esc_attr('مشاهدهٔ '.$item['title']); ?>">
   <?php if(\RadmanCT\PageTitle\attachment_allowed($poster))echo wp_get_attachment_image($poster,'medium_large',false,['loading'=>'lazy','sizes'=>$rail?'(max-width:650px) 80vw,(max-width:900px) 42vw,460px':'(max-width:700px) 90vw,460px']); ?>
   <?php if(!$observe): ?><span class="rdc-video-card__play"><?php echo \RadmanCT\icon('play'); ?></span><?php endif; ?>
  </a>
  <?php if($aparat): ?><template data-rdc-aparat-tpl><?php get_template_part('template-parts/ui/aparat',null,$item); ?></template><?php endif; ?>
 <?php endif; ?>
 </div><div class="rdc-video-card__copy"><<?php echo $tag; ?>><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($item['title']); ?></a></<?php echo $tag; ?>><p><?php echo esc_html($item['desc']); ?></p><?php if(!empty($args['actions'])):
 if(!empty($item['aparat'])):
  echo \RadmanCT\Videos\aparat_download_button($item['aparat'],$download_class,$download_appearance);
 else:
 $downloads=\RadmanCT\Videos\downloads((int)($item['id']??0));
 $download=$downloads[1080]?:($downloads[720]?:($downloads[480]?:$file));
 if($download)echo \RadmanCT\button('دانلود',$download['url'],['class'=>$download_class,'download'=>true]+$download_appearance+['variant'=>'download']);
 endif; endif; ?></div></article>
<?php endforeach;
if($rail&&!empty($args['more']))get_template_part('template-parts/sections/home/rail-more',null,$args['more']);
?></div>
