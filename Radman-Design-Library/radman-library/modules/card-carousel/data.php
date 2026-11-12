<?php
namespace RadmanLibrary\Designs\CardCarousel;
defined('ABSPATH')||exit;
function data():array {
 $video=(int)get_option('rdl_card_video',0);
 $photos=get_option('rdl_reference_media',array());
 $image=(int)($photos[2]['id']??0);$portrait=(int)($photos[4]['id']??0);
 $direction=isset($_GET['rdl_dir'])&&is_string($_GET['rdl_dir'])&&sanitize_key(wp_unslash($_GET['rdl_dir']))==='ltr'?'ltr':'rtl';
 $layout=isset($_GET['rdl_layout'])&&is_string($_GET['rdl_layout'])&&sanitize_key(wp_unslash($_GET['rdl_layout']))==='boxed'?'boxed':'bleed';
 return apply_filters('rdl/card-carousel/data',array(
  'direction'=>$direction,'layout'=>$layout,'title'=>'راهکارهایی برای رشد ایده‌ها','desc'=>'طراحی هوشمند، برای تجربه‌ای بهتر.','link'=>home_url('/'),
  'cards'=>array(
   array('type'=>'stat','value'=>'35×','label'=>'بهینه‌سازی فرایندها','title'=>'سریع‌تر پیش بروید.','desc'=>'تصمیم‌های بهتر، اجرای ساده‌تر.'),
   array('type'=>'photo','image'=>$image,'title'=>'از ایده تا تجربه','desc'=>'همراهی دقیق برای ساخت محصولی ماندگار.'),
   array('type'=>'progress','icon'=>'layers','title'=>'رویکردی برای آینده','desc'=>'ترکیب طراحی، داده و اتوماسیون.','label'=>'کارایی','value'=>80),
   array('type'=>'dark','value'=>'5.8×','label'=>'توسعهٔ سریع‌تر','title'=>'سیستم‌هایی که رشد می‌کنند.','desc'=>'تجربه‌ای روشن، بدون پیچیدگی.'),
   array('type'=>'video','video'=>$video,'image'=>$portrait,'title'=>'تجربه در حرکت'),
   array('type'=>'icon','icon'=>'file-text','title'=>'ساختار، پیش از جزئیات','desc'=>'پایه‌ای روشن برای هر ایدهٔ تازه.'),
   array('type'=>'photo','image'=>$portrait,'title'=>'روایت شما، نگاه ما','desc'=>'طراحی با توجه به جزئیات.'),
   array('type'=>'stat','value'=>'24/7','label'=>'همراه مسیر شما','title'=>'بهتر بسازیم.','desc'=>'کیفیت در هر قدم از مسیر.'),
   array('type'=>'icon','icon'=>'image','title'=>'جایی برای تجربه‌های تازه','desc'=>'محتوای هر کارت مستقل از هستهٔ کاروسل است.'),
  ),
 ));
}
