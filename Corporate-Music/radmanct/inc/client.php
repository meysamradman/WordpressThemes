<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
add_filter('body_class',static function(array $classes):array{$classes[]='rdc-inv';return $classes;});
add_filter('radmanct/page-title/enabled','__return_false');
add_filter('radmanct/contact/data',static fn(array $data):array=>array('channels'=>array()));
add_filter('radmanct/footer/data',static function(array $data):array {
 $data['links']=array(array('title'=>'خانه','url'=>home_url('/')),array('title'=>'موسیقی','url'=>\RadmanCT\Music\url()),array('title'=>'وبلاگ','url'=>home_url('/blog/')),array('title'=>'درباره ما','url'=>home_url('/about-us/')),array('title'=>'تماس با ما','url'=>home_url('/contact-us/')));
 $data['services']=array();$data['socials']=array();$data['phone']='';$data['messenger_phone']='';$data['email']='';$data['address']='';$data['hours']='';return $data;
});
