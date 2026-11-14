<?php defined('ABSPATH')||exit;$data=\RadmanCT\MusicHome\data(); ?>
<section class="rdc-ctr rdc-main"><p><?php echo esc_html($data['label']); ?></p><h1><?php echo esc_html($data['title']); ?></h1><p><?php echo esc_html($data['desc']); ?></p><?php echo \RadmanCT\button('همهٔ آهنگ‌ها',\RadmanCT\Music\url()); ?></section>
