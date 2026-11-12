<?php
namespace RadmanLibrary\Designs\HeroSlider;
defined('ABSPATH')||exit;

/** Replace this data filter and remap attachments when transferring to a client. */
function data():array {
 $media=get_option('rdl_section_media',array());
 return apply_filters('rdl/hero-slider/data',array(
  'label'=>'هر ایده، شروع یک روایت تازه',
  'slides'=>array(
   array('image'=>(int)($media['jane-brand']??0),'label'=>'۰۱ / هویت و فرم','title'=>'نگاهی تازه،\nفراتر از چارچوب','desc'=>'با نگاهی دقیق به رنگ، فرم و جزئیات، برای ایده‌های شما فضایی تازه می‌سازیم.','button'=>'کشف مسیر طراحی','url'=>home_url('/design/image-accordion/')),
   array('image'=>(int)($media['jane-web']??0),'label'=>'۰۲ / طراحی و تجربه','title'=>'روایت شما،\nبا امضای متفاوت','desc'=>'از نخستین تصویر تا تجربهٔ نهایی؛ هویتی که دیده می‌شود و در ذهن می‌ماند.','button'=>'دیدن نمونه‌کارها','url'=>home_url('/projects/')),
   array('image'=>(int)($media['jane-photo']??0),'label'=>'۰۳ / نور و تصویر','title'=>'برای تجربه‌های\nبه‌یادماندنی','desc'=>'ترکیبی فکرشده از نور و تصویر، برای ارتباطی روشن‌تر میان شما و مخاطبتان.','button'=>'تماشای طرح‌ها','url'=>home_url('/')),
  ),
 ));
}
