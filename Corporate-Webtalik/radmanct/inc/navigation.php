<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function navigation_target():string {
 if(is_front_page())return home_url('/');
 if(is_post_type_archive('project')||is_singular('project')||is_tax('project_category'))return get_post_type_archive_link('project')?:home_url('/projects/');
 if(is_home()||is_singular('post')||is_category()||is_tag()||is_date()||is_author()||is_search())return get_permalink((int)get_option('page_for_posts'))?:home_url('/blog/');
 return is_singular()?get_permalink():'';
}
function navigation_path(string $url):string {
 $parts=wp_parse_url(html_entity_decode($url,ENT_QUOTES,'UTF-8'));$home=wp_parse_url(home_url('/'));
 if(trim($url)===''||!is_array($parts)||array_key_exists('fragment',$parts))return '';
 if(isset($parts['host'])&&($parts['host']!==($home['host']??'')||($parts['port']??null)!==($home['port']??null)))return '';
 $path=trim(rawurldecode($parts['path']??''),'/');return $path===''?'/':'/'.$path.'/';
}
add_filter('wp_nav_menu_objects',static function(array $items,$args):array{
 if(($args->theme_location??'')!=='primary')return $items;
 $target=navigation_path(navigation_target());$chosen=false;
 foreach($items as$index=>$original){$item=clone $original;$active=!$chosen&&$target!==''&&navigation_path((string)$item->url)===$target;
  $item->current=$active;$item->classes=array_values(array_diff((array)$item->classes,array('current-menu-item')));
  if($active){$item->classes[]='current-menu-item';$chosen=true;}$items[$index]=$item;
 }
 return $items;
},20,2);

add_filter('nav_menu_item_title',static function(string $title,$item,$args,int $depth):string{
 if(($args->theme_location??'')!=='primary'||($args->menu_class??'')!=='rdc-menu'||$depth!==0)return $title;
 $text=esc_html(wp_strip_all_tags($title));return '<span class="rdc-menu-roll"><span class="rdc-menu-roll__tx">'.$text.'</span><span class="rdc-menu-roll__copy" aria-hidden="true">'.$text.'</span></span>';
},10,4);

add_filter('nav_menu_item_id',static function(string $id,$item,$args):string{
 return ($args->menu_id??'')==='rdc-pnl-nav'?'rdc-pnl-itm-'.(int)$item->ID:$id;
},10,3);
