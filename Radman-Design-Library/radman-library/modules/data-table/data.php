<?php
namespace RadmanLibrary\Designs\DataTable;
defined('ABSPATH')||exit;

function data():array {
 return apply_filters('rdl/data-table/data',array(
  'label'=>'اطلاعات در یک نگاه',
  'title'=>'جزئیات، روشن و دقیق.',
  'description'=>'یک چیدمان منظم برای ارائهٔ مشخصات؛ با تمرکز بر خوانایی و مقایسهٔ آسان اطلاعات.',
  'caption'=>'مشخصات فنی پنل آلومینیومی',
  'model'=>'R-240',
  'columns'=>array('ویژگی','توضیحات','مقدار','واحد'),
  'rows'=>array(
   array('cells'=>array('جنس بدنه','آلومینیوم با پوشش محافظ','۶۰۶۳','آلیاژ')),
   array('cells'=>array('ابعاد','طول × عرض پنل','۱۲۰ × ۸۰','سانتی‌متر')),
   array('cells'=>array('ضخامت','ضخامت یکنواخت ورق','۲','میلی‌متر')),
   array('cells'=>array('وزن','وزن خالص هر پنل','۲٫۴','کیلوگرم')),
   array('cells'=>array('پوشش سطح','رنگ پودری الکترواستاتیک','مات','نوع پوشش')),
   array('cells'=>array('کاربرد','قابل استفاده در فضای داخلی','دکوراتیو','رده')),
  ),
 ));
}
