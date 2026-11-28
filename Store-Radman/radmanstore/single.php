<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main-content" class="rds-main"><?php while(have_posts()) : the_post(); ?>
<div class="rds-blog-layout"><article <?php post_class('rds-reading'); ?>><header><h1><?php the_title(); ?></h1></header>
<?php if(post_password_required()) { echo get_the_password_form(); } else { ?>
<p class="rds-meta"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time> · <?php echo wp_kses_post(get_the_category_list('، ')); ?></p>
<?php if(has_excerpt()) : ?><div class="rds-intro"><?php the_excerpt(); ?></div><?php endif; ?>
<?php if(has_post_thumbnail()) : ?><figure class="rds-media"><?php the_post_thumbnail('large',['loading'=>'eager','fetchpriority'=>'high','sizes'=>'(max-width:1000px) 100vw, 900px']); ?></figure><?php endif; ?>
<?php the_content(); wp_link_pages(); the_tags('<p>','، ','</p>'); ?>
<nav class="rds-post-links" aria-label="<?php esc_attr_e('مقاله‌های دیگر','radmanstore'); ?>"><?php previous_post_link('%link','مقالهٔ قبلی: %title'); next_post_link('%link','مقالهٔ بعدی: %title'); ?></nav>
<?php if(comments_open()||get_comments_number()) { comments_template(); } } ?>
</article><?php get_template_part('template-parts/blog/sidebar'); ?></div><?php endwhile; ?></main><?php get_footer(); ?>
