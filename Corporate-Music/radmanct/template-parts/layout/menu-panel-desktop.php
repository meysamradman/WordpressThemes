<?php
 defined('ABSPATH')||exit;$panel=$args['panel'];
 $projects=get_posts(array('post_type'=>'project','post_status'=>'publish','has_password'=>false,'posts_per_page'=>1,'no_found_rows'=>true,'orderby'=>array('menu_order'=>'ASC','date'=>'DESC'),'meta_key'=>'_thumbnail_id','update_post_term_cache'=>false));
 $featured=$projects[0]??null;
?>
<section class="rdc-pnl__desk" aria-labelledby="rdc-pnl-intr-ttl">
 <div class="rdc-pnl__intr">
  <p class="rdc-pnl__lbl" data-rdc-pnl-rvl><?php esc_html_e('A new collaboration','radmanct'); ?></p>
  <h2 id="rdc-pnl-intr-ttl" data-rdc-pnl-rvl><?php esc_html_e('Your next idea deserves a remarkable website.','radmanct'); ?></h2>
  <p class="rdc-pnl__desc" data-rdc-pnl-rvl><?php esc_html_e('Thoughtful design, reliable development, and room for your business to grow.','radmanct'); ?></p>
  <?php if($panel['contact']): ?><?php echo \RadmanCT\button(__('Start a conversation','radmanct'),$panel['contact'],array('class'=>'rdc-pnl__cta','data-rdc-pnl-rvl'=>true)); ?><?php endif; ?>
  <?php get_template_part('template-parts/layout/menu-panel-info',null,array('panel'=>$panel,'compact'=>true)); ?>
 </div>
 <div class="rdc-pnl__work" data-rdc-pnl-rvl>
  <p class="rdc-pnl__lbl"><?php esc_html_e('Selected work','radmanct'); ?></p>
  <?php if($featured): ?><a class="rdc-pnl__prj" href="<?php echo esc_url(get_permalink($featured)); ?>">
   <?php echo get_the_post_thumbnail($featured,'large',array('alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(min-width:1024px) 42vw, 1px')); ?>
   <h3 class="rdc-card-tx"><?php echo esc_html(get_the_title($featured)); ?></h3>
   <span><?php esc_html_e('View details','radmanct'); ?><?php echo \RadmanCT\icon('arrow-up-right'); ?></span>
  </a><?php endif; ?>
  <a class="rdc-pnl__all" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"><?php esc_html_e('All projects','radmanct'); ?><?php echo \RadmanCT\icon('arrow-up-right'); ?></a>
 </div>
</section>
