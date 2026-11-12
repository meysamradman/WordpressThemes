<?php defined('ABSPATH')||exit;$tracks=array_slice($args['tracks']??array(),0,4); ?>
<svg class="rdc-pt__orbs" viewBox="0 0 1920 1080" aria-hidden="true" focusable="false">
 <?php foreach($tracks as $track): ?>
 <path d="<?php echo esc_attr($track[0]); ?>" data-orbit-pipe data-orbit-duration="<?php echo (int)$track[1]; ?>" fill="none" stroke="none"/>
 <?php endforeach; ?>
</svg>
<?php foreach($tracks as $index=>$track): ?>
<span class="rdc-pt__dot" data-orbit-track="<?php echo (int)$index; ?>" data-orbit-phase="<?php echo esc_attr($track[2]); ?>" aria-hidden="true"></span>
<?php endforeach; ?>
