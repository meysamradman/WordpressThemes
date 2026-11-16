<?php defined('ABSPATH')||exit;$data=\RadmanCT\CompactTitle\data(); ?>
<header class="rdc-mini rdc-inv">
 <div class="rdc-mini__copy">
  <?php if($data['label']!==''): ?><p class="rdc-mini__lbl"><?php echo esc_html($data['label']); ?></p><?php endif; ?>
  <h1 class="rdc-mini__ttl"><?php echo esc_html($data['title']); ?></h1>
  <?php if($data['desc']!==''): ?><p class="rdc-mini__desc"><?php echo esc_html($data['desc']); ?></p><?php endif; ?>
 </div>
 <?php if($data['image']): ?>
 <div class="rdc-mini__art" data-rdc-mini-art aria-hidden="true">
  <svg class="rdc-mini__paths" viewBox="0 0 1000 625" focusable="false" aria-hidden="true">
   <path d="M155 318 C155 168 845 168 845 318 C845 468 155 468 155 318Z" data-orbit-pipe data-orbit-duration="36"/>
   <path d="M290 485 C125 370 690 65 775 175 C940 335 420 575 290 485Z" data-orbit-pipe data-orbit-duration="44"/>
  </svg>
  <?php echo wp_get_attachment_image($data['image'],'full',false,['class'=>'rdc-mini__mark','alt'=>'','loading'=>'eager','decoding'=>'async','sizes'=>'(max-width:640px) 160px, 360px']); ?>
  <span class="rdc-mini__dot" data-orbit-track="0" data-orbit-phase=".1" aria-hidden="true"></span>
  <span class="rdc-mini__dot" data-orbit-track="1" data-orbit-phase=".65" aria-hidden="true"></span>
 </div>
 <?php endif; ?>
</header>
