<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection rdc-collection--video-detail" data-rdc-video-page tabindex="-1"><?php while(have_posts()):the_post(); ?><article>
 <?php if(post_password_required()): ?><h1><?php the_title(); ?></h1><?php echo get_the_password_form();else:$item=\RadmanCT\Videos\item(get_post());$file=\RadmanCT\Videos\file($item['video']);$downloads=\RadmanCT\Videos\downloads(get_the_ID()); ?>
 <?php if($item['aparat']): ?><div class="rdc-clip-player"><?php get_template_part('template-parts/ui/aparat',null,$item); ?></div><?php elseif($file): ?><div class="rdc-clip-player"><video controls playsinline preload="none" width="1920" height="1080"<?php if(\RadmanCT\PageTitle\attachment_allowed($item['image'])): ?> poster="<?php echo esc_url(wp_get_attachment_image_url($item['image'],'large')); ?>"<?php endif; ?> aria-label="<?php the_title_attribute(); ?>"><source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['type']); ?>"></video></div><?php else: ?><p>فایل ویدئو به‌زودی اضافه می‌شود.</p><?php endif; ?>
 <nav class="rdc-clip-downloads" aria-label="دانلود ویدئو با کیفیت دلخواه">
 <?php if($item['aparat']): ?>
  <?php echo \RadmanCT\Videos\aparat_download_button($item['aparat'],'rdc-clip-download'); ?>
 <?php else: foreach([480,720,1080] as $quality){$download=$downloads[$quality]??[];echo \RadmanCT\button('دانلود '.$quality.'P',$download['url']??'#',['class'=>'rdc-clip-download','variant'=>'download','download'=>true,'disabled'=>!$download]);} endif; ?>
 </nav>
 <h1><?php the_title(); ?></h1>
 <div class="rdc-cnt rdc-collection__intro rdc-clip-content rdc-reading"><?php the_content();wp_link_pages(); ?></div>
 <nav class="rdc-collection-terms" aria-label="دسته‌بندی و برچسب‌ها"><?php the_terms(get_the_ID(),'clip_category','<div class="rdc-tags">',' ','</div>');the_terms(get_the_ID(),'clip_tag','<div class="rdc-tags">',' ','</div>'); ?></nav>
 <a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('clip')); ?>">همهٔ ویدئوها <?php echo \RadmanCT\icon('arrow-right'); ?></a>
 <?php endif; ?></article><?php endwhile; ?></main><?php get_footer(); ?>
