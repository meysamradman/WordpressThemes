<?php defined('ABSPATH')||exit;$data=\RadmanCT\MusicHome\data(); ?>
<section class="rdc-music-section" aria-labelledby="rdc-latest-music" data-rdc-rail><header class="rdc-music-section__hdr"><h2 id="rdc-latest-music"><span class="rdc-music-section__badge" aria-hidden="true">NEW</span><?php echo esc_html($data['tracks']); ?></h2><div class="rdc-music-section__arrows"><button type="button" data-rdc-rail-prev aria-label="آهنگ‌های قبلی"><?php echo \RadmanCT\icon('arrow-right'); ?></button><button type="button" data-rdc-rail-next aria-label="آهنگ‌های بعدی"><?php echo \RadmanCT\icon('arrow-right'); ?></button></div></header><div class="rdc-music-rail" data-rdc-rail-track>
<?php foreach(\RadmanCT\MusicHome\entries('track',12) as $track)\RadmanCT\Music\card($track,'h3',true); ?>
</div></section>
