<?php defined('ABSPATH')||exit; ?>
<section class="rdc-trk-arc rdc-ctr rdc-main"><h1><?php if(is_tax('music_category'))single_term_title();else echo 'موسیقی'; ?></h1>
 <nav class="rdc-trk-cats" aria-label="دسته‌های موسیقی"><a href="<?php echo esc_url(\RadmanCT\Music\url()); ?>">همهٔ آهنگ‌ها</a><?php foreach(get_terms(['taxonomy'=>'music_category','hide_empty'=>true]) as $term): ?><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a><?php endforeach; ?></nav>
 <div class="rdc-trk-grid"><?php if(have_posts()):while(have_posts()):the_post();\RadmanCT\Music\card(get_post());endwhile;else: ?><p>هنوز آهنگی اضافه نشده است.</p><?php endif; ?></div><?php the_posts_pagination(['prev_text'=>'قبلی','next_text'=>'بعدی']); ?>
</section>
