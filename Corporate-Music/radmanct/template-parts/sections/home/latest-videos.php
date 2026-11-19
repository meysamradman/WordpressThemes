<?php defined('ABSPATH')||exit;$data=\RadmanCT\Videos\data(6);if(empty($data['items']))return;
$rail=['heading_id'=>'rdc-latest-videos','title'=>$data['title'],'icon'=>'video','url'=>home_url('/videos/'),'previous'=>'ویدئوهای قبلی','next'=>'ویدئوهای بعدی']; ?>
<section id="videos" class="rdc-music-section" aria-labelledby="rdc-latest-videos" data-rdc-rail>
<?php get_template_part('template-parts/sections/home/rail-heading',null,$rail); ?>
<div class="rdc-music-section__stage">
<?php get_template_part('template-parts/ui/video-grid',null,array('data'=>$data,'inline'=>true,'rail'=>true,'actions'=>true)); ?>
<?php get_template_part('template-parts/sections/home/rail-arrows',null,$rail+['side'=>true]); ?>
</div><p class="screen-reader-text" data-rdc-rail-status role="status" aria-live="polite"></p></section>
