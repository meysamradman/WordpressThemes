<?php defined('ABSPATH')||exit;$items=\RadmanCT\Services\items();$count=wp_count_posts('project'); ?>
<section class="rdc-ctr rdc-svc-show" data-rdc-svc-show aria-labelledby="services-showcase-title">
 <header class="rdc-svc-show__hdr"><h2 id="services-showcase-title"><?php esc_html_e('Our solutions','radmanct'); ?><?php echo \RadmanCT\icon('arrow-up-right'); ?></h2><p><?php esc_html_e('Design, development and growth','radmanct'); ?></p></header>
 <div class="rdc-svc-show__ly">
  <aside class="rdc-svc-show__vis" dir="<?php echo is_rtl()?'rtl':'ltr'; ?>" aria-label="<?php esc_attr_e('Service preview','radmanct'); ?>">
   <div class="rdc-svc-show__stg" aria-hidden="true">
    <?php foreach($items as $index=>$item): ?><figure data-rdc-svc-im="<?php echo (int)$index; ?>" <?php if($index)echo 'hidden'; ?>><?php if($item['image'])echo wp_get_attachment_image($item['image'],'large',false,array('alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 767px) 100vw, 32vw')); ?></figure><?php endforeach; ?>
   </div>
   <p class="rdc-svc-show__stat"><strong><?php echo esc_html(number_format_i18n((int)($count->publish??0))); ?></strong><a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"><?php esc_html_e('Website projects','radmanct'); ?></a></p>
  </aside>
  <div class="rdc-svc-show__list" dir="<?php echo is_rtl()?'rtl':'ltr'; ?>">
   <?php foreach($items as $index=>$item): ?>
    <details class="rdc-svc-itm" data-rdc-svc="<?php echo (int)$index; ?>" <?php if(!$index)echo 'open'; ?>>
     <summary><h3><span class="rdc-svc-itm__num" aria-hidden="true">[<?php echo esc_html(str_pad((string)($index+1),2,'0',STR_PAD_LEFT)); ?>]</span><span class="rdc-svc-itm__ttl"><?php echo esc_html($item['title']); ?></span><span class="rdc-svc-itm__thm" aria-hidden="true"><?php if($item['image'])echo wp_get_attachment_image($item['image'],'medium',false,array('alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 767px) 88px, 180px')); ?></span></h3></summary>
     <div class="rdc-svc-itm__cnt"><div><p><?php echo esc_html($item['description']); ?></p><?php if($item['url']): ?><a href="<?php echo esc_url($item['url']); ?>"><?php esc_html_e('Explore this service','radmanct'); ?><?php echo \RadmanCT\icon('arrow-up-right'); ?></a><?php endif; ?></div></div>
    </details>
   <?php endforeach; ?>
  </div>
 </div>
</section>
