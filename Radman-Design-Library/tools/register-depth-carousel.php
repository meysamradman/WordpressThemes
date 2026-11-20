<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination');
$slug='depth-carousel';
$module=RadmanLibrary\modules()[$slug];
$post=get_page_by_path($slug,OBJECT,'rdl_design');
if($post&&get_post_meta($post->ID,'_rdl_module',true)!==$slug)throw new RuntimeException('Design slug already belongs to another module.');

$parent=term_exists($module['group'],'rdl_group')?:wp_insert_term('کاروسل','rdl_group',['slug'=>$module['group']]);
if(is_wp_error($parent))throw new RuntimeException($parent->get_error_message());
$parent_id=(int)$parent['term_id'];
$name='کاروسل سه‌بعدی';
$child=get_term_by('slug',$module['subgroup'],'rdl_group');
if(!$child){
 $result=wp_insert_term($name,'rdl_group',['slug'=>$module['subgroup'],'parent'=>$parent_id]);
}elseif((int)$child->parent!==$parent_id||$child->name===$module['subgroup']){
 $result=wp_update_term($child->term_id,'rdl_group',['name'=>$child->name===$module['subgroup']?$name:$child->name,'parent'=>$parent_id]);
}
if(isset($result)&&is_wp_error($result))throw new RuntimeException($result->get_error_message());

foreach(['css-3d'=>'پرسپکتیو CSS','drag'=>'کشیدن کارت‌ها','smooth'=>'حرکت نرم'] as $effect=>$label){
 if(term_exists($effect,'rdl_fx'))continue;
 $result=wp_insert_term($label,'rdl_fx',['slug'=>$effect]);
 if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
}
if(!$post){
 $id=wp_insert_post(['post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']],true);
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 update_post_meta($id,'_rdl_module',$slug);
}else $id=$post->ID;
foreach(['rdl_group'=>[$module['group'],$module['subgroup']],'rdl_kind'=>[$module['kind']],'rdl_fx'=>$module['effects']] as $taxonomy=>$terms){
 $result=wp_set_object_terms($id,$terms,$taxonomy);
 if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
}
echo get_permalink($id).PHP_EOL;
