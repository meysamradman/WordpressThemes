<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
function socials():array {
 $handle=rawurlencode(social_handle());
 return [
  ['title'=>'تلگرام','url'=>'https://t.me/'.$handle,'icon'=>'social-telegram'],
  ['title'=>'ایتا','url'=>'https://eitaa.com/'.$handle,'icon'=>'social-messenger'],
  ['title'=>'اینستاگرام','url'=>'https://www.instagram.com/'.$handle.'/','icon'=>'social-instagram'],
  ['title'=>'وب‌سایت','url'=>'https://aliasadioriginal.ir/','icon'=>'social-website'],
  ['title'=>'روبیکا','url'=>'https://rubika.ir/'.$handle,'icon'=>'social-rubika'],
  ['title'=>'آپارات','url'=>'https://www.aparat.com/'.$handle,'icon'=>'social-aparat'],
 ];
}
add_filter('body_class',static function(array $classes):array{$classes[]='rdc-inv';return $classes;});
add_filter('radmanct/page-title/enabled','__return_false');
add_filter('radmanct/contact/data',static fn(array $data):array=>array('channels'=>array()));
add_filter('radmanct/footer/data',static function(array $data):array {
 $data['links']=array(array('title'=>'خانه','url'=>home_url('/')),array('title'=>'موسیقی','url'=>\RadmanCT\Music\url()),array('title'=>'وبلاگ','url'=>home_url('/blog/')),array('title'=>'درباره ما','url'=>home_url('/about-us/')),array('title'=>'تماس با ما','url'=>home_url('/contact-us/')));
 $data['services']=array();$data['socials']=socials();$data['phone']='';$data['messenger_phone']='';$data['email']='';$data['address']='';$data['hours']='';return $data;
});

// Editorial placeholders until the artist supplies the final media and copy.
add_filter('radmanct/music-home/data',static function(array $data):array {
 return array_merge($data,array('label'=>'جدیدترین آثار','tracks'=>'آخرین موزیک‌ها','hero_desc'=>'صدایی برای روایت لحظه‌ها؛ تازه‌ترین آثار، موسیقی و تصویر را اینجا دنبال کنید. این متن و تصاویر پس از دریافت محتوای نهایی هنرمند تکمیل می‌شوند.'));
});

function social_handle():string {return 'Aliasadioriginal';}
function booking():array {
 return ['phone'=>'','handle'=>'SarvGraphi'];
}
