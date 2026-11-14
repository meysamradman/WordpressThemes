<?php defined('ABSPATH')||exit; ?>
<section class="rdc-collection" data-rdc-arc data-rdc-pagination-only><div class="rdc-ctr"><?php while(have_posts()):the_post(); ?>
<a class="rdc-collection__back" href="<?php echo esc_url(get_post_type_archive_link('album')); ?>">بازگشت به آلبوم‌ها ←</a><h1><?php the_title(); ?></h1>
<?php if(post_password_required()):echo get_the_password_form();else:
$images=\RadmanLibrary\Collections\images(get_the_ID());$pages=max(1,(int)ceil(count($images)/16));$raw=$_GET['photo_page']??1;$page=is_scalar($raw)?max(1,min($pages,absint($raw))):1;
$base=str_replace('999999999','%#%',add_query_arg('photo_page',999999999,get_permalink($demo?$design:get_the_ID())));
?><div class="rdc-cnt rdc-collection__lead"><?php the_content(); ?></div><div data-rdc-res><div class="rdc-album-images">
<?php foreach(array_slice($images,($page-1)*16,16) as $image): ?><a href="<?php echo esc_url(wp_get_attachment_image_url($image,'full')); ?>" data-rdc-gal data-rdc-album="<?php echo (int)get_the_ID(); ?>"><?php echo wp_get_attachment_image($image,'medium_large',false,['loading'=>'lazy','sizes'=>'(max-width:600px) 45vw,(max-width:900px) 30vw,320px']); ?></a><?php endforeach; ?>
</div><?php if($pages>1): ?><nav class="navigation pagination" aria-label="صفحه‌بندی عکس‌ها"><div class="nav-links"><?php echo paginate_links(['base'=>$base,'format'=>'','current'=>$page,'total'=>$pages,'prev_text'=>'قبلی','next_text'=>'بعدی']); ?></div></nav><?php endif; ?></div>
<?php endif;endwhile; ?><p class="rdc-arc-sts screen-reader-text" role="status" aria-live="polite"></p></div></section>
<?php if(!post_password_required())get_template_part('template-parts/ui/album-lightbox'); ?>
