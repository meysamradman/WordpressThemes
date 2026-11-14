<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection" tabindex="-1"><?php while(have_posts()):the_post(); ?><article>
 <h1><?php the_title(); ?></h1>
 <?php if(post_password_required()){echo get_the_password_form();}else{$images=\RadmanCT\Albums\images(get_the_ID()); ?>
 <a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('album')); ?>">همهٔ آلبوم‌ها <?php echo \RadmanCT\icon('arrow-right'); ?></a>
 <?php if(trim(get_the_content())!==''): ?><div class="rdc-cnt rdc-collection__intro"><?php the_content();wp_link_pages(); ?></div><?php endif; ?>
 <div class="rdc-album-images" data-rdc-gallery><?php foreach($images as $index=>$image): ?><a href="<?php echo esc_url(wp_get_attachment_image_url($image,'full')); ?>" data-rdc-gal data-rdc-caption="<?php echo esc_attr(wp_strip_all_tags(wp_get_attachment_caption($image))); ?>" aria-label="<?php echo esc_attr(sprintf('نمایش عکس %s از %s',number_format_i18n($index+1),number_format_i18n(count($images)))); ?>"><?php echo wp_get_attachment_image($image,'medium_large',false,['loading'=>'lazy','sizes'=>'(max-width:600px) 90vw,(max-width:900px) 45vw,400px']); ?></a><?php endforeach; ?></div>
 <?php if(!$images): ?><p>تصاویر این آلبوم به‌زودی اضافه می‌شوند.</p><?php endif;} ?>
 </article><?php endwhile; ?></main><?php get_footer(); ?>
