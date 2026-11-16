<?php defined('ABSPATH')||exit;$data=\RadmanCT\CompactTitle\data(); ?>
<header class="rdc-mini rdc-inv">
 <div class="rdc-mini__copy">
  <?php if($data['label']!==''): ?><p class="rdc-mini__lbl"><?php echo esc_html($data['label']); ?></p><?php endif; ?>
  <h1 class="rdc-mini__ttl"><?php echo esc_html($data['title']); ?><?php if($data['accent']!==''): ?> <span class="rdc-mini__acc"><?php echo esc_html($data['accent']); ?></span><?php endif; ?></h1>
  <?php if($data['desc']!==''): ?><p class="rdc-mini__desc"><?php echo esc_html($data['desc']); ?></p><?php endif; ?>
  <?php if(!empty($data['button']['url'])&&!empty($data['button']['label']))echo \RadmanCT\button($data['button']['label'],$data['button']['url'],['class'=>'rdc-mini__cta','icon_position'=>'start']); ?>
 </div>
 <?php if($data['image']): ?>
 <div class="rdc-mini__media" aria-hidden="true"><div class="rdc-mini__art" data-rdc-mini-art>
  <svg class="rdc-mini__paths" viewBox="0 150 1100 820" width="1100" height="820" focusable="false" aria-hidden="true">
   <image href="<?php echo esc_url(wp_get_attachment_url($data['image'])); ?>" width="1920" height="1080"/>
   <path d="M260 254 L300 218 L350 187 L400 166 L450 154 L500 148 L550 150 L600 158 L650 175 L700 202 L750 239 L800 294" data-orbit-pipe data-orbit-duration="36"/>
   <path d="M938 703 L927 740 L914 770 L896 800 L873 830 L840 860 L799 890 L736 920" data-orbit-pipe data-orbit-duration="44"/>
  </svg>
  <span class="rdc-mini__dot" data-orbit-track="0" data-orbit-phase=".3" aria-hidden="true"></span>
  <span class="rdc-mini__dot" data-orbit-track="1" data-orbit-phase=".4" aria-hidden="true"></span>
 </div></div>
 <?php endif; ?>
</header>
