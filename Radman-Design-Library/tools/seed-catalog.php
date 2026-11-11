<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready'))throw new RuntimeException('Wrong destination.');
$family=term_exists('catalog','rdl_group')?:wp_insert_term('کاتالوگ محصول','rdl_group',['slug'=>'catalog']);
foreach(['archive'=>'آرشیو محصولات','single'=>'جزئیات محصول'] as $layout=>$label){
 $slug='catalog-'.$layout;$sub='catalog-'.($layout==='archive'?'grid':'detail');if(!term_exists($sub,'rdl_group'))wp_insert_term($label,'rdl_group',['slug'=>$sub,'parent'=>(int)$family['term_id']]);
 $module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
 if(!$post){$id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_title'=>$module['title'],'post_name'=>$slug,'post_excerpt'=>$module['motion']],true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());update_post_meta($id,'_rdl_module',$slug);wp_set_object_terms($id,['catalog',$sub],'rdl_group');wp_set_object_terms($id,'page','rdl_kind');wp_set_object_terms($id,$module['effects'],'rdl_fx');}
}
$images=get_posts(['post_type'=>'attachment','post_status'=>'inherit','post_mime_type'=>'image','posts_per_page'=>8,'fields'=>'ids']);if(!$images)throw new RuntimeException('No local images.');
$cats=[];foreach(['equipment'=>'تجهیزات نمونه','accessories'=>'لوازم جانبی نمونه'] as $slug=>$name){$term=term_exists($slug,'radman_product_cat')?:wp_insert_term($name,'radman_product_cat',['slug'=>$slug]);$cats[]=(int)$term['term_id'];}
foreach(['radman_product_brand'=>['demo-brand','برند نمایشی'],'radman_product_feature'=>['demo-quality','کیفیت نمونه'],'radman_product_tag'=>['demo-product','محصول نمایشی']] as $tax=>[$slug,$name])if(!term_exists($slug,$tax))wp_insert_term($name,$tax,['slug'=>$slug]);
for($n=1;$n<=8;$n++){
 $slug='demo-product-'.$n;if(get_page_by_path($slug,OBJECT,'radman_product'))continue;
 $id=wp_insert_post(['post_type'=>'radman_product','post_status'=>'publish','post_name'=>$slug,'post_title'=>'محصول نمایشی '.$n,'post_excerpt'=>'نمونهٔ کاتالوگ برای بررسی تصویر، مشخصات، دسته‌بندی و جزئیات محصول.','post_content'=>'<!-- wp:paragraph --><p>این محصول نمونه است. عنوان، تصویر، توضیحات، مشخصات و گالری را از مدیریت محصولات می‌توانید جایگزین کنید. این ساختار فروش آنلاین ندارد.</p><!-- /wp:paragraph -->'],true);
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());set_post_thumbnail($id,$images[($n-1)%count($images)]);wp_set_object_terms($id,[$cats[$n%2]],'radman_product_cat');
 foreach(['radman_product_brand'=>'demo-brand','radman_product_feature'=>'demo-quality','radman_product_tag'=>'demo-product'] as $tax=>$term)wp_set_object_terms($id,$term,$tax);
 update_post_meta($id,'_radman_content_data',['version'=>1,'code'=>'RD-'.str_pad((string)$n,3,'0',STR_PAD_LEFT),'gallery'=>array_slice($images,0,4),'specifications'=>[['id'=>'material','label'=>'جنس','value'=>'نمونهٔ قابل ویرایش','unit'=>''],['id'=>'size','label'=>'ابعاد','value'=>'۳۰ × ۲۰','unit'=>'سانتی‌متر']],'pricing'=>['mode'=>'contact','text'=>'']]);update_post_meta($id,'_rdl_demo_content',1);
}
flush_rewrite_rules(false);echo wp_json_encode(['products'=>wp_count_posts('radman_product')->publish,'designs'=>count(RadmanLibrary\modules())-1,'plugins'=>get_option('active_plugins',[])],JSON_UNESCAPED_UNICODE);
