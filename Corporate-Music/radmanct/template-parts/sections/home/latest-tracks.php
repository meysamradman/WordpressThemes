<?php defined('ABSPATH')||exit;$data=\RadmanCT\MusicHome\data();
$rail=['heading_id'=>'rdc-latest-music','title'=>$data['tracks'],'badge'=>'NEW','url'=>\RadmanCT\Music\url(),'previous'=>'آهنگ‌های قبلی','next'=>'آهنگ‌های بعدی']; ?>
<section class="rdc-music-section" aria-labelledby="rdc-latest-music" data-rdc-rail>
<?php get_template_part('template-parts/sections/home/rail-heading',null,$rail); ?>
<div class="rdc-music-section__stage">
<div class="rdc-music-rail" data-rdc-rail-track tabindex="0" role="group" aria-label="آهنگ‌ها؛ جابه‌جایی با کلیدهای چپ و راست">
<?php foreach(\RadmanCT\MusicHome\entries('track',8) as $track)\RadmanCT\Music\card($track,'h3',true); ?>
<?php get_template_part('template-parts/sections/home/rail-more',null,['url'=>$rail['url'],'label'=>'مشاهده همهٔ آهنگ‌ها']); ?>
</div>
<?php get_template_part('template-parts/sections/home/rail-arrows',null,$rail+['side'=>true]); ?>
</div><p class="screen-reader-text" data-rdc-rail-status role="status" aria-live="polite"></p></section>
