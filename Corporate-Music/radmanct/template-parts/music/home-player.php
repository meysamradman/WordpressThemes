<?php defined('ABSPATH')||exit; ?>
<div class="rdc-home-player" data-rdc-home-player hidden>
 <div class="rdc-home-player__hdr"><div><p class="rdc-home-player__lbl">در حال پخش</p><p class="rdc-home-player__ttl" data-rdc-home-track></p></div><button type="button" data-rdc-home-player-close aria-label="بستن پخش‌کننده و توقف آهنگ"><?php echo \RadmanCT\icon('x'); ?></button></div>
 <?php get_template_part('template-parts/music/player',null,['compact'=>true]); ?>
</div>
