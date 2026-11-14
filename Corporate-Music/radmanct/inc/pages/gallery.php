<?php
namespace RadmanCT\ArtistGallery;
defined('ABSPATH')||exit;
function images():array {return apply_filters('radmanct/gallery/images',get_posts(array('post_type'=>'attachment','post_status'=>'inherit','post_mime_type'=>'image','posts_per_page'=>12,'fields'=>'ids')));}
