<?php
namespace RadmanLibrary\Designs\VentureCarousel;
defined('ABSPATH')||exit;
function data():array {
 return apply_filters('rdl/venture-carousel/data',[
  'title'=>'ایده‌هایی که آینده را می‌سازند','desc'=>'هر ایده، یک مسیر تازه. برای کشف پروژه‌ها کارت‌ها را جابه‌جا کنید.',
  'direction'=>isset($_GET['rdl_dir'])&&is_string($_GET['rdl_dir'])&&sanitize_key(wp_unslash($_GET['rdl_dir']))==='rtl'?'rtl':'ltr',
  'cards'=>[
   ['name'=>'AURA','mark'=>'ribbon','desc'=>'تجربه‌ای تازه برای شنیدن، کشف‌کردن و به‌اشتراک‌گذاشتن داستان‌ها.','link'=>'','color'=>'#7863ef','ink'=>'#f0eef5'],
   ['name'=>'NEXORA','mark'=>'star','desc'=>'فناوری هوشمند برای پیوند دادن نیازها به فرصت‌های درست.','link'=>'','color'=>'#c0e0c9'],
   ['name'=>'STRATA','mark'=>'steps','desc'=>'داده‌های پراکنده، ساختاری روشن و تصمیم‌هایی بهتر برای فردا.','link'=>'','color'=>'#f2a37e'],
   ['name'=>'VELO','mark'=>'orbit','desc'=>'مدل‌سازی دقیق برای تیم‌هایی که سریع‌تر به آینده حرکت می‌کنند.','link'=>'','color'=>'#b5ccec'],
   ['name'=>'ORBIT','mark'=>'grid','desc'=>'زیرساختی مطمئن برای پرواز ایده‌ها و گسترش مرزهای ممکن.','link'=>'','color'=>'#e6d784'],
  ],
 ]);
}
