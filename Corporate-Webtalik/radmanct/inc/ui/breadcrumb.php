<?php
namespace RadmanCT\Breadcrumb;
defined('ABSPATH')||exit;

function page_item(?\WP_Post $page):array {
 if(!$page||!is_post_publicly_viewable($page)||$page->post_password!=='')return [];
 return ['title'=>get_the_title($page),'url'=>get_permalink($page)];
}

function section_page():?\WP_Post {
 if(!is_page()||is_front_page())return null;
 $slug=apply_filters('radmanct/breadcrumb/section-page','');
 return $slug!==''?get_page_by_path($slug,OBJECT,'page'):null;
}

function data(string $title):array {
 if(is_front_page()||$title==='')return [];
 $items=[['title'=>__('Home','radmanct'),'url'=>home_url('/')]];

 if(is_page()){
  $page_id=get_queried_object_id();
  $section=section_page();
  $ancestors=array_reverse(get_post_ancestors($page_id));
  if($section&&$section->ID!==$page_id&&!in_array($section->ID,$ancestors,true)){
   array_unshift($ancestors,$section->ID);
  }
  foreach($ancestors as $id){
   $item=page_item(get_post($id));
   if($item&&$item['url']!==$items[0]['url'])$items[]=$item;
  }
 }elseif(is_singular('project')||is_tax('project_category')){
  $url=get_post_type_archive_link('project');
  if($url)$items[]=['title'=>__('Projects','radmanct'),'url'=>$url];
 }elseif(is_singular('post')||is_category()||is_tag()||is_date()||is_author()){
  $blog_id=(int)get_option('page_for_posts');
  $item=$blog_id?page_item(get_post($blog_id)):[];
  if($item)$items[]=$item;
 }

 $items[]=['title'=>$title,'url'=>''];
 return $items;
}

function render(array $items):void {
 if(count($items)<2)return;
 $last=array_key_last($items);
 echo '<nav class="rdc-breadcrumb" aria-label="'.esc_attr__('Breadcrumb','radmanct').'"><ol>';
 foreach($items as $index=>$item){
  if(!isset($item['title']))continue;
  echo '<li>';
  if($index===$last)echo '<span aria-current="page">'.esc_html($item['title']).'</span>';
  elseif(!empty($item['url']))echo '<a href="'.esc_url($item['url']).'">'.esc_html($item['title']).'</a>';
  else echo '<span>'.esc_html($item['title']).'</span>';
  echo '</li>';
 }
 echo '</ol></nav>';
}

add_filter('wpseo_breadcrumb_links',static function(array $links):array {
 $section=page_item(section_page());
 if(!$section||count($links)<2)return $links;
 foreach($links as $link){
  if(($link['url']??'')===$section['url'])return $links;
 }
 array_splice($links,1,0,[['text'=>$section['title'],'url'=>$section['url']]]);
 return $links;
});
