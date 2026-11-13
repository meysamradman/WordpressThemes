<?php defined('ABSPATH')||exit;$filters=\RadmanLibrary\Library\filters(); ?>
<aside class="rdl-sb" aria-label="جستجو و دسته‌بندی طراحی‌ها">
 <section class="rdl-wdg"><h2>جستجوی طراحی</h2><form class="rdc-srch" role="search" data-rdl-srch method="get" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="rdl-sr" for="rdl-srch">نام یا توضیح طراحی</label><input id="rdl-srch" type="search" name="s" value="<?php echo esc_attr($filters['s']??''); ?>" placeholder="جستجوی طراحی…" autocomplete="off">
  <?php if(isset($filters['rdl_group'])): ?><input type="hidden" name="rdl_group" value="<?php echo esc_attr($filters['rdl_group']); ?>"><?php endif; ?><span aria-hidden="true"><?php echo \RadmanCT\icon('search'); ?></span>
 </form></section>
 <section class="rdl-wdg"><h2>دسته‌بندی</h2>
  <a class="rdl-all" href="<?php echo esc_url(home_url('/')); ?>"<?php if(!$filters)echo ' aria-current="page"'; ?>>همهٔ طرح‌ها</a>
  <?php
  $terms=get_terms(['taxonomy'=>'rdl_group','hide_empty'=>true]);
  // Keep the requested button family visible even when no samples are assigned.
  $buttons=get_term_by('slug','button','rdl_group');
  if(!is_wp_error($terms)&&$buttons&&!in_array((int)$buttons->term_id,array_map('intval',wp_list_pluck($terms,'term_id')),true)){$terms[]=$buttons;$terms=wp_list_sort($terms,'name','ASC');}
  $current=isset($filters['rdl_group'])?get_term_by('slug',$filters['rdl_group'],'rdl_group'):false;
  if(!is_wp_error($terms))\RadmanCT\UI\category_tree($terms,['current'=>$current?$current->term_id:0,'ancestors'=>$current?get_ancestors($current->term_id,'rdl_group'):[],'url'=>static fn($term)=>\RadmanLibrary\Library\url(['rdl_group'=>$term->slug])]);
  ?>
 </section>
</aside>
