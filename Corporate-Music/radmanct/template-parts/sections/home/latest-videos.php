<?php defined('ABSPATH')||exit;$data=\RadmanCT\Videos\data();if(empty($data['items']))return; ?>
<section id="videos" class="rdc-music-section" aria-labelledby="rdc-latest-videos" data-rdc-rail>
<?php get_template_part('template-parts/sections/home/rail-heading',null,['heading_id'=>'rdc-latest-videos','title'=>$data['title'],'icon'=>'video','url'=>home_url('/videos/'),'previous'=>'ویدئوهای قبلی','next'=>'ویدئوهای بعدی']); ?>
<?php get_template_part('template-parts/ui/video-grid',null,array('data'=>$data,'inline'=>true,'rail'=>true)); ?><p class="screen-reader-text" data-rdc-rail-status role="status" aria-live="polite"></p></section>
