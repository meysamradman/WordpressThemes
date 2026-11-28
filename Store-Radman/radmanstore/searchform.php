<?php defined('ABSPATH') || exit; ?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><label for="rds-blog-search"><?php esc_html_e('عبارت جست‌وجو','radmanstore'); ?></label>
<input id="rds-blog-search" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" required><input type="hidden" name="post_type" value="post">
<button type="submit"><?php esc_html_e('جست‌وجو','radmanstore'); ?></button></form>
