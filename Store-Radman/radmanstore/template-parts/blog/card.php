<?php defined('ABSPATH') || exit; ?>
<article <?php post_class('rds-blog-card'); ?>>
<?php if (!post_password_required() && has_post_thumbnail()) : ?><a class="rds-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('medium_large',['alt'=>'','loading'=>'lazy','sizes'=>'(max-width:700px) 100vw, (max-width:1000px) 50vw, 33vw']); ?></a><?php endif; ?>
<div class="rds-card-body"><p class="rds-meta"><?php echo wp_kses_post(get_the_category_list('، ')); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<time class="rds-meta" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
<?php the_excerpt(); ?></div></article>
