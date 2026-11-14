<?php defined('ABSPATH')||exit;get_header(); ?>
<main id="main-content" tabindex="-1">
 <?php while(have_posts()):the_post();if(post_password_required()){echo get_the_password_form();continue;} ?>
 <section class="rdc-ctr rdc-main"><p>دنیای صدا و روایت</p><h1><?php echo esc_html(get_bloginfo('name')); ?></h1><p>تازه‌ترین آهنگ‌ها، معرفی هنرمندان و خواندنی‌های دنیای موسیقی.</p><?php echo \RadmanCT\button('همهٔ آهنگ‌ها',\RadmanCT\Music\url()); ?></section>
 <section class="rdc-ctr rdc-main" aria-labelledby="rdc-music-latest"><h2 id="rdc-music-latest">تازه‌ترین آهنگ‌ها</h2><div class="rdc-trk-grid"><?php foreach(get_posts(array('post_type'=>'track','post_status'=>'publish','has_password'=>false,'posts_per_page'=>6,'no_found_rows'=>true)) as $track)\RadmanCT\Music\card($track,'h3'); ?></div></section>
 <section class="rdc-ctr rdc-main" aria-labelledby="rdc-blog-latest"><h2 id="rdc-blog-latest">تازه‌های وبلاگ</h2><div class="rdc-trk-grid"><?php foreach(get_posts(array('post_type'=>'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>3,'no_found_rows'=>true)) as $item): ?><article class="rdc-trk-card"><a href="<?php echo esc_url(get_permalink($item)); ?>"><?php echo get_the_post_thumbnail($item,'medium_large',array('loading'=>'lazy')); ?><div><h3><?php echo esc_html($item->post_title); ?></h3><p><?php echo esc_html(wp_trim_words($item->post_excerpt?:wp_strip_all_tags($item->post_content),22)); ?></p></div></a></article><?php endforeach; ?></div></section>
 <?php endwhile; ?>
</main><?php get_footer(); ?>
