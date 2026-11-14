<?php defined('ABSPATH')||exit;
$audio=$args['audio']; ?>
<div class="rdc-player" data-rdc-player role="group" aria-label="پخش‌کنندهٔ آهنگ">
 <audio controls preload="none" src="<?php echo esc_url($audio['url']); ?>" aria-label="<?php echo esc_attr('پخش '.get_the_title()); ?>">مرورگر شما پخش صوت را پشتیبانی نمی‌کند.</audio>
 <div class="rdc-player__controls" hidden>
  <div class="rdc-player__transport"><?php foreach(['previous'=>['skip-back','آهنگ قبلی'],'play'=>['play','پخش آهنگ'],'next'=>['skip-forward','آهنگ بعدی']] as $key=>[$icon,$label]):
   if($key==='play'): ?><button class="rdc-player__play" type="button" data-rdc-player-play aria-label="پخش آهنگ" aria-pressed="false"><span data-rdc-player-start><?php echo \RadmanCT\icon('play'); ?></span><span data-rdc-player-pause hidden><?php echo \RadmanCT\icon('pause'); ?></span></button><?php
   else:$item=$args[$key]??null;if($item&&$item->post_status==='publish'&&!$item->post_password): ?><a href="<?php echo esc_url(get_permalink($item)); ?>" aria-label="<?php echo esc_attr($label); ?>"><?php echo \RadmanCT\icon($icon); ?></a><?php else: ?><button type="button" disabled aria-label="<?php echo esc_attr($label); ?>"><?php echo \RadmanCT\icon($icon); ?></button><?php endif;endif;
  endforeach; ?></div>
  <output data-rdc-player-time aria-label="زمان پخش">0:00</output>
  <input class="rdc-player__seek" type="range" min="0" max="1000" value="0" step="1" data-rdc-player-seek aria-label="موقعیت پخش" aria-valuetext="۰ دقیقه" disabled>
  <output data-rdc-player-duration aria-label="مدت آهنگ"><?php echo esc_html($audio['duration']?:'0:00'); ?></output>
  <button type="button" data-rdc-player-mute aria-label="قطع صدا" aria-pressed="false"><?php echo \RadmanCT\icon('volume'); ?></button>
  <input class="rdc-player__volume" type="range" min="0" max="1" value="1" step=".05" data-rdc-player-volume aria-label="بلندی صدا">
 </div>
 <p class="screen-reader-text" data-rdc-player-status role="status" aria-live="polite"></p>
</div>
