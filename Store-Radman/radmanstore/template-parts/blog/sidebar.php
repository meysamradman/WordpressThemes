<?php defined('ABSPATH') || exit; ?>
<aside class="rds-sidebar" aria-label="<?php esc_attr_e('نوار کناری وبلاگ','radmanstore'); ?>">
<section><h2><?php esc_html_e('جست‌وجوی مقاله','radmanstore'); ?></h2><?php get_search_form(); ?></section>
<section><h2><?php esc_html_e('دسته‌بندی‌ها','radmanstore'); ?></h2><ul><?php wp_list_categories(['title_li'=>'','show_count'=>true,'hide_empty'=>true]); ?></ul></section>
<section><h2><?php esc_html_e('آخرین مقاله‌ها','radmanstore'); ?></h2><ul>
<?php foreach(get_posts(['posts_per_page'=>5,'post_status'=>'publish','has_password'=>false,'no_found_rows'=>true]) as $entry) : ?><li><a href="<?php echo esc_url(get_permalink($entry)); ?>"><?php echo esc_html(get_the_title($entry)); ?></a></li><?php endforeach; ?></ul></section></aside>
