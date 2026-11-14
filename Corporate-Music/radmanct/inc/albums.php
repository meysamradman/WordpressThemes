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
 echo '<article class="rdc-album-card"><a class="rdc-album-card__cover" href="'.esc_url(get_permalink($post)).'">';
 if($cover)echo wp_get_attachment_image($cover,'medium_large',false,['loading'=>'lazy','sizes'=>'(max-width:600px) 90vw,(max-width:900px) 45vw,400px']);else echo '<span class="rdc-album-card__empty">'.\RadmanCT\icon('image').'</span>';
 echo '</a><div class="rdc-album-card__copy"><h2><a href="'.esc_url(get_permalink($post)).'">'.esc_html(get_the_title($post)).'</a></h2>';
 if($allowed)echo '<p>'.esc_html(number_format_i18n(count($images)).' عکس').'</p>';
 echo '</div></article>';
}
