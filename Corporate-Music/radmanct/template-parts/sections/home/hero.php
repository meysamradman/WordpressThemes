<?php defined('ABSPATH')||exit; $hero=\RadmanCT\Home\data(); ?>
<section class="rdc-hero rdc-inv" aria-label="<?php echo esc_attr($hero['heading']); ?>">
 <?php if(\RadmanCT\Home\image_id()): ?><div class="rdc-hero__md" aria-hidden="true"><?php get_template_part('template-parts/sections/home/hero-art'); ?></div><?php endif; ?>
 <div class="rdc-hero__inr"><div class="rdc-hero__copy" data-rdc-ent dir="<?php echo is_rtl()?'rtl':'ltr'; ?>">
 <div class="rdc-hero__ttl" data-rdc-en><?php \RadmanCT\Home\heading($hero['heading'],'p'); ?></div>
 <div class="rdc-hero__sttl" data-rdc-en="heading"><?php \RadmanCT\Home\heading($hero['subheading'],'h1'); ?></div>
 <div class="rdc-hero__acc" data-rdc-en><?php \RadmanCT\Home\heading($hero['accent'],'p','',true); ?></div>
 <div class="rdc-hero__desc" data-rdc-en="paragraph"><?php \RadmanCT\Home\heading($hero['description'],'p'); ?></div>
 <?php if($hero['button_url'])echo \RadmanCT\button($hero['button_label'],$hero['button_url'],array('class'=>'rdc-hero__cta','data-rdc-en'=>true)); ?>
 </div></div>
</section>
