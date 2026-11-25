<?php defined('ABSPATH')||exit;
if(empty($args['url']))return;
?>
<a class="rdc-music-rail__more" href="<?php echo esc_url($args['url']); ?>" aria-label="<?php echo esc_attr($args['label']); ?>">
 <span class="rdc-music-rail__more-body">
  <?php echo \RadmanCT\icon('arrow-right'); ?>
  <span>مشاهده همه</span>
 </span>
</a>
