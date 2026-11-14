<?php defined('ABSPATH')||exit; ?>
<div class="rdc-post-meta"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time><span><?php the_author(); ?></span></div>
