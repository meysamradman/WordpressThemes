<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
function service_copy():array {
 static $copy=array();
 $page=get_queried_object();
 if(!$page instanceof \WP_Post||$page->post_type!=='page')return array();
 $slug=$page->post_name;
 if(isset($copy[$slug]))return $copy[$slug];
 $file=match($slug){
  'specialized-wordpress-design-development'=>'wordpress',
  'admin-panel-web-systems'=>'web-systems',
  'mobile-app-design-development'=>'mobile-app',
  default=>'',
 };
 return $copy[$slug]=$file?require __DIR__.'/client/services/'.$file.'.php':array();
}

add_filter('radmanct/service/intro',static function(array $data):array {
 $intro=[
  'label'=>'رویکرد ما',
  'title'=>'طراحی سایت اختصاصی را با شناخت کسب‌وکارتان آغاز می‌کنیم و با طراحی هدفمند و توسعه حرفه‌ای به نتیجه می‌رسانیم.',
  'lead'=>'از شناخت نیازها و اهداف کسب‌وکار تا طراحی و راه‌اندازی وب‌سایت، هر مرحله را متناسب با هویت برند، مخاطبان و نیازهای پروژه پیش می‌بریم.',
  'cta'=>'درباره پروژه گفت‌وگو کنیم',
  'link'=>home_url('/contact-us/'),
  'cards'=>[
   ['title'=>'طراحی با هویت شما','desc'=>'ساختار صفحه‌ها، تایپوگرافی و جزئیات رابط را با توجه به مخاطب و هویت برند شما طراحی می‌کنیم.','image'=>\RadmanCT\media_id('service-intro-brand-identity')?:\RadmanCT\media_id('service-intro-design')],
   ['title'=>'توسعه برای استفادهٔ واقعی','desc'=>'از عملکرد در موبایل تا مدیریت محتوا، سایت را با ساختاری می‌سازیم که تیم شما بتواند از آن استفاده کند و آن را توسعه دهد.','image'=>\RadmanCT\media_id('service-intro-responsive-testing')?:\RadmanCT\media_id('service-intro-experience')],
  ],
 ];
 return array_replace_recursive($intro,service_copy()['intro']??array());
});
add_filter('radmanct/service/process',static function():array {
 $process=[
  'label'=>'مسیر اجرای پروژه',
  'title'=>'مراحل طراحی و توسعه وب‌سایت اختصاصی',
  'desc'=>'هر پروژه با بررسی نیازهای کسب‌وکار آغاز می‌شود و در مسیری مشخص از برنامه‌ریزی، طراحی و توسعه تا آزمایش و راه‌اندازی پیش می‌رود. جزئیات هر مرحله متناسب با گستردگی و الزامات فنی پروژه تعیین می‌شود.',
  'steps'=>[
   ['title'=>'نیازسنجی و برنامه‌ریزی پروژه','desc'=>'در ابتدا اهداف کسب‌وکار، مخاطبان، امکانات موردنیاز و محدودیت‌های پروژه بررسی می‌شوند. بر اساس این اطلاعات، محدوده کار، ساختار اولیه، اولویت‌ها و مسیر اجرای پروژه مشخص می‌شود.','result'=>'نیازمندی‌ها و محدوده پروژه','image'=>\RadmanCT\media_id('service-process-planning')?:\RadmanCT\media_id('service-intro-experience')],
   ['title'=>'طراحی تجربه و رابط کاربری (UI/UX)','desc'=>'ساختار صفحات، مسیرهای دسترسی و نحوه تعامل کاربران طراحی می‌شود. سپس رابط بصری متناسب با هویت برند شکل می‌گیرد و طرح‌ها پیش از شروع توسعه بررسی و اصلاح می‌شوند.','result'=>'ساختار صفحات و طرح رابط کاربری','image'=>\RadmanCT\media_id('service-process-design')?:\RadmanCT\media_id('service-intro-design')],
   ['title'=>'برنامه‌نویسی و توسعه اختصاصی','desc'=>'پس از تأیید طراحی، بخش‌های فرانت‌اند و بک‌اند، امکانات موردنیاز و ارتباط بین اجزای وب‌سایت پیاده‌سازی می‌شوند. انتخاب فناوری و معماری فنی بر اساس نیازهای پروژه انجام می‌شود.','result'=>'نسخه قابل آزمایش وب‌سایت','image'=>\RadmanCT\media_id('service-process-development')?:\RadmanCT\media_id('services-web')],
   ['title'=>'آزمایش و بهینه‌سازی فنی','desc'=>'عملکرد قابلیت‌ها، نمایش صفحات در دستگاه‌های مختلف، دسترس‌پذیری و ساختار فنی بررسی می‌شود. ایرادهای شناسایی‌شده برطرف می‌شوند و موارد مرتبط با سرعت و سئوی فنی تا محدوده توافق‌شده بهینه‌سازی می‌شوند.','result'=>'نسخه بررسی و آماده انتشار','image'=>\RadmanCT\media_id('service-process-testing')?:\RadmanCT\media_id('services-systems')],
   ['title'=>'راه‌اندازی، تحویل و پشتیبانی','desc'=>'پس از بررسی‌های نهایی، وب‌سایت در محیط اصلی راه‌اندازی می‌شود. دسترسی‌ها و مستندات موردتوافق تحویل داده می‌شوند و آموزش مدیریت محتوا و خدمات پشتیبانی مطابق قرارداد انجام می‌گیرد.','result'=>'وب‌سایت راه‌اندازی‌شده و تحویل پروژه','image'=>\RadmanCT\media_id('service-process-launch')?:\RadmanCT\media_id('services-apps')],
  ],
 ];
 return array_replace_recursive($process,service_copy()['process']??array());
});
add_filter('radmanct/service/mosaic',static function(array $data):array {
 $media=array(
  [\RadmanCT\media_id('service-mosaic-ui-design')?:\RadmanCT\media_id('services-marquee-1'),0],
  [\RadmanCT\media_id('service-mosaic-modular-development')?:\RadmanCT\media_id('services-marquee-2'),0],
  [23,92],
  [\RadmanCT\media_id('service-mosaic-content-management')?:\RadmanCT\media_id('services-marquee-3'),0],
 );
 $website=array('وب‌سایتی اختصاصی، متناسب با نیاز کسب‌وکار شما','سایت شرکتی باید کسب‌وکار را روشن معرفی کند، سایت خدماتی مسیر ثبت درخواست را ساده کند و سایت کاتالوگی پیدا کردن محصولات را آسان کند. ساختار صفحه‌ها و امکانات را بر اساس کاری که مخاطب باید در سایت انجام دهد، طراحی می‌کنیم.',array(
   array(array('رابط کاربری','اختصاصی و هدفمند'),'منو، ترتیب محتوا و دکمه‌ها را بر اساس کارهای اصلی مخاطب طراحی می‌کنیم تا بتواند خدمت مناسب را پیدا کند، جزئیات را بخواند و درخواستش را ثبت کند.'),
   array(array('امکانات سفارشی','و توسعه‌پذیر'),'بسته به پروژه، امکاناتی مثل فرم چندمرحله‌ای، فیلتر محصولات یا پنل مدیریت درخواست‌ها را پیاده‌سازی می‌کنیم. اجزا را جدا می‌سازیم تا افزودن قابلیت‌های بعدی ساده‌تر باشد.'),
   array(array('تجربه کاربری','در همه دستگاه‌ها'),'خوانایی متن، دسترسی به منو و تکمیل فرم‌ها را در موبایل، تبلت و دسکتاپ بررسی می‌کنیم. کنترل‌های اصلی برای استفاده با لمس و صفحه‌کلید طراحی می‌شوند.'),
   array(array('مدیریت آسان','و ساختار بهینه'),'بخش‌هایی مثل معرفی خدمات و جزئیات محصولات را برای ویرایش تیم شما آماده می‌کنیم. ساختار نشانی صفحات، عنوان‌ها و بارگذاری تصاویر را هم در اجرای فنی در نظر می‌گیریم.')));
 [$title,$description,$cards]=service_copy()['mosaic']??$website;
 $items=array();
 foreach($cards as $index=>[$lines,$text]){
  [$image,$video]=$media[$index];
  $items[]=array('title'=>implode(' ',$lines),'title_lines'=>$lines,'desc'=>$text,'image'=>$image,'video'=>$video);
 }
 return array('label'=>'از ایده تا اجرا','title'=>$title,'desc'=>$description,'items'=>$items,'display_heading'=>true);
});
add_filter('radmanct/seo/description',static function(string $description):string {
 if(\RadmanCT\SEO\plain($description)!=='')return $description;
 if(is_front_page())return \RadmanCT\Home\data()['description'];
 if(is_page()&&\RadmanCT\PageTitle\is_used())return \RadmanCT\PageTitle\data()['desc'];
 return $description;
});
add_filter('radmanct/contact/data',static function(array $data):array {
 $number='+9809124707989';
 $data['channels']=array(array('label'=>__('Phone call','radmanct'),'url'=>'tel:'.$number,'value'=>$number,'icon'=>'phone'));
 return $data;
});
add_filter('radmanct/service/sticky-rows',static function():array {
    $rows=array(
        'label'=>'فناوری‌های توسعه',
        'title'=>'فناوری‌هایی برای توسعه وب‌سایت اختصاصی شما',
        'desc'=>'انتخاب فناوری به نوع محتوا، امکانات سایت و نحوهٔ نگهداری آن بستگی دارد. در وبتالیک از ابتدا مشخص می‌کنیم کدام بخش به رابط تعاملی، پردازش سمت سرور یا ارتباط با نرم‌افزارهای دیگر نیاز دارد.',
        'items'=>array(
            array('title'=>'React، Next.js و Nuxt.js','features'=>['طراحی اجزای قابل استفادهٔ مجدد','ساختار مسیرها و ناوبری','رندر صفحات بر اساس نوع محتوا','دریافت و نمایش داده‌ها','ساختار فنی مناسب جست‌وجو'],'title_ltr'=>true,'image'=>\RadmanCT\media_id('service-tech-frontend'),'subtitle'=>'توسعه فرانت‌اند و رابط کاربری تعاملی','desc'=>'با React و Vue.js، رابط‌های کاربری تعاملی و مبتنی بر کامپوننت توسعه می‌دهیم. در پروژه‌های مبتنی بر Next.js و Nuxt.js، ساختار مسیرها، شیوه رندر صفحات و دریافت داده‌ها را متناسب با نیازهای پروژه، عملکرد وب‌سایت و اصول سئوی فنی پیاده‌سازی می‌کنیم.'),
            array('title'=>'Python، Django، FastAPI و Laravel','features'=>['پیاده‌سازی منطق کسب‌وکار','طراحی ساختار داده و ذخیره‌سازی','احراز هویت و سطح دسترسی','اعتبارسنجی ورودی‌ها','ساخت سرویس و API سمت سرور'],'title_ltr'=>true,'image'=>\RadmanCT\media_id('service-tech-backend'),'subtitle'=>'برنامه‌نویسی بک‌اند و معماری سمت سرور','desc'=>'با Python و فریم‌ورک‌های Django و FastAPI و همچنین PHP و Laravel، بخش سمت سرور وب‌سایت‌ها و سامانه‌های اختصاصی را توسعه می‌دهیم. انتخاب فناوری بر اساس نیازهای پروژه، منطق کسب‌وکار، مدیریت داده‌ها، امنیت، سطح دسترسی کاربران و قابلیت توسعه در آینده انجام می‌شود.'),
            array('title'=>'API و یکپارچه‌سازی سیستم‌ها','features'=>['اتصال به نرم‌افزار مدیریت مشتریان','خواندن اطلاعات از سامانهٔ موجود','همگام‌سازی داده‌ بین سرویس‌ها','کنترل مجوزهای تبادل داده','مدیریت خطا و محدودیت درخواست‌ها'],'image'=>\RadmanCT\media_id('service-tech-api'),'subtitle'=>'اتصال سایت به نرم‌افزارهای دیگر','desc'=>'با API می‌توان درخواست‌های سایت را به نرم‌افزار مدیریت مشتریان فرستاد یا اطلاعات محصول را از سامانهٔ موجود خواند. پیش از اجرا، داده‌های قابل تبادل، مجوزهای دسترسی و محدودیت‌های هر سرویس را بررسی می‌کنیم.'),
        ),
    );
    return array_replace_recursive($rows,service_copy()['technology']??array());
});

add_filter('radmanct/service/topic-links',static function():array {
 $topics=service_copy()['topics']??null;
 if($topics)return array_map(static fn(string $url):string=>str_starts_with($url,'#')?$url:home_url($url),$topics);
 return array(
  'طراحی رابط کاربری سایت'=>'#rdc-svc-intro',
  'توسعه وب‌سایت با React و Next.js'=>'#rdc-svc-technologies',
  'مراحل طراحی سایت اختصاصی'=>'#rdc-svc-process',
  'طراحی و توسعه تخصصی وردپرس'=>home_url('/specialized-wordpress-design-development/'),
  'طراحی پنل مدیریت و سامانه‌های تحت وب'=>home_url('/admin-panel-web-systems/'),
 );
});
add_filter('radmanct/service/topic-label',static function(string $label):string {
 return service_copy()['topic_label']??$label;
});

add_filter('radmanct/service/case-copy',static function(array $copy):array {
 return service_copy()['case']??$copy;
});

add_filter('radmanct/services/faq',static function():array {
 $faq=service_copy()['faq']??null;
 if($faq)return $faq;
 if(is_page('custom-website-design-development')){
  $wordpress='<a href="'.esc_url(home_url('/specialized-wordpress-design-development/')).'">طراحی و توسعهٔ تخصصی وردپرس</a>';
  return array(
   array('چه زمانی کدنویسی اختصاصی برای سایت مناسب است؟','وقتی ساختار صفحه‌ها، قوانین کسب‌وکار یا ارتباط سایت با سامانه‌های دیگر به پیاده‌سازی سفارشی نیاز دارد. اگر امکانات وردپرس نیاز پروژه را پوشش می‌دهند، '.$wordpress.' هم قابل بررسی است.'),
   array('با چه فناوری‌هایی وب‌سایت اختصاصی توسعه می‌دهید؟','رابط وب را با Next.js و React می‌سازیم. ذخیرهٔ داده، حساب کاربری، مدیریت محتوای موردنیاز یا قواعد اختصاصی را بررسی می‌کنیم تا مشخص شود کدام بخش به بک‌اند و API نیاز دارد و Django یا FastAPI برای آن مناسب‌تر است.'),
   array('آیا می‌توان سایت را به پنل مدیریت یا نرم‌افزار فعلی متصل کرد؟','بله، در صورت دسترسی به API یا یک مسیر یکپارچه‌سازی مناسب. پیش از برآورد، نوع داده‌ها، مجوزهای دسترسی و محدودیت‌های سرویس فعلی بررسی می‌شوند.'),
   array('آیا سئو، امنیت و توسعهٔ آینده در ساخت سایت در نظر گرفته می‌شوند؟','ساختار عنوان‌ها، لینک‌های داخلی، متادیتا، دسترسی به محتوای عمومی و بارگذاری بررسی می‌شوند. اعتبارسنجی ورودی‌ها و کنترل دسترسی بر اساس قابلیت‌های پروژه طراحی می‌شوند. برای توسعهٔ آینده، نیازهای شناخته‌شده و بخش‌هایی را که ممکن است بعداً تغییر کنند از ابتدا مشخص می‌کنیم. دیده‌شدن در جست‌وجو به کیفیت محتوا و رقابت هم بستگی دارد.'),
   array('هزینه و زمان توسعه چطور برآورد می‌شود؟','تعداد الگوهای صفحه، پیچیدگی رابط، امکانات اختصاصی، اتصال به سرویس‌ها و آماده بودن محتوا بر برآورد اثر دارند. پس از بررسی نیازها، محدودهٔ کار و مرحله‌های تحویل مشخص می‌شوند.'),
  );
 }
 return array(
  array(__('What are the steps in a design project?','radmanct'),__('We start by understanding your goals, then plan the structure, design the interface, review your feedback, and prepare the project for launch.','radmanct')),
  array(__('How long will my project take?','radmanct'),__('The timeline depends on the scope and complexity. After the initial review, we agree on the milestones and delivery dates with you.','radmanct')),
  array(__('Can a startup work with you?','radmanct'),__('Yes. We work with new teams and established businesses, and shape the project around their current needs and goals.','radmanct')),
  array(__('Can you build a custom solution?','radmanct'),__('Yes. We review your requirements and recommend an approach for your custom website, web application, or mobile app.','radmanct')),
 );
});
add_filter('radmanct/services/marquee',static function():array {
 $labels=array('عکاسی','مدل‌سازی سه‌بعدی','توسعه','تصویرسازی','مد و استایل','هنر دیجیتال','بسته‌بندی','موشن','تولید ویدئو');
 $images=array();foreach($labels as $index=>$label)$images[]=array('id'=>\RadmanCT\media_id('services-marquee-'.($index+1)),'label'=>$label);
 return array('title'=>array('دربارهٔ پروژه‌ات','گفت‌وگو کنیم'),'url'=>\RadmanCT\Footer\data()['contact'],'images'=>$images);
});
add_filter('radmanct/services/strip',static function():array {
 return array_map(static fn(int $number):int=>\RadmanCT\media_id('services-strip-'.$number),[48,45,46,47,49]);
});
// Current customer's editorial data. Replace this file for the next project;
// shared renderers, assets and animation code remain independent of this content.
add_filter('body_class',static function(array $classes):array {
 if(is_page('services')||\RadmanCT\Services\is_detail())$classes[]='rdc-inv';
 return $classes;
});
add_filter('radmanct/breadcrumb/section-page',static function(string $slug):string {
 if(\RadmanCT\Services\is_detail())return 'services';
 if(is_page(['custom-real-estate-platform','custom-e-commerce-platform','custom-corporate-website']))return 'products';
 return $slug;
});
add_filter('radmanct/page-title/enabled',static function(bool $enabled):bool {
 $services=array_column(apply_filters('radmanct/services/specs',array()),'slug');
 return $enabled&&is_page(array_merge(array('services','contact-us','about-us'),$services));
});
add_filter('radmanct/page-title/data',static function(array $data):array {
 if(!$data['image'])$data['image']=\RadmanCT\media_id('hero');
 $data['media_inset']=is_page('services');
 if(is_page('services'))$data['art_tracks']=array(
  array('M739.5 215 L765 187.25 H777 V383',18,.62),
  array('M60 186.25 H111.75 V255.5',20,.3),
  array('M78 584 V718.25 H262.5',19,.55),
  array('M682.5 718.25 H915',24,.28)
 );
 $data['label']=__('What we build','radmanct');
 $description=__('Thoughtful design, reliable development, and room for your business to grow.','radmanct');
 $data['button']=array('label'=>__('Start a project','radmanct'),'url'=>\RadmanCT\Footer\data()['contact']);
 foreach(apply_filters('radmanct/services/specs',array()) as $service){
  if(is_page($service['slug'])){$description=$service['description'];break;}
 }
 if(is_page('custom-website-design-development')){
  $data['subheading']='وب‌سایتی فراتر از یک قالب آماده، متناسب با اهداف کسب‌وکار شما';
  $data['desc']='در وبتالیک، وب‌سایت شما بر اساس نیازهای واقعی کسب‌وکارتان طراحی و توسعه داده می‌شود. از طراحی رابط کاربری و تجربه کاربری تا برنامه‌نویسی اختصاصی، بهینه‌سازی فنی و پیاده‌سازی امکانات موردنیاز، با تمرکز بر عملکرد، توسعه‌پذیری و تجربه‌ای حرفه‌ای برای کاربران.';
  $data['button']['label']='مشاوره طراحی سایت';
  $data['link']=array(
   'label'=>'مشاهده نمونه‌کارها',
   'url'=>get_post_type_archive_link('project')?:'',
   'variant'=>'outline',
  );
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_draw']=true;
  $data['art_tracks']=array(
   array('M35 510 A420 350 0 0 1 455 160 A420 350 0 0 1 875 510',32,.3),
   array('M875 510 A420 350 0 0 1 455 860',28,.65),
   array('M455 860 A420 350 0 0 1 35 510',30,.4)
  );
  $data['art_nodes']=array(array(35,510),array(875,510),array(455,860));
  $data['art_matrix']=array(91.87,760);
 }elseif(is_page('specialized-wordpress-design-development')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_tracks']=array(
   array('M241.76 208.16 C343.47 114.23 512.49 98.29 626.67 188.30',30,.3),
   array('M81.89 346.65 V396.89',22,.6),
   array('M833.84 657.31 C822.75 679.79 809.61 702.32 791.70 721.14',26,.35)
  );
  $data['art_nodes']=array(array(155.74,134.64),array(155.74,241.15),array(796.79,228.09),array(158.76,693.3),array(848.04,615.93),array(781.72,713.4));
 }elseif(is_page('admin-panel-web-systems')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_tracks']=array(
   array('M211.60 242.53 C315.16 103.67 521.27 95.20 642.68 202.51',30,.3),
   array('M738.22 728.14 C708.43 762.80 669.31 788.01 627.94 806.81',26,.6),
   array('M411.02 832.12 C325.24 815.18 251.99 767.03 197.12 704.95',32,.4)
  );
  $data['art_nodes']=array(array(145,183),array(329,183),array(855,205),array(774,696),array(145,770),array(773,770));
 }elseif(is_page('mobile-app-design-development')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_tracks']=array(
   array('M366.42 103.71 C433.28 84.61 509.44 92.93 569.15 129.74',30,.35),
   array('M756.00 704.49 C726.13 754.90 681.87 793.92 631.17 821.13',28,.6),
   array('M270.85 151.71 C220.23 205.23 160.68 252.34 131.58 322.39',32,.4)
  );
  $data['art_nodes']=array(array(337,114),array(618,166),array(823,222),array(98,417),array(777,663),array(242,739));
 }elseif(is_page('about-us')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_nodes']=array(array(328.56,123.59),array(643.06,188.9),array(789.76,557.65),array(188.9,750.57),array(89.43,276.32),array(89.43,368.76));
  $data['art_tracks']=array(
   array('M220.05 180.86 L199.95 200.96 L181.87 221.05 L167.8 241.15 L155.74 261.24 L145.69 281.34 L136.65 301.44',28,.35),
   array('M790.77 462.2 C794.79 484.31 794.79 506.41 792.78 527.51',24,.65),
   array('M192.92 643.06 C241.15 706.36 301.44 748.57 361.72 757.61',30,.3)
  );
  $data['label']=__('Who we are','radmanct');
  $description=__('A focused team for thoughtful design and reliable development.','radmanct');
 }elseif(is_page('contact-us')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_nodes']=array(array(91.87,479.75),array(737.22,274.3),array(937.03,702.44),array(893.4,812.58));
  $data['art_tracks']=array(
   array('M153.88 479.75 C153.88 296.11 315.79 146.91 516.75 146.91 C671.77 146.91 810.72 239.87 872.73 376.45',28,.12),
   array('M179.14 195.11 V596.81',24,.55),
   array('M838.28 269.71 H1056.46',20,.3),
   array('M183.73 789.63 V895.22',22,.72)
  );
  $data['label']=__('Let us talk','radmanct');
  $description=__('Let’s talk about your next project.','radmanct');
  $data['button']=array('label'=>__('Explore our services','radmanct'),'url'=>home_url('/services/'));
 }
 $hero=service_copy()['hero']??null;
 if($hero){
  $data['subheading']=$hero['subheading'];
  $data['desc']=$hero['desc'];
  $data['button']['label']=$hero['cta'];
  $data['link']=array('label'=>'مشاهده نمونه‌کارها','url'=>get_post_type_archive_link('project')?:'','variant'=>'outline');
 }
 if($data['desc']==='')$data['desc']=$description;
 return $data;
});
add_filter('radmanct/home/data',static function(array $data):array {
 return array_replace($data,array(
  'heading'=>__('Webtalik','radmanct'),
  'subheading'=>__('Website, web application and mobile app design and development','radmanct'),
  'accent'=>implode("\n",array(
   __('Custom website design and development','radmanct'),
   __('Specialist WordPress design and development','radmanct'),
   __('Management panels and web applications','radmanct'),
   __('Mobile application design and development','radmanct'),
  )),
  'description'=>__('At Webtalik, we turn ideas into websites, advanced systems and practical applications through modern design and custom programming.','radmanct'),
  'button_url'=>\RadmanCT\Footer\data()['contact'],'button_label'=>__('Start a project','radmanct'),
 ));
});
add_filter('radmanct/services/specs',static function(array $items):array {
 return array(
        array('slug'=>'custom-website-design-development','title'=>__('Custom website development','radmanct'),'description'=>__('Fast, tailored websites designed around your brand and business needs.','radmanct'),'media'=>'services-web'),
        array('slug'=>'specialized-wordpress-design-development','title'=>__('Specialized WordPress development','radmanct'),'description'=>__('Custom WordPress design, development and optimization with a maintainable architecture.','radmanct'),'media'=>'services-wordpress'),
        array('slug'=>'admin-panel-web-systems','title'=>__('Admin panels and web systems','radmanct'),'description'=>__('Purpose-built dashboards and connected systems that simplify your daily workflows.','radmanct'),'media'=>'services-systems'),
        array('slug'=>'mobile-app-design-development','title'=>__('Mobile application development','radmanct'),'description'=>__('Thoughtful mobile experiences with reliable performance and integrated services.','radmanct'),'media'=>'services-apps'),
    );
});
add_filter('radmanct/home/selected-work',static function(array $items):array {
 return array(
  array('image'=>25,'title'=>__('Skills learning website','radmanct'),'description'=>__('A WordPress website for educational content and courses.','radmanct'),'categories'=>array(__('Educational website','radmanct'))),
  array('image'=>24,'title'=>__('Sepehr real estate website','radmanct'),'description'=>__('A real estate website for services and property listings.','radmanct'),'categories'=>array(__('Real estate website','radmanct'))),
  array('image'=>23,'title'=>__('Afagh corporate website','radmanct'),'description'=>__('A corporate website with clear content and services.','radmanct'),'categories'=>array(__('Corporate website','radmanct'))),
 );
});
add_filter('radmanct/home/offerings',static function(array $items):array {
 return array(
	array( 'web', __( 'Custom website design', 'radmanct' ), __( 'Tailored', 'radmanct' ), __( 'We design and develop modern, fast websites that fit your brand. From the first idea to launch, we are with you.', 'radmanct' ), array( __( 'Security and stability', 'radmanct' ), __( 'Responsive design', 'radmanct' ), __( 'Speed and performance', 'radmanct' ) ) ),
	array( 'wordpress', __( 'WordPress design and development', 'radmanct' ), __( 'WordPress websites', 'radmanct' ), __( 'Professional WordPress solutions: custom themes and plugins, easy content management, performance and security built for growth.', 'radmanct' ), array( __( 'Performance and scalability', 'radmanct' ), __( 'Custom development', 'radmanct' ), __( 'Multisite network', 'radmanct' ) ) ),
	array( 'systems', __( 'Management panel design', 'radmanct' ), __( 'and web applications', 'radmanct' ), __( 'Custom management panels and powerful web applications, built around your business with a scalable, modular architecture.', 'radmanct' ), array( __( 'Security and access control', 'radmanct' ), __( 'APIs and integrations', 'radmanct' ), __( 'Scalable architecture', 'radmanct' ) ) ),
	array( 'apps', __( 'Application design and development', 'radmanct' ), __( 'Mobile applications', 'radmanct' ), __( 'Custom Android and iOS applications with a smooth user experience, reliable performance and seamless connections to your business systems.', 'radmanct' ), array( __( 'Android and iOS', 'radmanct' ), __( 'User experience', 'radmanct' ), __( 'APIs and integrations', 'radmanct' ) ) ),
);
});
add_filter('radmanct/footer/data',static function(array $data):array {
 $messenger_phone='+989124707989';
 $messenger_number=ltrim($messenger_phone,'+');
 return array_replace($data,array(
  'messenger_phone'=>$messenger_phone,
  'address'=>__('Based in Iran, working with businesses everywhere.','radmanct'),
  'hours'=>__('Saturday to Thursday · 9:00–18:00','radmanct'),
  'socials'=>array(
   array('title'=>__('Instagram','radmanct'),'icon'=>'instagram','url'=>'https://www.instagram.com/radmanct/'),
   array('title'=>__('LinkedIn','radmanct'),'icon'=>'linkedin','url'=>''),
   array('title'=>__('Telegram','radmanct'),'icon'=>'telegram','url'=>'https://t.me/+'.$messenger_number),
   array('title'=>__('WhatsApp','radmanct'),'icon'=>'whatsapp','url'=>'https://wa.me/'.$messenger_number),
  ),
 ));
});

add_filter('radmanct/compact-title/data',static function(array $data):array {
 if(is_post_type_archive('project')){
  $data['label']='';
  $data['title']='نمونه‌کارها';
  $data['accent']='';
  $data['desc']='مجموعه‌ای از پروژه‌های منتخب طراحی و توسعهٔ وب‌سایت، اپلیکیشن و سامانه‌های اختصاصی.';
  $data['button']=[];
 }
 elseif(is_home()){$data['label']='یادداشت‌ها و دیدگاه‌ها';$data['desc']='دربارهٔ طراحی، توسعه و ساخت تجربه‌های بهتر در وب.';}
 elseif(is_search()){$data['label']='جستجو در وبتالیک';}
 return $data;
});
