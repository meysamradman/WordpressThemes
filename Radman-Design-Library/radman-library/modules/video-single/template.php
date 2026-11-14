<?php defined('ABSPATH')||exit; ?>
<section class="rdc-collection rdc-collection--video-detail"><div class="rdc-ctr"><?php while(have_posts()):the_post(); ?>
<a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('clip')); ?>">بازگشت به ویدئوها ←</a>
<?php if(post_password_required()): ?><h1><?php the_title(); ?></h1><?php echo get_the_password_form();else:
$data=\RadmanLibrary\Collections\clip(get_the_ID());$video=\RadmanLibrary\Collections\file(is_scalar($data['video']??0)?absint($data['video']??0):0);$cover=(int)get_post_thumbnail_id();$poster=\RadmanCT\PageTitle\attachment_allowed($cover)?wp_get_attachment_image_url($cover,'large'):'';
?>
<div class="rdc-clip-player"><?php if($video): ?><video controls preload="none" playsinline<?php if($poster): ?> poster="<?php echo esc_url($poster); ?>"<?php endif; ?> aria-label="<?php echo esc_attr(get_the_title()); ?>"><source src="<?php echo esc_url($video['url']); ?>" type="<?php echo esc_attr($video['type']); ?>"></video><?php endif; ?></div>
<div class="rdc-clip-downloads"><?php foreach([1080,720,480] as $quality):$raw=$data['download_'.$quality]??0;$file=\RadmanLibrary\Collections\file(is_scalar($raw)?absint($raw):0);if($file):echo \RadmanCT\button('دانلود '.$quality.'P',$file['url'],['class'=>'rdc-clip-download','icon'=>'download','download'=>true]);else: ?><button class="rdc-btn rdc-clip-download" type="button" disabled>دانلود <?php echo (int)$quality; ?>P</button><?php endif;endforeach; ?></div>
<h1><?php the_title(); ?></h1><div class="rdc-cnt rdc-clip-content"><?php the_content();wp_link_pages(); ?></div>
<?php endif;endwhile; ?></div></section>
