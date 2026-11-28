<?php
if ('1'!==getenv('STORE_LOCAL') || PHP_SAPI!=='cli') { exit(1); }
require '/var/www/html/wp-load.php';
if (DB_NAME!=='store_radman_local'||!get_option('store_radman_local_owner')) { throw new RuntimeException('Wrong environment.'); }
if (get_option('store_radman_pages_ready')) { echo "Existing pages and editorial settings preserved.\n"; return; }
foreach (['hello-world','sample-page'] as $slug) {
 $sample=get_page_by_path($slug,OBJECT,['page','post']);
 if($sample && $sample->ID<=2) { wp_trash_post($sample->ID); }
}
$pages=[];
foreach ([
 'about-us'=>['دربارهٔ ما','این برگه برای معرفی فروشگاه، داستان برند و راه‌های شناخت مجموعه آماده شده است. محتوای نهایی با اطلاعات واقعی فروشگاه تکمیل می‌شود.'],
 'contact-us'=>['تماس با ما','برای پرسش دربارهٔ محصولات، پیگیری سفارش و ارتباط با فروشگاه، راه‌های تماس این بخش تکمیل خواهد شد.'],
 'terms-and-conditions'=>['قوانین و مقررات','شرایط ثبت سفارش، پرداخت، ارسال و بازگشت کالا باید پیش از راه‌اندازی فروشگاه در این برگه تکمیل و تأیید شود. این متن، متن حقوقی نهایی فروشگاه نیست.']
] as $slug=>$data) {
 $p=get_page_by_path($slug);
 $id=$p?$p->ID:wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$data[0],'post_name'=>$slug,'post_content'=>'<!-- wp:paragraph --><p>'.esc_html($data[1]).'</p><!-- /wp:paragraph -->'],true);
 if(is_wp_error($id)) {throw new RuntimeException($id->get_error_message());}
 $pages[$slug]=$id;
}
if(!get_option('woocommerce_terms_page_id')) { update_option('woocommerce_terms_page_id',$pages['terms-and-conditions']); }
$locations=get_theme_mod('nav_menu_locations',[]);
$menu=(int)($locations['primary']??0);
if($menu){
 $existing=wp_get_nav_menu_items($menu);$ids=array_map(static fn($item)=>(int)$item->object_id,$existing?:[]);
 foreach($pages as $id){
  if(!in_array($id,$ids,true))wp_update_nav_menu_item($menu,0,['menu-item-object-id'=>$id,'menu-item-object'=>'page','menu-item-type'=>'post_type','menu-item-status'=>'publish']);
 }
}
update_option('store_radman_pages_ready',1,false);
echo "Home, blog, about, contact, terms and native WooCommerce pages ready.\n";
