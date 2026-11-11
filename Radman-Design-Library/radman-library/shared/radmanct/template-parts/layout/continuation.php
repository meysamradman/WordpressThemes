<?php
defined('ABSPATH')||exit;
$more=\RadmanCT\Content\Continuation\data();
if(!$more)return;
$has_related=$more['related']&&$more['related']->have_posts();
if(!$more['previous']&&!$more['next']&&!$has_related)return;
?>
<div class="rdc-sgl-more rdc-ctr">
 <?php if($more['previous']||$more['next']): ?>
 <nav class="rdc-sgl-more__nav" aria-label="<?php esc_attr_e('Previous and next','radmanct'); ?>">
  <?php foreach(array('previous','next') as $direction): $item=$more[$direction];if(!$item instanceof WP_Post)continue;$previous=$direction==='previous'; ?>
  <a class="rdc-sgl-more__lnk<?php if(!$previous)echo ' rdc-sgl-more__lnk--next'; ?>" rel="<?php echo $previous?'prev':'next'; ?>" href="<?php echo esc_url(get_permalink($item)); ?>">
   <span class="rdc-sgl-more__lbl">
    <?php if($previous): ?><span class="rdc-sgl-more__ico"><?php echo \RadmanCT\icon('arrow-right'); ?></span><?php endif; ?>
    <?php echo esc_html($previous?__('Previous','radmanct'):__('Next','radmanct')); ?>
    <?php if(!$previous)echo \RadmanCT\icon('arrow-right'); ?>
   </span>
   <span class="rdc-sgl-more__ttl"><?php echo esc_html(get_the_title($item)?:__('(no title)','radmanct')); ?></span>
  </a>
  <?php endforeach; ?>
 </nav>
 <?php endif; ?>
 <?php if($has_related): ?>
 <section class="rdc-sgl-more__rel" aria-labelledby="rdc-sgl-more-title">
  <h2 id="rdc-sgl-more-title"><?php echo esc_html($more['matched']?($more['project']?__('Related projects','radmanct'):__('Related articles','radmanct')):($more['project']?__('More projects','radmanct'):__('More articles','radmanct'))); ?></h2>
  <div class="rdc-sgl-more__grid">
   <?php try{while($more['related']->have_posts()):$more['related']->the_post();
    get_template_part($more['project']?'shared/radmanct/template-parts/cards/project':'shared/radmanct/template-parts/blog/preview',null,array('variant'=>'tile'));
   endwhile;}finally{wp_reset_postdata();} ?>
  </div>
 </section>
 <?php endif; ?>
</div>
