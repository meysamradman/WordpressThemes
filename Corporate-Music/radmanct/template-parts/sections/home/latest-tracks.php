<?php defined('ABSPATH')||exit;$data=\RadmanCT\MusicHome\data(); ?>
<section class="rdc-ctr rdc-main" aria-labelledby="rdc-music-latest"><h2 id="rdc-music-latest"><?php echo esc_html($data['tracks']); ?></h2><div class="rdc-trk-grid"><?php foreach(\RadmanCT\MusicHome\entries('track',6) as $track)\RadmanCT\Music\card($track,'h3'); ?></div></section>
