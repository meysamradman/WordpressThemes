<?php defined('ABSPATH')||exit; ?>
<section class="rdc-trk-single rdc-ctr rdc-main"><?php while(have_posts()):the_post(); ?>
 <article>
 <?php if(post_password_required()): ?><h1><?php the_title(); ?></h1><?php echo get_the_password_form(); ?>
 <?php else:
  $data=\RadmanCT\Music\meta(get_the_ID());
  $audio=\RadmanCT\Music\file((int)$data['audio']);
  $download=\RadmanCT\Music\download(get_the_ID(),$data,$audio);
  $previous=$audio?get_adjacent_post(false,'',true,'music_category'):null;
  $next=$audio?get_adjacent_post(false,'',false,'music_category'):null;
 ?>
  <div class="rdc-trk-hero">
   <figure class="rdc-trk-hero__cover"><?php echo \RadmanCT\Content\Media\thumbnail(get_the_ID(),'large',['loading'=>'eager','fetchpriority'=>'high','sizes'=>'(max-width:650px) min(90vw,480px),min(42vw,580px)']); ?></figure>
   <div class="rdc-trk-hero__copy"><h1><?php the_title(); ?></h1>
    <?php \RadmanCT\Music\credits($data,true); ?>
    <?php if($download)echo \RadmanCT\button('دانلود',$download['url'],['class'=>'rdc-trk-download','variant'=>'download','download'=>$download['filename']]); ?>
   </div>
  </div>
  <?php if($audio):get_template_part('template-parts/music/player',null,['audio'=>$audio,'previous'=>$previous,'next'=>$next,'wave'=>true]);else: ?><p class="rdc-trk-note">فایل پخش این اثر هنوز اضافه نشده است.</p><?php endif; ?>
  <?php if(trim(get_the_content())!==''||has_excerpt()): ?><div class="rdc-cnt rdc-trk-intro"><?php if(has_excerpt())the_excerpt();the_content();wp_link_pages(); ?></div><?php endif; ?>
  <?php if($data['lyrics']): ?><section class="rdc-trk-lyrics" aria-labelledby="rdc-lyrics-title"><h2 id="rdc-lyrics-title">متن شعر</h2><p><?php echo nl2br(esc_html($data['lyrics'])); ?></p></section><?php endif; ?>
 <?php endif; ?>
 </article><?php endwhile; ?>
</section>
