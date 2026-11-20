<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$projects=$args['projects']??\RadmanLibrary\Designs\PortfolioRows\data();
$projects=array_filter($projects,static fn($project)=>is_post_publicly_viewable($project)&&!post_password_required($project));
if(!$projects)return;
?>
<section class="rdc-ctr rdc-ctr--wide rdc-prj-list" aria-label="نمونه‌کارهای اخیر" data-rdc-prj-list>
 <?php if(($args['heading_tag']??'')==='h1'): ?><h1 class="screen-reader-text">نمونه‌کارهای اخیر</h1><?php endif; ?>
 <ul class="rdc-prj-list__rows">
  <?php foreach($projects as $project):
   $image_id=(int)get_post_thumbnail_id($project);
   $image=\RadmanCT\PageTitle\attachment_allowed($image_id)?wp_get_attachment_image_src($image_id,'medium_large'):false;
   $terms=get_the_terms($project,'project_category');
   $category=is_array($terms)&&$terms?reset($terms)->name:'';
  ?>
   <li>
    <a class="rdc-prj-list__row" href="<?php echo esc_url(get_permalink($project)); ?>"<?php if($image): ?> data-rdc-prj-preview="<?php echo esc_url($image[0]); ?>"<?php endif; ?>>
     <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C,$project)); ?>"><?php echo esc_html(get_the_date('',$project)); ?></time>
     <span class="rdc-prj-list__name">
      <?php if($image)echo wp_get_attachment_image($image_id,'medium',false,['alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'90px']); ?>
      <span><?php echo esc_html(get_the_title($project)); ?></span>
     </span>
     <?php if($category!==''): ?><span class="rdc-prj-list__cat"><?php echo esc_html($category); ?></span><?php endif; ?>
     <span class="rdc-prj-list__ico" aria-hidden="true">
      <span class="rdc-prj-list__act">مشاهدهٔ پروژه</span><?php echo \RadmanCT\icon('arrow-up-right'); ?>
     </span>
    </a>
   </li>
  <?php endforeach; ?>
 </ul>
</section>
