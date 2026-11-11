<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-svc-page rdc-inv" tabindex="-1">
<?php get_template_part('template-parts/layout/page-title'); ?>
<?php while(have_posts()):the_post();if(post_password_required()){echo '<div class="rdc-ctr rdc-main">'.get_the_password_form().'</div>';continue;} ?>
<?php get_template_part('template-parts/sections/services/showcase'); ?>
<?php get_template_part('template-parts/sections/services/strip'); ?>
<?php get_template_part('template-parts/ui/faq',null,array('items'=>apply_filters('radmanct/services/faq',array()))); ?>
<?php if(trim(get_the_content())): ?><div class="rdc-ctr rdc-main rdc-cnt"><?php the_content();wp_link_pages(); ?></div><?php endif; ?>
<?php endwhile; ?>
</main><?php get_footer(); ?>
