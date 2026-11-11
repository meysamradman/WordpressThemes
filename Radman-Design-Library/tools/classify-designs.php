<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
foreach(['hero'=>'هیرو','panels'=>'پنل‌های اسکرولی','showcase'=>'نمایش تعاملی','accordion'=>'آکاردئون','page-title'=>'عنوان صفحه','process'=>'مراحل شماره‌دار'] as $slug=>$name){$term=get_term_by('slug',$slug,'rdl_group');if($term)wp_update_term($term->term_id,'rdl_group',['name'=>$name]);else wp_insert_term($name,'rdl_group',['slug'=>$slug]);}
$slugs=['home-hero','home-services','home-projects','services-cover','services-list','services-strip','services-faq','process-steps','project-marquee','wellness-scroll','text-fill'];
foreach($slugs as $slug){$post=get_page_by_path($slug,OBJECT,'rdl_design');if(!$post)continue;$m=RadmanLibrary\modules()[$slug];wp_update_post(['ID'=>$post->ID,'post_title'=>$m['title'],'post_excerpt'=>$m['motion']]);
 $terms=wp_get_object_terms($post->ID,'rdl_group');$keep=[];foreach($terms as $term)if(!in_array($term->slug,['home','services','hero'],true)||($term->slug==='hero'&&$m['group']==='hero'))$keep[]=$term->slug;
 $keep[]=$m['group'];wp_set_object_terms($post->ID,array_values(array_unique($keep)),'rdl_group');
}
$term=get_term_by('slug','services','rdl_group');if($term&&!$term->count)wp_delete_term($term->term_id,'rdl_group');
echo "Design names and family assignments updated.\n";
