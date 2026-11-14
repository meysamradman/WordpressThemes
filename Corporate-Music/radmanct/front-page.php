<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" tabindex="-1">
 <?php while(have_posts()):the_post();if(post_password_required()){echo get_the_password_form();continue;}
 get_template_part('template-parts/sections/home/music-intro');
 get_template_part('template-parts/sections/home/latest-tracks');
 get_template_part('template-parts/sections/home/latest-posts');
 if(trim(get_the_content())): ?><section class="rdc-ctr rdc-main rdc-cnt"><?php the_content();wp_link_pages(); ?></section><?php endif;endwhile; ?>
</main><?php get_footer(); ?>
