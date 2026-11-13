<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$data=\RadmanLibrary\Designs\ParticlesFloat\data();$number=max(0,min(24,(int)$data['number']));$heading=wp_unique_id('rdl-pfl-ttl-'); ?>
<section class="rdl-pfl" data-rdl-pfl aria-labelledby="<?php echo esc_attr($heading); ?>">
 <div class="rdl-pfl__dots" aria-hidden="true">
 <?php for($index=0;$index<$number;$index++):$x=8+($index*37)%84;$y=10+($index*29)%78;$size=4+($index*3)%5;$duration=24+($index*7)%19;$delay=-($index*11)%37;$dy=($index%2?1:-1)*(30+($index*13)%70); ?>
  <span class="rdl-pfl__dot" style="--rdl-pfl-x:<?php echo (int)$x; ?>%;--rdl-pfl-y:<?php echo (int)$y; ?>%;--rdl-pfl-size:<?php echo (int)$size; ?>px;--rdl-pfl-dur:<?php echo (int)$duration; ?>s;--rdl-pfl-delay:<?php echo (int)$delay; ?>s;--rdl-pfl-dy:<?php echo (int)$dy; ?>px"></span>
 <?php endfor; ?>
 </div>
 <div class="rdl-pfl__copy"><p class="rdl-pfl__lbl"><?php echo esc_html($data['label']); ?></p><h1 id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></h1><p class="rdl-pfl__desc"><?php echo esc_html($data['description']); ?></p></div>
 <button class="rdl-pfl__pause" type="button" data-rdl-pfl-pause aria-pressed="false" hidden>توقف حرکت</button>
</section>
