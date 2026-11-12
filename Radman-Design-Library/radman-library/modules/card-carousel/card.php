<?php
defined('ABSPATH')||exit;
$type=in_array($card['type']??'',array('stat','photo','progress','dark','video','icon'),true)?$card['type']:'icon';
$image=(int)($card['image']??0);$video=(int)($card['video']??0);
$image=\RadmanCT\PageTitle\attachment_allowed($image)?$image:0;
$video=\RadmanCT\PageTitle\attachment_allowed($video)&&str_starts_with((string)get_post_mime_type($video),'video/')?$video:0;
?>
<article class="rdl-cards__card rdl-cards__card--<?php echo esc_attr($type); ?>">
 <?php if($type==='video'&&$video): ?>
  <video class="rdl-cards__md" controls muted playsinline preload="none"<?php if($image)echo ' poster="'.esc_url(wp_get_attachment_image_url($image,'large')).'"'; ?> src="<?php echo esc_url(wp_get_attachment_url($video)); ?>" aria-label="<?php echo esc_attr($card['title']); ?>"></video>
 <?php elseif($image): echo wp_get_attachment_image($image,'large',false,array('class'=>'rdl-cards__md','alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width:600px) 82vw, 336px')); endif; ?>
 <?php if(!empty($card['value'])&&$type!=='progress'): ?><p class="rdl-cards__num" dir="ltr"><?php echo esc_html($card['value']); ?></p><?php endif; ?>
 <?php if(!empty($card['label'])&&$type!=='progress'): ?><p class="rdl-cards__lbl"><?php echo esc_html($card['label']); ?></p><?php endif; ?>
 <?php if(!empty($card['icon'])): ?><span class="rdl-cards__ico"><?php echo \RadmanCT\icon($card['icon']); ?></span><?php endif; ?>
 <div class="rdl-cards__copy"><h3><?php echo esc_html($card['title']); ?></h3><?php if(!empty($card['desc'])): ?><p><?php echo esc_html($card['desc']); ?></p><?php endif; ?></div>
 <?php if($type==='progress'): $value=max(0,min(100,(int)$card['value'])); ?><div class="rdl-cards__prg"><label><?php echo esc_html($card['label']); ?><progress max="100" value="<?php echo $value; ?>"><?php echo $value; ?>%</progress></label><span dir="ltr"><?php echo $value; ?>%</span></div><?php endif; ?>
</article>
