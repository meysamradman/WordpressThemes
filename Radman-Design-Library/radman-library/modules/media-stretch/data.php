<?php
namespace RadmanLibrary\Designs\Stretch;
defined('ABSPATH')||exit;
function data():array {return apply_filters('rdl/media-stretch/data',['image'=>(int)(get_option('rdl_section_media',[])['iris']??0),'alt'=>'مانکن‌های لباس در قاب نور و سایه']);}
