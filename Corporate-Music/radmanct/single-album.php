<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection" data-rdc-arc data-rdc-pagination-only tabindex="-1"><?php while(have_posts()):the_post(); ?><article>
 <h1><?php the_title(); ?></h1>
 <?php if(post_password_required()){echo get_the_password_form();}else{$gallery=\RadmanCT\Albums\page(get_the_ID());$images=$gallery['images']; ?>
 <a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('album')); ?>">همهٔ آلبوم‌ها <?php echo \RadmanCT\icon('arrow-right'); ?></a>
 <?php if(trim(get_the_content())!==''): ?><div class="rdc-cnt rdc-collection__intro"><?php the_content();wp_link_pages(); ?></div><?php endif; ?>
 <p class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite"></p><div data-rdc-res tabindex="-1" aria-label="عکس‌های آلبوم">
 <div class="rdc-album-images" data-rdc-gallery data-rdc-arc-items><?php foreach($images as $index=>$image): ?><a class="rdc-media-frame" href="<?php echo esc_url(wp_get_attachment_image_url($image,'full')); ?>" data-rdc-gal data-rdc-caption="<?php echo esc_attr(wp_strip_all_tags(wp_get_attachment_caption($image))); ?>" aria-label="<?php echo esc_attr(sprintf('نمایش عکس %s',number_format_i18n($gallery['offset']+$index+1))); ?>"><?php echo \RadmanCT\Albums\thumbnail($image); ?></a><?php endforeach; ?></div>
 <?php get_template_part('template-parts/ui/archive-pagination',null,$gallery+['more'=>'عکس‌های بیشتر']); ?>
 </div>
 <?php if(!$images): ?><p>تصاویر این آلبوم به‌زودی اضافه می‌شوند.</p><?php endif;} ?>
 </article><?php endwhile; ?></main><?php get_footer(); ?>
