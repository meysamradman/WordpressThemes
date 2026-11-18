<?php defined('ABSPATH')||exit;
$rtl=is_rtl();
$side=!empty($args['side']);
?>
<div class="rdc-music-section__arrows<?php if($side)echo ' rdc-music-section__arrows--side'; ?>" data-rdc-rail-nav="<?php echo $side?'side':'header'; ?>" hidden>
 <button type="button" data-rdc-rail-prev aria-label="<?php echo esc_attr($args['previous']); ?>"><?php echo \RadmanCT\icon($rtl?'chevron-right':'chevron-left'); ?></button>
 <button type="button" data-rdc-rail-next aria-label="<?php echo esc_attr($args['next']); ?>"><?php echo \RadmanCT\icon($rtl?'chevron-left':'chevron-right'); ?></button>
</div>
