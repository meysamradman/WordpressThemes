<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-ctr rdc-collection" tabindex="-1"><?php while(have_posts()):the_post(); ?><article>
 <h1><?php the_title(); ?></h1>
 <?php if(post_password_required()){echo get_the_password_form();}else{$item=\RadmanCT\Videos\item(get_post());$file=\RadmanCT\Videos\file($item['video']); ?>
 <a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('clip')); ?>">همهٔ ویدئوها <?php echo \RadmanCT\icon('arrow-right'); ?></a>
 <?php if($file): ?><div class="rdc-clip-player"><video controls playsinline preload="none" width="1920" height="1080"<?php if(\RadmanCT\PageTitle\attachment_allowed($item['image'])): ?> poster="<?php echo esc_url(wp_get_attachment_image_url($item['image'],'large')); ?>"<?php endif; ?> aria-label="<?php the_title_attribute(); ?>"><source src="<?php echo esc_url($file['url']); ?>" type="<?php echo esc_attr($file['type']); ?>"></video></div><?php else: ?><p>فایل ویدئو به‌زودی اضافه می‌شود.</p><?php endif; ?>
 <div class="rdc-cnt rdc-collection__intro"><?php the_content();wp_link_pages(); ?></div>
 <?php } ?></article><?php endwhile; ?></main><?php get_footer(); ?>
