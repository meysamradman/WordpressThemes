<?php
defined('ABSPATH')||exit;
$data=apply_filters('radmanct/service/intro',[]);
if(empty($data['title'])||empty($data['cards']))return;
$projects=array_slice($args['projects']??[],0,5);
?>
<section class="rdc-ctr rdc-svc-intro" id="rdc-svc-intro" aria-labelledby="rdc-svc-intro-ttl" data-rdc-svc-intro>
 <header class="rdc-heading rdc-svc-intro__hdg">
  <p class="rdc-heading__lbl"><?php echo \RadmanCT\icon('arrow-up-right'); ?><?php echo esc_html($data['label']); ?></p>
  <h2 class="rdc-heading__ttl" id="rdc-svc-intro-ttl"><?php echo esc_html($data['title']); ?></h2>
  <?php echo \RadmanCT\button('گفت‌وگو دربارهٔ پروژه', $data['link'],['class'=>'rdc-svc-intro__cta']); ?>
 </header>
 <div class="rdc-svc-intro__grid">
  <div class="rdc-svc-intro__lead">
   <p><?php echo esc_html($data['lead']); ?></p>
   <?php if($projects): ?>
    <a class="rdc-svc-intro__work" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>">
     <span class="rdc-svc-intro__previews" aria-hidden="true">
      <?php foreach($projects as $project): $id=(int)get_post_thumbnail_id($project);
       if(\RadmanCT\PageTitle\attachment_allowed($id))echo wp_get_attachment_image($id,'thumbnail',false,['alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'52px']);
      endforeach; ?>
     </span>
     <span>دیدن نمونه‌کارها</span>
    </a>
   <?php endif; ?>
   <div class="rdc-svc-intro__art" aria-hidden="true" data-rdc-intro-art>
    <span></span><span></span><span></span>
   </div>
  </div>
  <?php foreach($data['cards'] as $card):
   $id=(int)$card['image'];
   $image=\RadmanCT\PageTitle\attachment_allowed($id)?wp_get_attachment_image($id,'full',false,[
    'alt'=>$card['alt'],'loading'=>'lazy','decoding'=>'async','sizes'=>'(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw','data-rdc-scr-spd'=>'.8',
   ]):'';
  ?>
   <article class="rdc-svc-intro__card">
    <?php if($image): ?><div class="rdc-svc-intro__frame" data-rdc-intro-media><div class="rdc-svc-intro__zoom"><?php echo $image; ?></div></div><?php endif; ?>
    <div class="rdc-svc-intro__copy" data-rdc-intro-copy>
     <h3 data-rdc-en><?php echo esc_html($card['title']); ?></h3>
     <p data-rdc-en><?php echo esc_html($card['desc']); ?></p>
    </div>
   </article>
  <?php endforeach; ?>
 </div>
</section>
