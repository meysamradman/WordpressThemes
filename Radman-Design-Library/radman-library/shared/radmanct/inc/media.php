<?php
namespace RadmanCT\Media;
defined('ABSPATH')||exit;
function frame(int $post_id,string $image):array {
 $out=array('html'=>$image,'control'=>'');if(post_password_required($post_id))return array('html'=>'','control'=>'');
 $id=(int)get_post_meta($post_id,'_webtalik_card_video',true);if(!$id||!wp_attachment_is('video',$id))return $out;
 $attachment=get_post($id);$parent=$attachment?get_post($attachment->post_parent):null;if(get_post_status($id)==='private'||($parent&&(!in_array($parent->post_status,array('publish','inherit'),true)||post_password_required($parent))))return $out;
 $url=wp_get_attachment_url($id);$uploads=wp_upload_dir(null,false);$mime=get_post_mime_type($id);
 if(!$url||!str_starts_with($url,trailingslashit($uploads['baseurl']))||!in_array($mime,array('video/mp4','video/webm','video/ogg'),true))return $out;
 $image=$image?:'<span class="rdc-card-pre-pst" aria-hidden="true"></span>';
 $key=wp_unique_id('rdc-pv-');$mode=get_post_meta($post_id,'_webtalik_card_mode',true)==='hover'?'hover':'auto';
 $out['html']='<span class="rdc-card-pre" data-rdc-card-pre data-mode="'.$mode.'">'.$image.'<video id="'.esc_attr($key).'" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-src="'.esc_url($url).'" data-mime="'.esc_attr($mime).'"></video></span>';
 return $out;
}
function badge(int $post_id):void {
 if(post_password_required($post_id))return;
 $post=get_post($post_id);if(!$post)return;$type=get_post_format($post_id);$slugs=wp_get_post_terms($post_id,'category',array('fields'=>'slugs'));$names=is_wp_error($slugs)?'':urldecode(implode(' ',$slugs));
 if(has_block('core/audio',$post)||preg_match('/podcast|audio|پادکست/u',$names))$type='audio';
 elseif(has_block('core/video',$post)||preg_match('/video|ویدئو/u',$names))$type='video';
 if(in_array($type,array('audio','video'),true))echo '<span class="rdc-md-bdg" aria-label="'.esc_attr($type==='audio'?__('Podcast','radmanct'):__('Video','radmanct')).'">'.\RadmanCT\icon($type==='audio'?'music':'play').'</span>';
}
