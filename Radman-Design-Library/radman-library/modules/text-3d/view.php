<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$data=\RadmanLibrary\Designs\Text3D\data();$duration=max(.2,min(3,(float)$data['duration'])); ?>
<section class="rdl-t3" data-rdl-t3 data-rdl-t3-ptr="<?php echo !empty($data['pointer'])?'1':'0'; ?>" style="--t3-dur:<?php echo esc_attr((string)$duration); ?>s">
 <h1 class="rdl-t3__ttl">
 <?php foreach($data['lines'] as $index=>$line):$delay=max(0,min(2,(float)$data['delay']+$index*(float)$data['stagger'])); ?>
  <span class="rdl-t3__row" style="--t3-delay:<?php echo esc_attr((string)$delay); ?>s">
   <span class="rdl-t3__ptr"><span class="rdl-t3__flip"><span class="rdl-t3__fr"><?php echo esc_html($line); ?></span><span class="rdl-t3__bk" aria-hidden="true"><?php echo esc_html($line); ?></span></span></span>
   <span class="rdl-t3__num" aria-hidden="true"><?php echo esc_html(sprintf('%03d',$index+1)); ?></span>
  </span>
 <?php endforeach; ?>
 </h1>
</section>
