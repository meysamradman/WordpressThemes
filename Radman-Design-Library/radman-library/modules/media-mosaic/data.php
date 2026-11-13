<?php
namespace RadmanLibrary\Designs\Mosaic;
defined('ABSPATH')||exit;
function data():array {
 $media=get_option('rdl_mosaic_media',array());$url=home_url('/');
 $copy=array(
  array('ایده‌ای با هویت مستقل','از یک ایدهٔ اولیه تا تجربه‌ای که شخصیت برند را نشان می‌دهد؛ ترکیبی از روایت، تصویر و جزئیات.', 'image-1',false),
  array('جزئیاتی که دیده می‌شوند','طراحی روشن و منسجم با توجه به فرم، رنگ و ارتباط میان محتوا و تصویر.', 'image-2',false),
  array('روایت در حرکت','در این خانه به‌جای تصویر ثابت، ویدئوی محلی با پخش اختیاری و کنترل‌های بومی مرورگر قرار دارد.', 'poster',true),
  array('فضایی برای تجربه‌های تازه','چیدمانی منعطف برای نمایش خدمات، فرایند، مجموعه آثار یا داستان یک برند.', 'image-3',false),
 );
 $items=array();foreach($copy as $row)$items[]=array('title'=>$row[0],'desc'=>$row[1],'image'=>(int)($media[$row[2]]??0),'video'=>$row[3]?(int)($media['video']??0):0,'link'=>'بیشتر ببینید','url'=>$url);
 return apply_filters('rdl/media-mosaic/data',array('label'=>'روایت تصویری','title'=>'ایده، تصویر و حرکت در یک چیدمان','items'=>$items));
}
function allowed(int $id):bool {
 if(!$id||get_post_type($id)!=='attachment'||!in_array(get_post_status($id),array('inherit','publish'),true)||post_password_required($id))return false;
 $parent=wp_get_post_parent_id($id);return !$parent||(is_post_publicly_viewable($parent)&&!post_password_required($parent));
}
