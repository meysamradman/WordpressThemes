<?php defined('ABSPATH')||exit;$data=$args['data']??\RadmanCT\Videos\data();$tag=($args['heading_tag']??'h3')==='h2'?'h2':'h3';$inline=!empty($args['inline']); ?>
<div class="rdc-video-grid">
<?php foreach($data['items'] as $item):$file=\RadmanCT\Videos\file((int)$item['video']);$poster=(int)($item['image']??0);$url=$item['url']??''; ?>
 <article class="rdc-video-card"><div class="rdc-video-card__media">
 <?php if($inline&&$file): ?><video controls playsinline preload="none" width="1920" height="1080"<?php if(\RadmanCT\PageTitle\attachment_allowed($poster)): ?> poster="<?php echo esc_url(wp_get_attachment_image_url($poster,'medium_large')); ?>"<?php endif; ?> aria-label="<?php echo esc_attr($item['title']); ?>"><source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['type']); ?>"></video>
 <?php else: ?><a class="rdc-video-card__cover" href="<?php echo esc_url($url); ?>" aria-label="<?php echo esc_attr('مشاهدهٔ '.$item['title']); ?>"><?php if(\RadmanCT\PageTitle\attachment_allowed($poster))echo wp_get_attachment_image($poster,'medium_large',false,['loading'=>'lazy','sizes'=>'(max-width:700px) 90vw,400px']); ?><span class="rdc-video-card__play"><?php echo \RadmanCT\icon('play'); ?></span></a><?php endif; ?>
 </div><div class="rdc-video-card__copy"><<?php echo $tag; ?>><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($item['title']); ?></a></<?php echo $tag; ?>><p><?php echo esc_html($item['desc']); ?></p></div></article>
<?php endforeach; ?></div>
