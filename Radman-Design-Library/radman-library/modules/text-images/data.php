<?php
namespace RadmanLibrary\Designs\TextImages;
defined('ABSPATH')||exit;
function data():array {
 $media=get_option('rdl_inline_media',array());
 return apply_filters('rdl/text-images/data',array(
  array('text'=>'مربی‌گری همراه شما','image'=>(int)($media[0]??0)),
  array('text'=>'در خانه','image'=>(int)($media[1]??0)),
  array('text'=>'در باشگاه','image'=>(int)($media[2]??0)),
  array('text'=>'در هر مسیر','image'=>(int)($media[3]??0)),
 ));
}
