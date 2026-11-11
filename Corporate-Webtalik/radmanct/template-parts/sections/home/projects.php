<?php
 defined('ABSPATH')||exit;$projects=\RadmanCT\Home\selected_work();if(!$projects)return;$direction=is_rtl()?'rtl':'ltr';
?>
<section class="rdc-work" data-rdc-sel-work aria-labelledby="rdc-work-ttl">
 <div class="rdc-work__ly">
  <div class="rdc-work__intr" dir="<?php echo esc_attr($direction); ?>">
   <p class="rdc-work__eb"><?php esc_html_e('Selected work','radmanct'); ?></p>
   <h2 id="rdc-work-ttl" class="rdc-dsp-ttl"><?php esc_html_e('Websites we are proud to have built','radmanct'); ?></h2>
   <p class="rdc-work__desc"><?php esc_html_e('A selection of websites and digital systems, designed and developed around the needs of each business.','radmanct'); ?></p>
   <?php echo \RadmanCT\button(__('All projects','radmanct'),get_post_type_archive_link('project')); ?>
  </div>
  <div class="rdc-work__list" dir="<?php echo esc_attr($direction); ?>">
   <?php foreach($projects as $index=>$project):$id=$index+1; ?>
   <article class="rdc-work__card" aria-labelledby="rdc-work-<?php echo $id; ?>">
    <div class="rdc-work__rvl" data-rdc-work-md><figure class="rdc-work__md"><?php echo wp_get_attachment_image($project['image'],'large',false,array('data-rdc-scr-spd'=>'.8','loading'=>'lazy','decoding'=>'async','sizes'=>'(min-width: 1024px) 50vw, 100vw')); ?></figure></div>
    <div class="rdc-work__meta">
     <div><h3 id="rdc-work-<?php echo $id; ?>" class="rdc-card-tx"><?php echo esc_html($project['title']); ?></h3><p class="rdc-card-tx"><?php echo esc_html($project['description']); ?></p></div>
     <?php if(!empty($project['categories'])): ?><ul class="rdc-work__cats" aria-label="<?php esc_attr_e('Project categories','radmanct'); ?>"><?php foreach(array_slice($project['categories'],0,3)as$category): ?><li><?php echo esc_html($category); ?></li><?php endforeach; ?></ul><?php endif; ?>
    </div>
   </article>
   <?php endforeach; ?>
  </div>
 </div>
</section>
