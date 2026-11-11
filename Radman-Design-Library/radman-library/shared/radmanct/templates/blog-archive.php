<?php defined('ABSPATH')||exit; ?>
<?php  ?>
<section class="rdc-ctr rdc-main rdc-blog-arc" data-rdc-arc="blog" data-rdc-blog-url="<?php echo esc_url(\RadmanLibrary\Blog\url()); ?>"><div class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite"></div>
<?php get_template_part('shared/radmanct/template-parts/ui/content-heading'); ?><div class="rdc-cnt-ly"><div data-rdc-res tabindex="-1"><?php get_template_part('shared/radmanct/template-parts/blog/results'); ?></div><?php get_template_part('shared/radmanct/template-parts/blog/sidebar'); ?></div></section><?php  ?>
