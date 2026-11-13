<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
require_once get_template_directory().'/shared/radmanct/inc/page-title.php';
$data=$args['data']??\RadmanLibrary\Designs\HeroTypography\data();
$heading=wp_unique_id('rdl-htx-ttl-');
$tag=($args['heading_tag']??'h1')==='h2'?'h2':'h1';
$image=(int)($data['image']??0);$background=(int)($data['background']??0);
?>
<section class="rdl-htx" data-rdl-htx aria-labelledby="<?php echo esc_attr($heading); ?>">
 <?php if(\RadmanCT\PageTitle\attachment_allowed($background)): ?>
  <div class="rdl-htx__bg" aria-hidden="true"><?php echo wp_get_attachment_image($background,'full',false,array('alt'=>'','loading'=>'eager','fetchpriority'=>'low','sizes'=>'100vw')); ?></div>
 <?php endif; ?>
 <div class="rdl-htx__grid">
  <div class="rdl-htx__main">
   <<?php echo $tag; ?> class="rdl-htx__ttl" id="<?php echo esc_attr($heading); ?>">
    <?php foreach(($data['rows']??array()) as $row): ?>
     <span class="rdl-htx__row" data-rdl-htx-row><?php foreach($row as $part): ?>
      <?php if(isset($part['text'])): ?><span><?php echo esc_html($part['text']); ?></span>
      <?php elseif(!empty($part['image'])&&\RadmanCT\PageTitle\attachment_allowed((int)$part['image'])): ?>
       <span class="rdl-htx__chip" aria-hidden="true"><?php echo wp_get_attachment_image((int)$part['image'],'thumbnail',false,array('alt'=>'','loading'=>'eager','fetchpriority'=>'low','sizes'=>'(max-width:800px) 48px, 90px')); ?></span>
      <?php elseif(!empty($part['mark'])): ?><span class="rdl-htx__mark" aria-hidden="true"></span><?php endif; ?>
     <?php endforeach; ?></span>
    <?php endforeach; ?>
   </<?php echo $tag; ?>>
   <p class="rdl-htx__desc"><?php echo esc_html($data['desc']); ?></p>
  </div>
  <div class="rdl-htx__side">
   <p class="rdl-htx__intro"><?php echo esc_html($data['intro']); ?></p>
   <div class="rdl-htx__bottom">
    <?php if(\RadmanCT\PageTitle\attachment_allowed($image)): ?>
     <figure class="rdl-htx__media"><?php echo wp_get_attachment_image($image,'large',false,array('class'=>'rdl-htx__im','loading'=>'eager','fetchpriority'=>'high','sizes'=>'(max-width:800px) calc(100vw - 40px), 28vw')); ?></figure>
    <?php endif; ?>
    <?php if(!empty($data['socials'])): ?>
     <ul class="rdl-htx__socials" aria-label="شبکه‌های اجتماعی">
      <?php foreach($data['socials'] as $social): ?><li><?php if(!empty($social['url'])): ?><a href="<?php echo esc_url($social['url']); ?>"><?php echo esc_html($social['label']); ?></a><?php else: ?><span><?php echo esc_html($social['label']); ?></span><?php endif; ?></li><?php endforeach; ?>
     </ul>
    <?php endif; ?>
   </div>
  </div>
 </div>
</section>
