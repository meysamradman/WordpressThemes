<?php
namespace RadmanLibrary\Designs\TextMarquee;
defined('ABSPATH')||exit;
function data():array {$media=get_option('rdl_section_media',[]);return apply_filters('rdl/text-marquee/data',['words'=>['توسعه‌دهندهٔ وب','طراح تجربهٔ کاربری'],'flower'=>(int)($media['aziz-flower']??0),'image'=>(int)($media['aziz-profile']??0),'label'=>'روایت کوتاه من']);}
