<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$fixture=RadmanLibrary\Samples\fixture();$images=array_column($fixture['projects'],'image');
$images=array_values(array_unique(array_merge($images,array_column(get_option('rdl_reference_media',[]),'id'),array_values(get_option('rdl_wellness_media',[])))));
if(!$images)throw new RuntimeException('No existing images.');
$author=get_user_by('login','admin');$author=$author?$author->ID:1;
$hello=get_page_by_path('hello-world',OBJECT,'post');if($hello&&$hello->post_title==='Hello world!'&&str_contains($hello->post_content,'Welcome to WordPress'))wp_update_post(['ID'=>$hello->ID,'post_status'=>'draft']);
$post_titles=['از ایده تا طراحی یک تجربهٔ روشن','نقش تایپوگرافی در هویت بصری','ساختار محتوای یک وب‌سایت حرفه‌ای','چگونه تجربهٔ کاربری را ساده‌تر کنیم؟','حرکت هدفمند در طراحی وب','رنگ و تصویر در روایت یک برند','اصول یک رابط کاربری خوانا','طراحی واکنش‌گرا برای همهٔ صفحه‌ها','جزئیات کوچک و تأثیرهای بزرگ','از نمونهٔ اولیه تا انتشار وب‌سایت'];
$categories=[];foreach(['ui-design'=>'طراحی رابط کاربری','web-development'=>'توسعهٔ وب'] as $slug=>$name){$term=term_exists($slug,'category')?:wp_insert_term($name,'category',['slug'=>$slug]);$categories[]=(int)$term['term_id'];}
foreach($post_titles as $index=>$title){
    $slug='demo-article-'.($index+1);if(get_page_by_path($slug,OBJECT,'post'))continue;
    $id=wp_insert_post(['post_type'=>'post','post_status'=>'publish','post_author'=>$author,'post_name'=>$slug,'post_title'=>$title,'post_excerpt'=>'یک نوشتهٔ نمایشی برای بررسی چیدمان آرشیو، تصویر، دسته‌بندی و صفحهٔ جزئیات وبلاگ.','post_content'=>'<!-- wp:paragraph --><p>این نوشته برای نمایش طراحی وبلاگ در کتابخانهٔ Radman ساخته شده است. عنوان، تصویر، متن و دسته‌بندی آن از مدیریت بومی وردپرس قابل ویرایش است.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">جزئیات طراحی</h2><!-- /wp:heading --><!-- wp:paragraph --><p>فاصله‌ها، تایپوگرافی و ارتباط تصویر با محتوا در یک صفحهٔ واقعی دیده می‌شوند. این محتوا نمونه است و برای مشتری با محتوای نهایی جایگزین می‌شود.</p><!-- /wp:paragraph -->','post_date'=>wp_date('Y-m-d H:i:s',time()-($index+1)*DAY_IN_SECONDS)],true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    set_post_thumbnail($id,$images[$index%count($images)]);wp_set_post_categories($id,[$categories[$index%count($categories)]]);update_post_meta($id,'_rdl_demo_content',1);
}
$projects=get_posts(['post_type'=>'project','post_status'=>'publish','numberposts'=>-1,'fields'=>'ids']);
$term=term_exists('web-design','project_category')?:wp_insert_term('طراحی وب‌سایت','project_category',['slug'=>'web-design']);
for($index=count($projects)+1;$index<=10;$index++){
    $slug='demo-case-'.$index;if(get_page_by_path($slug,OBJECT,'project'))continue;
    $id=wp_insert_post(['post_type'=>'project','post_status'=>'publish','post_author'=>$author,'post_name'=>$slug,'post_title'=>'پروژهٔ نمایشی '.$index.' · طراحی و توسعه','post_excerpt'=>'نمونهٔ مستقل برای نمایش کارت، تصویر، گالری و اطلاعات پروژه در مرجع طراحی.','post_content'=>'<!-- wp:paragraph --><p>این پروژهٔ نمایشی برای بررسی آرشیو و جزئیات نمونه‌کار آماده شده است. اطلاعات مشتری، شرح پروژه و تصاویر را می‌توانید برای هر سایت جایگزین کنید.</p><!-- /wp:paragraph -->','post_date'=>wp_date('Y-m-d H:i:s',time()-$index*DAY_IN_SECONDS)],true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    set_post_thumbnail($id,$images[$index%count($images)]);wp_set_object_terms($id,[(int)$term['term_id']],'project_category');
    update_post_meta($id,'_webtalik_project',['gallery'=>[$images[$index%count($images)],$images[($index+1)%count($images)]],'client'=>'مشتری نمونه','year'=>wp_date('Y'),'specifications'=>[['label'=>'نوع پروژه','value'=>'نمونهٔ نمایشی']]]);update_post_meta($id,'_rdl_demo_content',1);
}
$term=term_exists('demo-music','music_category')?:wp_insert_term('آثار نمونه','music_category',['slug'=>'demo-music']);
foreach(['آرامش صبح','مسیر تازه','روایت شب'] as $index=>$title){
    $slug='demo-track-'.($index+1);if(get_page_by_path($slug,OBJECT,'track'))continue;
    $id=wp_insert_post(['post_type'=>'track','post_status'=>'publish','post_author'=>$author,'post_name'=>$slug,'post_title'=>$title,'post_excerpt'=>'نمونهٔ نمایشی ساختار آهنگ؛ فایل پخش و دانلود از مدیریت انتخاب می‌شود.','post_content'=>'<!-- wp:paragraph --><p>این رکورد نمونه برای نمایش کاور، نام اثر و اطلاعات هنرمند است. فایل صوتی واقعی هنوز انتخاب نشده است.</p><!-- /wp:paragraph -->'],true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    set_post_thumbnail($id,$images[($index+3)%count($images)]);wp_set_object_terms($id,[(int)$term['term_id']],'music_category');update_post_meta($id,'_rdl_track',['artist'=>'هنرمند نمونه','album'=>'مجموعهٔ نمایشی','released'=>wp_date('Y'),'audio'=>0,'download'=>0,'lyrics'=>'']);update_post_meta($id,'_rdl_demo_content',1);
}
flush_rewrite_rules(true);
echo wp_json_encode(['posts'=>wp_count_posts('post')->publish,'projects'=>wp_count_posts('project')->publish,'tracks'=>wp_count_posts('track')->publish],JSON_PRETTY_PRINT);
