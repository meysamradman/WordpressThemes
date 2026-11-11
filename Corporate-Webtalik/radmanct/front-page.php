<?php defined('ABSPATH')||exit; get_header(); ?>
<main id="main-content" tabindex="-1"><?php while(have_posts()):the_post();if(post_password_required()){echo get_the_password_form();continue;}get_template_part('template-parts/sections/home/hero');get_template_part('template-parts/sections/home/offerings');get_template_part('template-parts/sections/home/projects');if(trim(get_the_content())){echo '<div class="rdc-ctr rdc-main rdc-cnt">';the_content();echo '</div>';}endwhile; ?></main>
<?php get_footer(); ?>
