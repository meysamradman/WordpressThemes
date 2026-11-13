<?php
defined('ABSPATH')||exit;
$device=\RadmanLibrary\Preview\devices()[\RadmanLibrary\Preview\mode()];
?>
<main id="main-content" class="rdl-pv__workspace" tabindex="-1" data-rdl-pv-work>
 <h1 class="screen-reader-text"><?php echo esc_html('پیش‌نمایش '.$device['label'].' · '.$args['title']); ?></h1>
 <div class="rdl-pv__port" data-rdl-pv-port style="--rdl-pv-w:<?php echo (int)$device['width']; ?>px;--rdl-pv-h:<?php echo (int)$device['height']; ?>px">
  <div class="rdl-pv__device" data-rdl-pv-shell="<?php echo esc_attr(\RadmanLibrary\Preview\mode()); ?>">
   <iframe class="rdl-pv__frame" data-rdl-pv-frame src="<?php echo esc_url(\RadmanLibrary\Preview\url('desktop',true)); ?>" width="<?php echo (int)$device['width']; ?>" height="<?php echo (int)$device['height']; ?>" title="<?php echo esc_attr('پیش‌نمایش '.$device['label'].' · '.$args['title']); ?>" loading="eager"></iframe>
  </div>
 </div>
</main>
