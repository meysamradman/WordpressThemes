<?php
namespace RadmanLibrary\Designs\Particles;
defined('ABSPATH')||exit;
function data():array {
 return apply_filters('rdl/particles/data',array(
  'label'=>'ذرات پیوندخورده',
  'title'=>'هر نقطه، آغاز یک ارتباط.',
  'description'=>'حرکتی آرام از نقطه‌ها و خط‌ها؛ برای پس‌زمینه‌ای زنده که به محتوای شما فضا می‌دهد.',
  'options'=>array('number'=>64,'size'=>2.4,'speed'=>.65,'distance'=>150),
 ));
}
