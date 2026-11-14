<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" class="rdc-music-home rdc-ctr" tabindex="-1" data-rdc-music-home>
 <?php while(have_posts()):the_post();if(post_password_required()){echo get_the_password_form();continue;}
 get_template_part('template-parts/sections/home/music-intro');
 get_template_part('template-parts/sections/home/latest-tracks'); ?>
 <audio class="rdc-music-player" data-rdc-music-player controls preload="none" hidden aria-label="پخش آهنگ انتخاب‌شده"></audio>
 <?php get_template_part('template-parts/sections/home/latest-videos');
 if(trim(get_the_content())): ?><section class="rdc-cnt"><?php the_content();wp_link_pages(); ?></section><?php endif;endwhile; ?>
</main><?php get_footer(); ?>
