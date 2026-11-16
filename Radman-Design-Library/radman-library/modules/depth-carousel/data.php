<?php
namespace RadmanLibrary\Designs\DepthCarousel;
defined('ABSPATH')||exit;
function data():array {
 $photos=get_option('rdl_reference_media',[]);
 $ids=array_values(array_filter(array_map(static fn($photo)=>(int)($photo['id']??0),is_array($photos)?$photos:[])));
 $labels=[['امنیت سایبری','راهکارهای امن برای دنیای دیجیتال','shield'],['رستوران و کافه','طعم‌هایی برای به‌خاطرسپردن','coffee'],['مد و فشن','تجربه‌ای برای یک سبک تازه','image'],['سلامت و پزشکی','طراحی برای اعتماد و آرامش','heart'],['نرم‌افزار و SaaS','یک پلتفرم برای ایده‌های بزرگ','layers'],['املاک و ساخت‌وساز','فضایی متناسب با سبک زندگی شما','image'],['فروشگاه آنلاین','خریدی ساده، تجربه‌ای ماندگار','box'],['آموزش و یادگیری','مسیر تازه‌ای برای رشد مهارت‌ها','file-text']];
 $cards=[];
 foreach($labels as $index=>[$title,$desc,$icon])$cards[]=['title'=>$title,'desc'=>$desc,'icon'=>$icon,'image'=>$ids[[0,1,3,4,5,2,3,0][$index]%max(1,count($ids))]??0];
 return apply_filters('rdl/depth-carousel/data',['title'=>'ایده‌ها، در بُعدی تازه','desc'=>'طراحی تجربه‌های متفاوت برای هر کسب‌وکار','cards'=>$cards,'direction'=>isset($_GET['rdl_dir'])&&is_string($_GET['rdl_dir'])&&sanitize_key(wp_unslash($_GET['rdl_dir']))==='ltr'?'ltr':'rtl']);
}
