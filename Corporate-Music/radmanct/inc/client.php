<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
function socials():array {return array(array('title'=>'تلگرام','url'=>'','icon'=>'social-telegram'),array('title'=>'پیام‌رسان','url'=>'','icon'=>'social-messenger'),array('title'=>'اینستاگرام','url'=>'','icon'=>'social-instagram'),array('title'=>'وب‌سایت','url'=>'','icon'=>'social-website'),array('title'=>'روبیکا','url'=>'','icon'=>'social-rubika'),array('title'=>'آپارات','url'=>'','icon'=>'social-aparat'));}
add_filter('body_class',static function(array $classes):array{$classes[]='rdc-inv';return $classes;});
add_filter('radmanct/page-title/enabled','__return_false');
add_filter('radmanct/contact/data',static fn(array $data):array=>array('channels'=>array()));
add_filter('radmanct/footer/data',static function(array $data):array {
 $data['links']=array(array('title'=>'خانه','url'=>home_url('/')),array('title'=>'موسیقی','url'=>\RadmanCT\Music\url()),array('title'=>'وبلاگ','url'=>home_url('/blog/')),array('title'=>'درباره ما','url'=>home_url('/about-us/')),array('title'=>'تماس با ما','url'=>home_url('/contact-us/')));
 $data['services']=array();$data['socials']=socials();$data['phone']='';$data['messenger_phone']='';$data['email']='';$data['address']='';$data['hours']='';return $data;
});

add_filter('radmanct/music-home/data',static function(array $data):array {
 return array_merge($data,array('label'=>'جدیدترین آثار','tracks'=>'آخرین موزیک‌ها'));
});

function social_handle():string {return 'AliAsadiOriginal';}
function booking():array {
 return ['phone'=>'','handle'=>'SarvGraphi'];
}
