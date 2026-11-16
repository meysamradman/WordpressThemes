<?php
namespace RadmanCT\CompactTitle;
defined('ABSPATH')||exit;
function is_used():bool {
 return !is_front_page()&&!\RadmanCT\PageTitle\is_used()&&!is_singular(['post','project'])&&(is_page()||is_home()||is_archive()||is_search());
}
function data():array {
 $id=is_page()?get_queried_object_id():0;
 $public=!$id||(is_post_publicly_viewable($id)&&!post_password_required($id));
 $data=apply_filters('radmanct/compact-title/data',['title'=>\RadmanCT\page_heading(),'label'=>'','accent'=>'','button'=>[],'desc'=>$public&&$id?wp_strip_all_tags(get_post_field('post_excerpt',$id)):'','image'=>0]);
 foreach(['title','label','accent','desc'] as $key)$data[$key]=is_scalar($data[$key]??null)?(string)$data[$key]:'';
 $data['button']=is_array($data['button']??null)?$data['button']:[];
 $data['image']=absint($data['image']??0);
 if(!$public){$data['desc']=$data['label']=$data['accent']='';$data['button']=[];$data['image']=0;}
 if(!\RadmanCT\PageTitle\attachment_allowed($data['image']))$data['image']=0;
 return $data;
}
