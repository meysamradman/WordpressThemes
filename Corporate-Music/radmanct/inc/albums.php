<?php
namespace RadmanCT\Albums;
defined('ABSPATH')||exit;
function images(int $id):array {
 if(post_password_required($id))return [];
 $meta=get_post_meta($id,'_rdc_album',true);
 $ids=\RadmanCT\Content\Media\gallery_ids(is_array($meta)?($meta['images']??[]):[]);
 return array_values(array_filter($ids,static fn($image)=>\RadmanCT\PageTitle\attachment_allowed($image)));
}
function card(\WP_Post $post):void {
 $allowed=!post_password_required($post);$images=$allowed?images($post->ID):[];$cover=$allowed?(int)get_post_thumbnail_id($post):0;
 if(!\RadmanCT\PageTitle\attachment_allowed($cover))$cover=$images[0]??0;
 $preview=count($images)===1?wp_get_attachment_image_url($images[0],'full'):false;
 $label=($preview?'نمایش عکس ':'مشاهدهٔ آلبوم ').get_the_title($post);
 echo '<article class="rdc-album-card"><a class="rdc-album-card__cover rdc-media-frame" href="'.esc_url($preview?:get_permalink($post)).'" aria-label="'.esc_attr($label).'"'.($preview?' data-rdc-gal data-rdc-caption="'.esc_attr(get_the_title($post)).'"':'').'>';
 if($cover)echo wp_get_attachment_image($cover,'medium_large',false,['sizes'=>'(max-width:600px) 90vw,(max-width:900px) 45vw,400px']);else echo '<span class="rdc-album-card__empty">'.\RadmanCT\icon('image').'</span>';
 echo '</a><div class="rdc-album-card__copy"><h2>'.esc_html(get_the_title($post)).'</h2>';
 if($allowed)echo '<p>'.esc_html(number_format_i18n(count($images)).' عکس').'</p>';
 echo '</div></article>';
}

function page(int $id):array {
 $images=images($id);$total=count($images);$pages=max(1,(int)ceil($total/16));$raw=get_query_var('gallery_page',1);
 $current=is_scalar($raw)&&ctype_digit((string)$raw)?max(1,min($pages,(int)$raw)):1;
 $base=str_replace('999999999','%#%',add_query_arg('gallery_page',999999999,get_permalink($id)));
 return ['images'=>array_slice($images,($current-1)*16,16),'current'=>$current,'total'=>$pages,'base'=>$base];
}

add_action('template_redirect',static function():void {
 if(!is_singular('album')||is_preview()||post_password_required())return;
 $raw=get_query_var('gallery_page',null);
 if($raw===null)return;
 $id=get_queried_object_id();
 $page=page($id);
 $current=$page['current'];
 if($current>1&&is_scalar($raw)&&(string)$raw===(string)$current)return;
 $url=get_permalink($id);
 if($current>1)$url=add_query_arg('gallery_page',$current,$url);
 wp_safe_redirect($url,302);
 exit;
},9);
