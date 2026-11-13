<?php
namespace RadmanCT\PageTitle;
defined('ABSPATH')||exit;

function is_used():bool { return is_page()&&!is_front_page()&&!is_home()&&(bool)apply_filters('radmanct/page-title/enabled',true); }
function attachment_allowed(int $id):bool {
 if(!$id||get_post_type($id)!=='attachment'||!in_array(get_post_status($id),array('inherit','publish'),true))return false;
 $parent=wp_get_post_parent_id($id);
 if($parent&&(!is_post_publicly_viewable($parent)||post_password_required($parent)))return false;
 $url=wp_get_attachment_url($id);
 return $url&&wp_parse_url($url,PHP_URL_HOST)===wp_parse_url(home_url('/'),PHP_URL_HOST)&&wp_parse_url($url,PHP_URL_PORT)===wp_parse_url(home_url('/'),PHP_URL_PORT);
}
function data(array $args=array()):array {
 $id=is_singular()?get_queried_object_id():0;
 $public=!$id||(is_post_publicly_viewable($id)&&!post_password_required($id));
 $title=\RadmanCT\page_heading();
 $trail=array(array('title'=>__('Home','radmanct'),'url'=>home_url('/')));
 if(is_singular('post')){
  $blog=(int)get_option('page_for_posts');
  if($blog&&get_post_status($blog)==='publish')$trail[]=array('title'=>get_the_title($blog),'url'=>get_permalink($blog));
 }elseif(is_singular('project'))$trail[]=array('title'=>__('Projects','radmanct'),'url'=>get_post_type_archive_link('project'));
 $trail[]=array('title'=>$title,'url'=>'');
 $defaults=array('type'=>'cover','title'=>$title,'accent'=>'','desc'=>$public&&$id?wp_strip_all_tags(get_post_field('post_excerpt',$id)):'','label'=>'','button'=>array(),'link'=>array(),'image'=>$public&&$id?(int)get_post_thumbnail_id($id):0,'video'=>0,'media_inset'=>false,'media_flat'=>false,'art_tracks'=>array(),'art_nodes'=>array(),'art_draw'=>false,'art_matrix'=>array(),'trail'=>$trail);
 $data=array_replace($defaults,apply_filters('radmanct/page-title/data',$defaults),$args);
 $data['type']=in_array($data['type'],array('split','cover'),true)?$data['type']:'cover';
 foreach(array('title','accent','desc','label') as $key)$data[$key]=is_scalar($data[$key])?(string)$data[$key]:'';
 foreach(array('button','link','trail','art_tracks','art_nodes','art_matrix') as $key)if(!is_array($data[$key]))$data[$key]=array();
 $data['image']=absint($data['image']);$data['video']=absint($data['video']);
 if(!$public){$data['desc']=$data['accent']=$data['label']='';$data['button']=$data['link']=array();$data['image']=$data['video']=0;}
 if(!attachment_allowed($data['image'])||!wp_attachment_is_image($data['image']))$data['image']=0;
 if(!attachment_allowed($data['video'])||!in_array(get_post_mime_type($data['video']),array('video/mp4','video/webm'),true))$data['video']=0;
 return $data;
}
function trail(array $items):void {
 if(!$items)return;
 echo '<nav class="rdc-pt__trl" aria-label="'.esc_attr__('Breadcrumb','radmanct').'"><ol>';
 foreach($items as $index=>$item){
  if(!is_array($item)||!isset($item['title']))continue;
  echo '<li>';
  if($index!==array_key_last($items)&&!empty($item['url']))echo '<a href="'.esc_url($item['url']).'">'.esc_html($item['title']).'</a>';
  else echo '<span aria-current="page">'.esc_html($item['title']).'</span>';
  echo '</li>';
 }
 echo '</ol></nav>';
}
function heading(array $data):void {
 echo '<h1 class="rdc-pt__ttl">'.esc_html($data['title']);
 if($data['accent']!=='')echo '<span class="rdc-pt__acc">'.esc_html($data['accent']).'</span>';
 echo '</h1>';
}
function actions(array $data):void {
 $button=$data['button'];$link=$data['link'];
 if(empty($button['url'])&&empty($link['url']))return;
 echo '<div class="rdc-pt__acts">';
 if(!empty($button['url'])&&!empty($button['label']))echo \RadmanCT\button($button['label'],$button['url'],array('icon'=>$button['icon']??'arrow-up-right'));
 if(!empty($link['url'])&&!empty($link['label']))echo '<a class="rdc-pt__lnk" href="'.esc_url($link['url']).'">'.esc_html($link['label']).'</a>';
 echo '</div>';
}
function media(array $data):void {
 if($data['image']||$data['video'])\RadmanCT\Assets\script('pages/page-title');
 echo '<div class="rdc-pt__md" aria-hidden="true">';
 echo '<div class="rdc-pt__inr" data-rdc-scr-spd=".4">';
 $art=$data['image']&&!$data['video']&&!empty($data['art_tracks']);
 if($art)echo '<div class="rdc-pt__art" data-rdc-pt-art data-rdc-ofr-art>';
 if($data['image'])echo wp_get_attachment_image($data['image'],'full',false,array('class'=>'rdc-pt__im','alt'=>'','loading'=>'eager','fetchpriority'=>'high','decoding'=>'async','sizes'=>'100vw'));
 if($art){get_template_part('template-parts/ui/title-art',null,array('tracks'=>$data['art_tracks'],'nodes'=>$data['art_nodes']??array(),'draw'=>!empty($data['art_draw']),'matrix'=>$data['art_matrix']??array()));echo '</div>';}
 if($data['video']){
  echo '<video class="rdc-pt__vid" muted loop playsinline preload="none" tabindex="-1" data-src="'.esc_url(wp_get_attachment_url($data['video'])).'"></video>';
 }
 echo '</div></div>';
 if($data['video'])echo '<button class="rdc-pt__play" type="button" data-rdc-pt-play aria-pressed="false" aria-label="'.esc_attr__('Pause background video','radmanct').'" data-play-label="'.esc_attr__('Play background video','radmanct').'" data-pause-label="'.esc_attr__('Pause background video','radmanct').'" hidden>'.\RadmanCT\icon('pause').\RadmanCT\icon('play').'</button>';
}
