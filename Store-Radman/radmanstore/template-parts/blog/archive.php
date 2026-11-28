<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main-content" class="rds-main">
<header class="rds-page-title"><h1><?php if(is_home()){echo esc_html(get_the_title((int)get_option('page_for_posts')));}elseif(is_search()){printf(esc_html__('جست‌وجو: %s','radmanstore'),esc_html(get_search_query()));}else{the_archive_title();} ?></h1><?php the_archive_description(); ?></header>
<div class="rds-blog-layout"><div>
<?php if(have_posts()) : ?><div class="rds-blog-grid" data-rds-items><?php while(have_posts()) : the_post(); get_template_part('template-parts/blog/card'); endwhile; ?></div>
<?php \RadmanStore\Pagination\render(); else : ?><p><?php esc_html_e('هنوز مقاله‌ای برای نمایش وجود ندارد.','radmanstore'); ?></p><?php endif; ?>
</div><?php get_template_part('template-parts/blog/sidebar'); ?></div></main><?php get_footer(); ?>
