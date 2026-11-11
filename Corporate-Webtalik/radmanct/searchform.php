<?php defined('ABSPATH')||exit;$id=wp_unique_id('rdc-srch-'); ?>
<form class="rdc-srch" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" data-rdc-srch>
<label class="screen-reader-text" for="<?php echo esc_attr($id); ?>"><?php esc_html_e('Search articles','radmanct'); ?></label>
<input id="<?php echo esc_attr($id); ?>" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Search articles','radmanct'); ?>" autocomplete="off"><input type="hidden" name="post_type" value="post"><span aria-hidden="true"><?php echo \RadmanCT\icon('search'); ?></span>
</form>
