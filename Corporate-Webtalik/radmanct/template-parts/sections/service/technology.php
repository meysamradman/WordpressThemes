<?php
defined('ABSPATH')||exit;
$data=apply_filters('radmanct/service/sticky-rows',array());
if(empty($data['title'])||empty($data['items']))return;
?>
<section id="rdc-svc-technologies" class="rdc-svc-tech rdc-ctr rdc-ctr--wide" aria-labelledby="rdc-svc-tech-ttl">
 <header class="rdc-heading">
  <div class="rdc-heading__inr">
   <?php if(!empty($data['label'])): ?><p class="rdc-heading__lbl"><?php echo esc_html($data['label']); ?></p><?php endif; ?>
   <h2 class="rdc-heading__ttl" id="rdc-svc-tech-ttl"><?php echo esc_html($data['title']); ?></h2>
  </div>
  <?php if(!empty($data['desc'])): ?><p class="rdc-heading__desc"><?php echo esc_html($data['desc']); ?></p><?php endif; ?>
 </header>
 <div class="rdc-ss" data-rdc-svc-tech data-mode="services" data-transition="slide-stack" data-min-width="1024" data-min-scale=".9" data-offset="20">
  <?php foreach($data['items'] as $item):
   $id=(int)($item['image']??0);
   $image=\RadmanCT\PageTitle\attachment_allowed($id)?wp_get_attachment_image($id,'full',false,[
    'loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 1023px) 90vw, min(41vw, 44rem)',
   ]):'';
  ?>
   <div class="rdc-ss__itm">
    <article class="rdc-ss__inr rdc-svc-tech__itm">
     <div class="rdc-svc-tech__copy">
      <h3 class="rdc-svc-tech__ttl" data-rdc-pnl-ttl>
       <bdi dir="<?php echo !empty($item['title_ltr'])?'ltr':'rtl'; ?>"><?php echo esc_html($item['title']); ?></bdi>
      </h3>
      <?php if(!empty($item['subtitle'])): ?><p class="rdc-svc-tech__sub" data-rdc-pnl-en><?php echo esc_html($item['subtitle']); ?></p><?php endif; ?>
      <p class="rdc-svc-tech__desc" data-rdc-pnl-copy><?php echo esc_html($item['desc']); ?></p>
      <?php if(!empty($item['features'])): ?>
       <ul class="rdc-svc-tech__fts" role="list" data-rdc-pnl-en>
        <?php foreach($item['features'] as $feature): ?><li><?php echo esc_html($feature); ?></li><?php endforeach; ?>
       </ul>
      <?php endif; ?>
     </div>
     <?php if($image): ?><figure class="rdc-svc-tech__im"><?php echo $image; ?></figure><?php endif; ?>
    </article>
   </div>
  <?php endforeach; ?>
 </div>
</section>
