<?php
namespace RadmanCT\MusicHome;
defined('ABSPATH')||exit;
function data():array {static $data;return $data??=apply_filters('radmanct/music-home/data',array('label'=>'جدیدترین آثار','title'=>get_bloginfo('name'),'desc'=>get_bloginfo('description'),'tracks'=>'آخرین موزیک‌ها'));}
function entries(string $type,int $limit):array {
 $query=['post_type'=>$type==='track'?'track':'post','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'no_found_rows'=>true];
 if($type==='track')$query['orderby']=\RadmanCT\Music\NEWEST_FIRST;
 return get_posts($query);
}
const FEATURED_META='_rdc_home_feature_tracks';
const BANNERS_META='_rdc_home_feature_banners';
const FEATURED_LIMIT=3;
const BANNER_DEVICES=[
 'desktop'=>'دسکتاپ',
 'tablet'=>'تبلت',
 'mobile'=>'موبایل',
];

function banners(int $page):array {
 $saved=get_post_meta($page,BANNERS_META,true);
 return is_array($saved)?$saved:[];
}

function banner_id(mixed $value):int {
 $id=is_scalar($value)?(int)filter_var($value,FILTER_VALIDATE_INT):0;
 return $id>0 && wp_attachment_is_image($id) && \RadmanCT\PageTitle\attachment_allowed($id)?$id:0;
}

function banner_picture(\WP_Post $track,array $images,bool $first):void {
 $sizes='(max-width: 1348px) 95vw, 1300px';
 $images=array_map(__NAMESPACE__.'\\banner_id',array_intersect_key($images,BANNER_DEVICES));
 $desktop=$images['desktop']??0;
 $default=$desktop?:banner_id(get_post_thumbnail_id($track))?:($images['tablet']??0)?:($images['mobile']??0);
 $attrs=['alt'=>'','sizes'=>$sizes,'loading'=>$first?'eager':'lazy','fetchpriority'=>$first?'high':'low'];
 $html=$default?wp_get_attachment_image($default,'large',false,$attrs):'';
 $sources=[];
 foreach(['mobile'=>'(max-width: 767px)','tablet'=>'(767px < width <= 900px)'] as $device=>$query){
  $id=$images[$device]??0;
  if(!$id||$id===$default)continue;
  $src=wp_get_attachment_image_src($id,'large');
  if(!$src)continue;
  $sources[$device]=[$query,wp_get_attachment_image_srcset($id,'large')?:esc_url_raw($src[0]),$src[1],$src[2]];
 }
 if(!$html)return;
 ?>
 <picture class="rdc-feature__art<?php if(!$desktop)echo ' rdc-feature__art--cvr'; ?>">
  <?php foreach($sources as [$media,$srcset,$width,$height]): ?>
   <source media="<?php echo esc_attr($media); ?>" srcset="<?php echo esc_attr($srcset); ?>" sizes="<?php echo esc_attr($sizes); ?>" width="<?php echo esc_attr($width); ?>" height="<?php echo esc_attr($height); ?>">
  <?php endforeach; ?>
  <?php echo $html; ?>
 </picture>
 <?php
}

function selected_tracks(int $page):array {
 $saved=get_post_meta($page,FEATURED_META,true);
 if(!is_array($saved))return [];
 $ids=[];
 foreach($saved as $id){
  if(!is_scalar($id))continue;
  $id=absint($id);
  if($id&&!in_array($id,$ids,true))$ids[]=$id;
  if(count($ids)===FEATURED_LIMIT)break;
 }
 return $ids;
}

function featured():array {
 $query=['post_type'=>'track','post_status'=>'publish','has_password'=>false,'no_found_rows'=>true];
 $ids=selected_tracks((int)get_option('page_on_front'));
 if($ids){
  $tracks=get_posts($query+['post__in'=>$ids,'posts_per_page'=>FEATURED_LIMIT,'orderby'=>'post__in']);
  if($tracks)return $tracks;
 }
 return get_posts($query+['posts_per_page'=>FEATURED_LIMIT,'orderby'=>\RadmanCT\Music\NEWEST_FIRST]);
}
