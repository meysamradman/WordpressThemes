<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection" data-rdc-arc tabindex="-1">
 <h1>ویدئوها</h1>
 <p class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></p>
 <div data-rdc-res tabindex="-1" aria-label="فهرست ویدئوها">
 <?php $items=[];while(have_posts()){the_post();$items[]=\RadmanCT\Videos\item(get_post());}if($items)get_template_part('template-parts/ui/video-grid',null,['heading_tag'=>'h2','data'=>['items'=>$items]]);else echo '<p>هنوز ویدئویی منتشر نشده است.</p>';get_template_part('template-parts/ui/archive-pagination'); ?>
 </div>
</main><?php get_footer(); ?>
