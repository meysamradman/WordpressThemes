<?php
namespace RadmanLibrary\Designs\Text3D;
defined('ABSPATH')||exit;
function data():array {
 return apply_filters('rdl/text-3d/data',[
  'lines'=>['تکامل ایده‌ها','در مسیر','طراحی ماندگار'],
  'duration'=>1.25,'delay'=>.05,'stagger'=>.2,'pointer'=>true,
 ]);
}
