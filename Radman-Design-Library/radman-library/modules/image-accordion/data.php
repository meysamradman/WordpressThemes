<?php
namespace RadmanLibrary\Designs\ImageAccordion;
defined('ABSPATH')||exit;
function data():array {
 $media=get_option('rdl_elementra_media',[]);
 return apply_filters('rdl/image-accordion/data',[
  ['image'=>(int)($media['street']??0),'title'=>'روایت تازهٔ یک استایل','label'=>'هویت بصری','desc'=>'از جزئیات کوچک تا تصویری که در ذهن می‌ماند؛ روایتی روشن برای یک ایدهٔ تازه.'],
  ['image'=>(int)($media['green']??0),'title'=>'رنگ، فرم و یک نگاه تازه','label'=>'طراحی محصول','desc'=>'ترکیبی هدفمند از رنگ و فرم برای معرفی محصول با هویتی متمایز و به‌یادماندنی.'],
  ['image'=>(int)($media['arch']??0),'title'=>'زیبایی در هندسهٔ فضا','label'=>'معماری و تصویر','desc'=>'خطوط و نور در کنار هم قرار می‌گیرند تا سادگی، عمق و تعادل را روایت کنند.'],
 ]);
}
