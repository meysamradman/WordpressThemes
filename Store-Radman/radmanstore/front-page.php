<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main-content" class="rds-main"><h1><?php echo esc_html(get_bloginfo('name')); ?></h1>
<p><?php esc_html_e('فروشگاه در حال آماده‌سازی است.','radmanstore'); ?></p>
<?php if (function_exists('wc_get_page_permalink')) : ?><p><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('ورود به فروشگاه','radmanstore'); ?></a></p><?php endif; ?>
</main><?php get_footer(); ?>
