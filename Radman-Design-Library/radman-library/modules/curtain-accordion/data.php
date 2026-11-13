<?php
namespace RadmanLibrary\Designs\CurtainAccordion;
defined('ABSPATH')||exit;
function data():array {
 $media=get_option('rdl_curtain_media',array());
 $copy=array(
  array('رزین‌ها','رزین‌های مورد استفاده در صنایع رنگ، پوشش، چسب و کاربردهای صنعتی.'),
  array('حلال‌های صنعتی','انواع حلال‌های داخلی و خارجی مورد استفاده در فرمولاسیون‌های صنعتی.'),
  array('ادتیوهای تخصصی','افزودنی‌های تخصصی برای تنظیم و بهبود عملکرد فرمولاسیون محصول.'),
  array('رنگدانه‌های صنعتی','تیتان و پیگمنت‌های مورد استفاده در صنایع رنگ، پوشش و چاپ.'),
  array('پودر روی (Zinc Dust)','ماده اولیه مورد استفاده در فرمولاسیون پوشش‌های حفاظتی و صنعتی.'),
  array('پرکننده‌ها و مواد کمکی','مواد کمکی برای تنظیم خواص فیزیکی و عملکرد نهایی فرمولاسیون.'),
 );
 $items=array();foreach($copy as $i=>$item)$items[]=array('title'=>$item[0],'desc'=>$item[1],'image'=>(int)($media['panel-'.$i]??0),'icon'=>(int)($media['icon-'.$i]??0));
 return apply_filters('rdl/curtain-accordion/data',array('title'=>'آکاردئون ستون‌های بازشونده','background'=>(int)($media['background']??0),'items'=>$items));
}
function media(int $id,string $size='large',array $attrs=array()):string {
 if(!$id||get_post_type($id)!=='attachment'||!in_array(get_post_status($id),array('inherit','publish'),true))return '';
 $parent=wp_get_post_parent_id($id);if($parent&&(!is_post_publicly_viewable($parent)||post_password_required($parent)))return '';
 return wp_get_attachment_image($id,$size,false,$attrs);
}
