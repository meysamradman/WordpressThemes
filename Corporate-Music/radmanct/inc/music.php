<?php
namespace RadmanCT\Music;
defined('ABSPATH')||exit;
require_once __DIR__.'/content/music.php';
function native(?\WP_Query $q=null):bool {$q??=$GLOBALS['wp_query'];return $q->is_singular('track')||$q->is_post_type_archive('track')||$q->is_tax('music_category')||($q->is_search()&&$q->get('post_type')==='track');}
function meta(int $id):array {
    $value=get_post_meta($id,'_rdc_track',true);$value=is_array($value)?$value:[];$out=[];
    foreach(['artist','poet','arranger','composer','album','released','lyrics'] as $key)$out[$key]=is_scalar($value[$key]??'')?(string)($value[$key]??''):'';
    foreach(['audio','download'] as $key)$out[$key]=is_scalar($value[$key]??0)?absint($value[$key]??0):0;
    return $out;
}
function file(int $id):array {
    if(!$id||get_post_type($id)!=='attachment'||!str_starts_with((string)get_post_mime_type($id),'audio/')||!in_array(get_post_status($id),['inherit','publish'],true))return [];
    $parent=wp_get_post_parent_id($id);if($parent&&(!is_post_publicly_viewable($parent)||post_password_required($parent)))return [];
    $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);if(!$url||!str_starts_with($url,trailingslashit($uploads['baseurl'])))return [];
    $metadata=wp_get_attachment_metadata($id);return ['url'=>$url,'duration'=>$metadata['length_formatted']??''];
}
function url():string {return get_post_type_archive_link('track')?:home_url('/music/');}
function download(int $track,array $data,array $audio):array {
 $file=$data['download']&&$data['download']!==$data['audio']?file($data['download']):$audio;
 if(!$file)return [];
 $extension=pathinfo((string)wp_parse_url($file['url'],PHP_URL_PATH),PATHINFO_EXTENSION);
 $title=sanitize_file_name(html_entity_decode(get_the_title($track),ENT_QUOTES,'UTF-8'));
 $file['filename']=$title!==''&&$extension!==''?$title.'.'.$extension:true;
 return $file;
}
function playback(int $id,array $audio,array $data):array {
 $image=(int)get_post_thumbnail_id($id);
 return ['key'=>$id,'url'=>$audio['url'],'artist'=>$data['artist'],'duration'=>$audio['duration']??'','cover'=>\RadmanCT\PageTitle\attachment_allowed($image)?(wp_get_attachment_image_url($image,'thumbnail')?:''):''];
}

function card(\WP_Post $post,string $heading='h2',bool $play=false,string $variant='compact'):void {
 $heading=in_array($heading,['h2','h3'],true)?$heading:'h2';$data=meta($post->ID);$allowed=!post_password_required($post);$url=get_permalink($post);
 $title=get_the_title($post);
 $size=['sizes'=>$variant==='list'?'(max-width:600px) 42vw,(max-width:900px) 38vw,300px':'(max-width:600px) 44vw,170px'];
 if($variant!=='list')$size['loading']='lazy';
 $cover=$allowed?\RadmanCT\Content\Media\thumbnail($post,'medium_large',$size):'';
 $frame='rdc-trk-card__cover rdc-media-frame';
 $cover_markup=$cover
  ? '<a class="'.$frame.'" href="'.esc_url($url).'" aria-label="'.esc_attr('مشاهدهٔ '.$title).'">'.$cover.'</a>'
  : '<span class="'.$frame.'" aria-hidden="true"></span>';
 if($variant==='list'){
  echo '<article class="rdc-trk-card rdc-trk-card--list rdc-media-frame-own">'.$cover_markup.'<div class="rdc-trk-card__body"><'.$heading.'><a href="'.esc_url($url).'">'.esc_html($title).'</a></'.$heading.'>';
  if($allowed)credits($data);
  echo '<div class="rdc-trk-card__action">'.\RadmanCT\icon('music').\RadmanCT\button('گوش کنید',$url,['class'=>'rdc-trk-card__listen','icon'=>'play']).'</div></div></article>';
  return;
 }

 echo '<article class="rdc-trk-card">'.$cover_markup.'<div class="rdc-trk-card__body"><'.$heading.'><a href="'.esc_url($url).'">'.esc_html($title).'</a></'.$heading.'><div class="rdc-trk-card__meta">';
 if($allowed){$terms=get_the_terms($post,'music_category');echo '<p>'.esc_html($terms&&!is_wp_error($terms)?$terms[0]->name:$data['artist']).'</p>';}
 $audio=$play&&$allowed?file($data['audio']):[];
 if($audio)echo \RadmanCT\button('پخش '.$title,'#',['class'=>'rdc-trk-card__play','icon'=>'play','icon_only'=>true,'audio'=>playback($post->ID,$audio,$data)]);
 else echo '<a class="rdc-trk-card__more" href="'.esc_url($url).'" aria-label="'.esc_attr('مشاهدهٔ '.$title).'">'.\RadmanCT\icon('play').'</a>';
 echo '</div></div></article>';
}

function credits(array $data,bool $released=false):void {
 $labels=['artist'=>'خواننده','poet'=>'شعر','arranger'=>'تنظیم','composer'=>'آهنگساز'];
 if($released)$labels+=['released'=>'تاریخ تولید','album'=>'آلبوم'];
 echo '<dl class="rdc-trk-credits">';
 foreach($labels as $key=>$label){if(empty($data[$key])&&!in_array($key,['artist','poet','arranger','composer'],true))continue;echo '<div><dt>'.esc_html($label).':</dt><dd>'.esc_html($data[$key]??'') . (empty($data[$key])?'—':'').'</dd></div>';}
 echo '</dl>';
}
