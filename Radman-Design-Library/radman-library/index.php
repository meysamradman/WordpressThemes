<?php defined('ABSPATH')||exit; get_header(); ?>
<main id="rdl-main" class="rdl-wrap" data-rdl-lib><h1 class="rdl-sr">کتابخانهٔ طراحی Radman</h1>

<p class="rdl-sts" role="status" aria-live="polite" data-rdl-sts></p><div class="rdl-load" aria-hidden="true"></div><div class="rdl-lib"><div class="rdl-lib__res" data-rdl-res>
<?php if(is_search()): ?><p>نتایج «<?php echo esc_html(get_search_query()); ?>»</p><?php elseif(is_tax()): ?><p><?php single_term_title(); ?></p><?php endif; ?>
<div class="rdl-grid"><?php if(have_posts()): while(have_posts()): the_post(); ?><article class="rdl-card"><a class="rdl-card__lnk" href="<?php the_permalink(); ?>"><div class="rdl-card__im" aria-hidden="true"><span class="rdl-card__sym"><?php $design=\RadmanLibrary\module(get_the_ID());echo \RadmanCT\icon($design['icon']??'layout'); ?></span></div><div class="rdl-card__cnt"><p class="rdl-lbl"><?php echo esc_html(implode(' · ',wp_get_post_terms(get_the_ID(),'rdl_group',['fields'=>'names']))); ?></p><h2><?php the_title(); ?></h2><?php if(!post_password_required()): ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?><span class="rdl-card__cta">مشاهدهٔ طراحی <span aria-hidden="true">↖</span></span></div></a></article><?php endwhile; else: ?><div class="rdl-emp"><h2>این بخش آمادهٔ اولین طراحی است.</h2><p>نمونه‌های تازه را به همین دسته اضافه می‌کنیم.</p></div><?php endif; ?></div>
<?php the_posts_pagination(['prev_text'=>'قبلی','next_text'=>'بعدی']); ?></div><?php get_template_part('template-parts/library-sidebar'); ?></div></main><?php get_footer(); ?>

