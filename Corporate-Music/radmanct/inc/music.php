<?php
namespace RadmanCT\Music;
defined('ABSPATH')||exit;
require_once __DIR__.'/content/music.php';
function native(?\WP_Query $q=null):bool {$q??=$GLOBALS['wp_query'];return $q->is_singular('track')||$q->is_post_type_archive('track')||$q->is_tax('music_category')||($q->is_search()&&$q->get('post_type')==='track');}
function meta(int $id):array {
    $value=get_post_meta($id,'_rdc_track',true);$value=is_array($value)?$value:[];$out=[];
    foreach(['artist','album','released','lyrics'] as $key)$out[$key]=is_scalar($value[$key]??'')?(string)($value[$key]??''):'';
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

function card(\WP_Post $post,string $heading='h2',bool $play=false):void {
 $heading=in_array($heading,['h2','h3'],true)?$heading:'h2';$data=meta($post->ID);$allowed=!post_password_required($post);$url=get_permalink($post);
 echo '<article class="rdc-trk-card"><a class="rdc-trk-card__cover" href="'.esc_url($url).'">';
 if($allowed)echo get_the_post_thumbnail($post,'medium_large',['loading'=>'lazy','sizes'=>'(max-width:600px) 44vw,170px','alt'=>get_the_title($post)]);
 echo '</a><div class="rdc-trk-card__body"><'.$heading.'><a href="'.esc_url($url).'">'.esc_html(get_the_title($post)).'</a></'.$heading.'><div class="rdc-trk-card__meta">';
 if($allowed){$terms=get_the_terms($post,'music_category');echo '<p>'.esc_html($terms&&!is_wp_error($terms)?$terms[0]->name:$data['artist']).'</p>';}
 $audio=$allowed?file((int)$data['audio']):[];
 if($play&&$audio)echo \RadmanCT\button('پخش '.get_the_title($post),'#',['class'=>'rdc-trk-card__play','icon'=>'play','icon_only'=>true,'audio'=>['key'=>$post->ID,'url'=>$audio['url']]]);
 else echo '<a class="rdc-trk-card__more" href="'.esc_url($url).'" aria-label="'.esc_attr('مشاهدهٔ '.get_the_title($post)).'">'.\RadmanCT\icon('play').'</a>';
 echo '</div></div></article>';
}
