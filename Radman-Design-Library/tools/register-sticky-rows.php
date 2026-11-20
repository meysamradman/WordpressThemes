<?php
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready')||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination.');

$slug='sticky-rows';
$module=RadmanLibrary\modules()[$slug];
$family=get_term_by('slug',$module['group'],'rdl_group');
$subgroup=get_term_by('slug',$module['subgroup'],'rdl_group');
if(!$family||!$subgroup||(int)$subgroup->parent!==(int)$family->term_id)throw new RuntimeException('Sticky panels category missing.');

$post=get_page_by_path($slug,OBJECT,'rdl_design');
if($post&&get_post_meta($post->ID,'_rdl_module',true)!==$slug)throw new RuntimeException('Design slug already belongs to another module.');
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
