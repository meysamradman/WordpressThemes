<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$families=['panels'=>'پنل‌های اسکرولی','showcase'=>'نمایش تعاملی','accordion'=>'آکاردئون','page-title'=>'عنوان صفحه','catalog'=>'کاتالوگ محصول','home'=>'صفحهٔ اول','portfolio'=>'نمونه‌کار','blog'=>'وبلاگ','music'=>'موسیقی و صدا','text'=>'انیمیشن متن','sidebar'=>'سایدبار','kits'=>'مجموعهٔ قالب‌ها','carousel'=>'کاروسل و اسلایدر'];
$families['hero']='هیرو';$families['button']='دکمه‌ها';$families['carousel']='کاروسل';$families['table']='جدول‌ها';$families['particles']='ذرات';
foreach($families as $slug=>$name)if(!term_exists($slug,'rdl_group'))wp_insert_term($name,'rdl_group',['slug'=>$slug]);
$hero=get_term_by('slug','hero','rdl_group');
foreach(['hero-static'=>'هیرو بدون اسلایدر','hero-slider'=>'هیرو اسلایدری'] as $slug=>$name)if(!term_exists($slug,'rdl_group'))wp_insert_term($name,'rdl_group',['slug'=>$slug,'parent'=>$hero?(int)$hero->term_id:0]);
foreach(['section'=>'سکشن و جزء','page'=>'صفحهٔ کامل','kit'=>'مجموعهٔ قالب'] as $slug=>$name)if(!term_exists($slug,'rdl_kind'))wp_insert_term($name,'rdl_kind',['slug'=>$slug]);
$effects=['gsap'=>'GSAP Core','scrolltrigger'=>'ScrollTrigger','smooth'=>'ScrollSmoother مشترک','native'=>'CSS / Vanilla بدون موشن GSAP','entrance'=>'ورود متن یا محتوا','text-fill'=>'پُرشدن متن با اسکرول','horizontal'=>'حرکت افقی با اسکرول','pin'=>'پین سکشن','loop'=>'حلقهٔ پیوسته / Marquee','parallax'=>'پارالاکس و زوم','sticky'=>'استیکی','rotator'=>'عبارت چرخشی','menu'=>'منو و پنل','lightbox'=>'نمایش بزرگ تصویر'];
foreach($effects as $slug=>$name)if(!term_exists($slug,'rdl_fx'))wp_insert_term($name,'rdl_fx',['slug'=>$slug]);
foreach(['process'=>['process-timeline'=>'مراحل با زمان‌بندی'],'panels'=>['panels-sticky'=>'ستون ثابت و کارت‌های اسکرولی'],'accordion'=>['accordion-image'=>'آکاردئون تصویری','accordion-card'=>'آکاردئون تصویر و متن','accordion-curtain'=>'آکاردئون ستون‌های بازشونده'],'catalog'=>['catalog-grid'=>'آرشیو محصولات','catalog-detail'=>'جزئیات محصول'],'portfolio'=>['portfolio-grid'=>'آرشیو نمونه‌کار','portfolio-detail'=>'جزئیات نمونه‌کار'],'blog'=>['blog-grid'=>'آرشیو وبلاگ','blog-detail'=>'جزئیات وبلاگ'],'text'=>['text-initial'=>'ورود در شروع صفحه','text-viewport'=>'ورود هنگام اسکرول','text-progress'=>'پُرشدن با اسکرول','text-loop'=>'نوار متن پیوسته'],'gallery'=>['gallery-reveal'=>'بازشدن قاب رسانه','gallery-columns'=>'گالری ستون‌های اسکرولی','marquee'=>'نوار پیوسته / Marquee'],'music'=>['music-grid'=>'آرشیو موسیقی','music-detail'=>'جزئیات آهنگ']] as $parent=>$children){
    $term=get_term_by('slug',$parent,'rdl_group');if(!$term)continue;
    foreach($children as $slug=>$name)if(!term_exists($slug,'rdl_group'))wp_insert_term($name,'rdl_group',['slug'=>$slug,'parent'=>$term->term_id]);
}
$fixture=RadmanLibrary\Samples\fixture();
if(!get_option('rdl_portfolio_seeded')){
    $term=term_exists('web-design','project_category')?:wp_insert_term('طراحی وب‌سایت','project_category',['slug'=>'web-design']);
    foreach($fixture['projects'] as $index=>$item){
        $id=wp_insert_post(['post_type'=>'project','post_status'=>'publish','post_name'=>'case-'.($index+1),'post_title'=>$item['title'],'post_excerpt'=>$item['description'],'post_content'=>'<!-- wp:paragraph --><p>این یک نمونهٔ نمایشی در کتابخانهٔ طراحی است. محتوای پروژه، تصاویر، مشخصات و توضیحات را از مدیریت نمونه‌کارها می‌توانید جایگزین کنید.</p><!-- /wp:paragraph -->','post_date'=>wp_date('Y-m-d H:i:s',time()-($index+1)*DAY_IN_SECONDS)],true);
        if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
        set_post_thumbnail($id,$item['image']);wp_set_object_terms($id,[(int)$term['term_id']],'project_category');
        update_post_meta($id,'_webtalik_project',['gallery'=>array_column($fixture['projects'],'image'),'client'=>'مشتری نمونه','year'=>wp_date('Y'),'specifications'=>[['label'=>'نوع پروژه','value'=>'وب‌سایت سفارشی'],['label'=>'وضعیت','value'=>'نمونهٔ نمایشی کتابخانه']]]);
    }
    update_option('rdl_portfolio_seeded',1,false);
}
foreach(RadmanLibrary\modules() as $slug=>$module){
    if($slug==='starter')continue;
    $post=get_page_by_path($slug,OBJECT,'rdl_design');
    if(!$post){
        $id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_title'=>$module['title'],'post_name'=>$slug,'post_excerpt'=>$module['motion']??''],true);
        if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
        update_post_meta($id,'_rdl_module',$slug);
        $image=$module['image']??'none';$image=$image==='projects'?($fixture['projects'][0]['image']??0):($fixture['map'][$image]??0);if($image)set_post_thumbnail($id,$image);
    }else{$id=$post->ID;}
    $groups=[$module['group']??'sections'];$subgroup=['process-phases'=>'process-timeline','media-stretch'=>'gallery-reveal','sticky-cards'=>'panels-sticky','text-loop'=>'text-loop','text-marquee'=>'text-loop','column-gallery'=>'gallery-columns','image-accordion'=>'accordion-image','card-accordion'=>'accordion-card','curtain-accordion'=>'accordion-curtain','catalog-archive'=>'catalog-grid','catalog-single'=>'catalog-detail','portfolio-archive'=>'portfolio-grid','portfolio-single'=>'portfolio-detail','blog-archive'=>'blog-grid','blog-single'=>'blog-detail','music-archive'=>'music-grid','music-single'=>'music-detail','text-start'=>'text-initial','text-scroll'=>'text-viewport','text-fill'=>'text-progress','services-strip'=>'marquee','project-marquee'=>'marquee'][$slug]??'';if($subgroup)$groups[]=$subgroup;
    if(!empty($module['subgroup']))$groups[]=$module['subgroup'];
    wp_set_object_terms($id,array_values(array_unique($groups)),'rdl_group');
    wp_set_object_terms($id,$module['kind']??'section','rdl_kind');
    wp_set_object_terms($id,$module['effects']??[],'rdl_fx');
}
if(!get_option('rdl_demo_menu')){
    $menu=wp_create_nav_menu('فهرست نمایشی Radman');if(is_wp_error($menu))throw new RuntimeException($menu->get_error_message());
    foreach([['خانه','page-home'],['خدمات','page-services'],['نمونه‌کارها','portfolio-archive'],['کتابخانه','kit-radman']] as [$title,$slug]){
        $id=wp_update_nav_menu_item($menu,0,['menu-item-title'=>$title,'menu-item-url'=>RadmanLibrary\Samples\link($slug),'menu-item-type'=>'custom','menu-item-status'=>'publish']);
        if($slug==='page-services')foreach(['services-list','services-faq','process-steps'] as $child)wp_update_nav_menu_item($menu,0,['menu-item-title'=>RadmanLibrary\modules()[$child]['title'],'menu-item-url'=>RadmanLibrary\Samples\link($child),'menu-item-type'=>'custom','menu-item-parent-id'=>$id,'menu-item-status'=>'publish']);
    }
    set_theme_mod('nav_menu_locations',['primary'=>$menu]);update_option('rdl_demo_menu',$menu,false);
}
$file='/opt/library/fixtures/layout.json';
if(is_file($file)&&!get_option('rdl_layout_media')){
    $layout=json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);$ids=[];$uploads=wp_upload_dir();
    foreach($layout['media'] as $item){
        $existing=get_posts(['post_type'=>'attachment','post_status'=>'inherit','numberposts'=>1,'meta_key'=>'_rdl_webtalik_id','meta_value'=>$item['id'],'fields'=>'ids']);
        if($existing){$ids[$item['id']]=$existing[0];continue;}
        $path=$uploads['basedir'].'/'.$item['file'];if(!is_file($path))throw new RuntimeException('Layout media missing.');
        $id=wp_insert_attachment(['post_title'=>$item['title'],'post_mime_type'=>$item['mime'],'post_status'=>'inherit'],$path,0,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
        update_attached_file($id,$path);if($item['meta'])wp_update_attachment_metadata($id,$item['meta']);update_post_meta($id,'_rdl_webtalik_id',$item['id']);$ids[$item['id']]=$id;
    }
    foreach($layout['map'] as &$id)$id=$ids[$id]??0;unset($id);update_option('rdl_layout_media',$layout['map'],false);
}
flush_rewrite_rules(true);
echo "Library taxonomy, independent portfolio and named compositions ready.\n";
