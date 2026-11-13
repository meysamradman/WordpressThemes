<?php
namespace RadmanLibrary\Designs\HeroTypography;
defined('ABSPATH')||exit;

/** Workshop media only. Replace this filter and remap attachments for a client. */
function data():array {
 $media=get_option('rdl_hero_typography_media',array());
 return apply_filters('rdl/hero-typography/data',array(
  'label'=>'طراحی خلاق، برای روایت متفاوت شما',
  'intro'=>'ایده‌های تازه، طراحی‌های منحصربه‌فرد؛ برای برندی که با خلاقیت دیده می‌شود.',
  'desc'=>'از خیال تا تصویری ماندگار؛ با نگاه تازه، دقت در جزئیات و جسارت در طراحی.',
  'image'=>(int)($media['portrait']??0),
  'background'=>(int)($media['background']??0),
  'rows'=>array(
   array(array('text'=>'خیال را')),
   array(array('text'=>'با'),array('image'=>(int)($media['detail-one']??0)),array('text'=>'خلاقیت')),
   array(array('mark'=>true),array('text'=>'به واقعیت')),
   array(array('image'=>(int)($media['detail-two']??0)),array('text'=>'تبدیل می‌کنیم')),
   array(array('text'=>'دقیق و ماندگار')),
  ),
  'socials'=>array(
   array('label'=>'INSTAGRAM','url'=>''),array('label'=>'LINKEDIN','url'=>''),
   array('label'=>'BEHANCE','url'=>''),array('label'=>'PINTEREST','url'=>''),
  ),
 ));
}
