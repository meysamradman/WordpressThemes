<?php
/** Register this one design and its local reference assets; never run on a client. */
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready')||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination.');
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/media.php';
require_once ABSPATH.'wp-admin/includes/image.php';
$base='https://creative-agency.elementra.themerex.net/wp-content/uploads/2024/12/';
$sources=array(
 'portrait'=>array('custom-img-19-copyright.jpg','دو رهگذر با پوشش تیره، کنار دیوار روشن و زیر نور آفتاب'),
 'detail-one'=>array('custom-img-20-copyright.jpg','جزئیات تصویر مرجع هیرو، شمارهٔ یک'),
 'detail-two'=>array('custom-img-21-copyright.jpg','جزئیات تصویر مرجع هیرو، شمارهٔ دو'),
 'background'=>array('background-01.jpg','زمینهٔ روشن و آبیِ هیروی مرجع'),
);
$media=get_option('rdl_hero_typography_media',array());
foreach($sources as $key=>[$filename,$alt]){
 if(!empty($media[$key])&&wp_attachment_is_image((int)$media[$key]))continue;
 $existing=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_rdl_reference_url','meta_value'=>$base.$filename));
 if($existing){$media[$key]=(int)$existing[0];continue;}
 $path='/tmp/hero-typography/'.$filename;
 if(!is_file($path))throw new RuntimeException('Copy the inspected reference image into '.$path.' first.');
 $id=media_handle_sideload(array('name'=>'reference-hero-'.$filename,'tmp_name'=>$path),0,'Radman hero typography reference: '.$key,array('post_content'=>'Design reference: '.$base.$filename.'. Original source filename and copyright notice retained. Local workshop media; not customer data.'));
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 update_post_meta($id,'_rdl_reference_url',$base.$filename);update_post_meta($id,'_wp_attachment_image_alt',$alt);$media[$key]=$id;
}
update_option('rdl_hero_typography_media',$media,false);
function rdl_hero_typography_term(string $slug,string $name,int $parent=0):int {
 $term=get_term_by('slug',$slug,'rdl_group');
 $result=$term?wp_update_term($term->term_id,'rdl_group',array('name'=>$name,'parent'=>$parent)):wp_insert_term($name,'rdl_group',array('slug'=>$slug,'parent'=>$parent));
 if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
 return (int)$result['term_id'];
}
$parent=rdl_hero_typography_term('hero','هیرو');
rdl_hero_typography_term('hero-static','هیرو بدون اسلایدر',$parent);
rdl_hero_typography_term('hero-slider','هیرو اسلایدری',$parent);
rdl_hero_typography_term('button','دکمه‌ها');
rdl_hero_typography_term('carousel','کاروسل');
// Keep existing hero content and its stored URL; only normalize family membership.
foreach(array('home-hero','hero-slider','hero-typography') as $slug){
 $module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
 if($slug==='hero-typography'){
  $id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);
  if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
  update_post_meta($id,'_rdl_module',$slug);
  foreach(array('rdl_kind'=>array('section'),'rdl_fx'=>$module['effects'])as$taxonomy=>$terms){$result=wp_set_object_terms($id,$terms,$taxonomy);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
 }else{$id=$post?$post->ID:0;}
 if($id){$result=wp_set_object_terms($id,array('hero',$module['subgroup']),'rdl_group');if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
}
echo home_url('/design/hero-typography/').PHP_EOL;
echo 'Hero families separated; button family created with no demo designs.'.PHP_EOL;
