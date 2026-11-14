<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
add_filter('body_class',static function(array $classes):array{$classes[]='rdc-inv';return $classes;});
add_filter('radmanct/page-title/enabled','__return_false');
add_filter('radmanct/contact/data',static fn(array $data):array=>array('channels'=>array()));
add_filter('radmanct/footer/data',static function(array $data):array {
 $data['links']=array(array('title'=>'خانه','url'=>home_url('/')),array('title'=>'موسیقی','url'=>\RadmanCT\Music\url()),array('title'=>'وبلاگ','url'=>home_url('/blog/')),array('title'=>'درباره ما','url'=>home_url('/about-us/')),array('title'=>'تماس با ما','url'=>home_url('/contact-us/')));
 $data['services']=array();$data['socials']=array(array('title'=>'تلگرام','url'=>'','icon'=>'telegram'),array('title'=>'اینستاگرام','url'=>'','icon'=>'instagram'),array('title'=>'وب‌سایت','url'=>'','icon'=>'globe'),array('title'=>'موسیقی','url'=>'','icon'=>'music'),array('title'=>'ویدئو','url'=>'','icon'=>'play'));$data['phone']='';$data['messenger_phone']='';$data['email']='';$data['address']='';$data['hours']='';return $data;
});

// Editorial placeholders until the artist supplies the final media and copy.
add_filter('radmanct/music-home/data',static function(array $data):array {
 return array_merge($data,array('label'=>'جدیدترین آثار','tracks'=>'آخرین موزیک‌ها','hero_desc'=>'صدایی برای روایت لحظه‌ها؛ تازه‌ترین آثار، موسیقی و تصویر را اینجا دنبال کنید. این متن و تصاویر پس از دریافت محتوای نهایی هنرمند تکمیل می‌شوند.'));
});
add_filter('radmanct/videos/data',static function(array $data):array {
 $media=get_option('rdc_music_home_media',array());$video=(int)($media['video']??0);
 $data['title']='آخرین ویدئوها';$data['items']=array(
 array('video'=>$video,'image'=>0,'title'=>'ویدئوی نمایشی اول','desc'=>'این ویدئو موقت است؛ عنوان، تصویر و فایل اصلی پس از دریافت جایگزین می‌شوند.'),
 array('video'=>$video,'image'=>0,'title'=>'ویدئوی نمایشی دوم','desc'=>'جای نمایش آثار تصویری هنرمند؛ ساختار نهایی بخش ویدئو بعداً مشخص می‌شود.'),
 array('video'=>$video,'image'=>0,'title'=>'ویدئوی نمایشی سوم','desc'=>'این بخش صرفاً برای بررسی چیدمان و رنگ‌بندی آماده شده است.'));
 return $data;
});
