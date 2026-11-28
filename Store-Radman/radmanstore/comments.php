<?php defined('ABSPATH') || exit; if(post_password_required()) { return; } ?>
<section class="rds-comments"><h2><?php esc_html_e('دیدگاه‌ها','radmanstore'); ?></h2>
<?php if(have_comments()) : ?><ol><?php wp_list_comments(['style'=>'ol','short_ping'=>true]); ?></ol><?php the_comments_pagination(); endif; comment_form(); ?></section>
