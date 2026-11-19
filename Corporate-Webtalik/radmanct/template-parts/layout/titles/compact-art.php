<?php
defined('ABSPATH')||exit;
$id=wp_unique_id('rdc-mini-art-');
$source=$args['source'];
?>
<div class="rdc-mini__motion" aria-hidden="true">
 <div class="rdc-mini__scene" data-rdc-mini-art data-rdc-mini-patches="<?php echo esc_url($args['patches']); ?>">
  <svg class="rdc-mini__tracks" viewBox="0 0 2172 724" focusable="false">
   <defs>
    <radialGradient id="<?php echo esc_attr($id); ?>-fade">
     <stop offset=".63" stop-color="white"/><stop offset="1" stop-color="white" stop-opacity="0"/>
    </radialGradient>
    <mask id="<?php echo esc_attr($id); ?>-ball" maskUnits="userSpaceOnUse" x="480" y="421" width="238" height="224">
     <ellipse cx="599" cy="533" rx="117" ry="111" fill="url(#<?php echo esc_attr($id); ?>-fade)"/>
    </mask>
    <mask id="<?php echo esc_attr($id); ?>-light" maskUnits="userSpaceOnUse" x="712" y="132" width="118" height="108">
     <ellipse cx="771" cy="186" rx="58" ry="53" fill="url(#<?php echo esc_attr($id); ?>-fade)"/>
    </mask>
    <image id="<?php echo esc_attr($id); ?>-patches" width="238" height="332" data-rdc-mini-paint/>
    <clipPath id="<?php echo esc_attr($id); ?>-ridge" clipPathUnits="objectBoundingBox">
     <path d="M0 0H1V1H.138C.126 .95 .117 .867 .104 .814C.08 .77 .044 .762 0 .783Z"/>
    </clipPath>
   </defs>
   <g mask="url(#<?php echo esc_attr($id); ?>-ball)"><use href="#<?php echo esc_attr($id); ?>-patches" x="480" y="421"/></g>
   <g mask="url(#<?php echo esc_attr($id); ?>-light)"><use href="#<?php echo esc_attr($id); ?>-patches" x="712" y="-92"/></g>
   <path d="M-120 718 C-40 670 13 601 122 568 C227 536 331 535 413 542 C481 548 527 554 588 560 C741 578 916 610 1071 623 C1244 639 1353 610 1478 589 C1541 578 1606 574 1650 577 C1857 584 2019 688 2292 711" fill="none" data-orbit-pipe data-orbit-wrap data-orbit-roll="54" data-orbit-duration="24"/>
   <path d="M468 186 C553 123 633 122 701 151 C805 189 881 286 902 413" fill="none" data-orbit-pipe data-orbit-duration="12"/>
  </svg>
  <div class="rdc-mini__roll" style="clip-path:url(#<?php echo esc_attr($id); ?>-ridge)">
   <svg class="rdc-mini__orb" viewBox="532 460 110 108" data-orbit-track="0" data-orbit-phase=".31" focusable="false">
    <defs><clipPath id="<?php echo esc_attr($id); ?>-sphere"><ellipse cx="587" cy="513.5" rx="54.5" ry="52.5"/></clipPath></defs>
    <image href="<?php echo esc_url($source); ?>" width="2172" height="724" clip-path="url(#<?php echo esc_attr($id); ?>-sphere)"/>
   </svg>
  </div>
  <span class="rdc-mini__point" data-orbit-track="1" data-orbit-phase=".54"></span>
 </div>
</div>
<button class="rdc-mini__motion-btn" type="button" data-rdc-mini-toggle hidden aria-pressed="false" aria-label="<?php esc_attr_e('Pause background animation','radmanct'); ?>">
 <span class="rdc-mini__pause"><?php echo \RadmanCT\icon('pause'); ?></span>
 <span class="rdc-mini__play"><?php echo \RadmanCT\icon('play'); ?></span>
</button>
