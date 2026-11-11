<?php
namespace RadmanLibrary\Designs\ColumnGallery;
defined('ABSPATH')||exit;
function data():array {
 $media=get_option('rdl_column_media',[]);$columns=[];
 foreach([[-.1,[140,142,143,141,140,139]],[.8,[147,144,145,146,148,149]],[-.1,[126,146,147,5,150,151]]] as [$speed,$keys])$columns[]=['speed'=>$speed,'images'=>array_map(static fn($key)=>(int)($media[$key]??0),$keys)];
 return apply_filters('rdl/column-gallery/data',$columns);
}
