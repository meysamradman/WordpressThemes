<?php
namespace RadmanLibrary\Designs\TextLoop;
defined('ABSPATH')||exit;
function data():array {return apply_filters('rdl/text-loop/data',['words'=>['توسعه‌دهندهٔ وب','طراح تجربهٔ کاربری'],'flower'=>(int)(get_option('rdl_section_media',[])['aziz-flower']??0)]);}
