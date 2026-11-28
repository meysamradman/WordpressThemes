<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main-content" class="rds-main">
<?php if (is_home()) : ?><h1><?php echo esc_html(get_the_title((int)get_option('page_for_posts'))); ?></h1>
<?php elseif (is_archive()) : the_archive_title('<h1>','</h1>'); elseif (is_search()) : ?><h1><?php esc_html_e('نتایج جست‌وجو','radmanstore'); ?></h1><?php endif; ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article <?php post_class('rds-entry'); ?>>
<?php if (is_singular()) : ?><h1><?php the_title(); ?></h1><?php the_content(); wp_link_pages(); ?>
<?php else : ?><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); endif; ?>
</article>
<?php endwhile; if (!is_singular()) { the_posts_pagination(); } else { if (comments_open() || get_comments_number()) { comments_template(); } } ?>
<?php else : ?><p><?php esc_html_e('هنوز محتوایی منتشر نشده است.','radmanstore'); ?></p><?php endif; ?>
</main><?php get_footer(); ?>
