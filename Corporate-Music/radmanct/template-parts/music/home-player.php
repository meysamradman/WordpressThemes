<?php defined('ABSPATH')||exit; ?>
<div class="rdc-home-player" data-rdc-home-player role="region" aria-label="پخش‌کنندهٔ آهنگ صفحهٔ اول" hidden>
 <div class="rdc-home-player__meta">
  <img data-rdc-home-cover width="48" height="48" alt="" decoding="async" hidden>
  <div><p class="rdc-home-player__ttl" data-rdc-home-track aria-live="polite"></p><p class="rdc-home-player__artist" data-rdc-home-artist></p><p class="rdc-home-player__lbl">آمادهٔ پخش</p></div>
 </div>
 <?php get_template_part('template-parts/music/player',null,['compact'=>true,'dock'=>true]); ?>
 <div class="rdc-home-player__actions">
  <button class="rdc-home-player__toggle" type="button" data-rdc-home-player-toggle aria-expanded="true" aria-label="جمع‌کردن پخش‌کننده؛ آهنگ ادامه پیدا می‌کند"><?php echo \RadmanCT\icon('chevron-down'); ?><span>جمع‌کردن</span></button>
  <button class="rdc-home-player__close" type="button" data-rdc-home-player-close aria-label="بستن پخش‌کننده و توقف آهنگ"><?php echo \RadmanCT\icon('x'); ?></button>
 </div>
</div>
