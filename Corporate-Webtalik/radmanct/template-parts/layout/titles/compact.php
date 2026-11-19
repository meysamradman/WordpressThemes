<?php defined('ABSPATH')||exit;$data=\RadmanCT\CompactTitle\data();$artwork=\RadmanCT\CompactTitle\artwork();$patches=\RadmanCT\CompactTitle\motion_patches(); ?>
<header class="rdc-mini rdc-inv">
 <?php if($artwork): ?><img class="rdc-mini__art" src="<?php echo esc_url($artwork[0]); ?>" width="<?php echo (int)$artwork[1]; ?>" height="<?php echo (int)$artwork[2]; ?>" alt=""><?php endif; ?>
 <?php if($patches)get_template_part('template-parts/layout/titles/compact-art',null,['source'=>$artwork[0],'patches'=>$patches[0]]); ?>
 <div class="rdc-mini__copy">
  <?php if($data['label']!==''): ?><p class="rdc-mini__lbl"><?php echo esc_html($data['label']); ?></p><?php endif; ?>
  <h1 class="rdc-mini__ttl"><?php echo esc_html($data['title']); ?><?php if($data['accent']!==''): ?> <span class="rdc-mini__acc"><?php echo esc_html($data['accent']); ?></span><?php endif; ?></h1>
  <?php if($data['desc']!==''): ?><p class="rdc-mini__desc"><?php echo esc_html($data['desc']); ?></p><?php endif; ?>
  <?php if(!empty($data['button']['url'])&&!empty($data['button']['label']))echo \RadmanCT\button($data['button']['label'],$data['button']['url'],['class'=>'rdc-mini__cta','icon_position'=>'start']); ?>
 </div>
</header>
