<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
add_filter('radmanct/service/mosaic',static function(array $data):array {
 $url=home_url('/contact-us/');
 $copy=array(
  array('طراحی با هویت برند شما','از ساختار صفحه‌ها تا جزئیات رابط کاربری؛ تجربه‌ای روشن و منسجم، متناسب با مخاطب و شخصیت کسب‌وکارتان.',\RadmanCT\media_id('services-marquee-1'),0),
  array('توسعهٔ اختصاصی و منعطف','سایت بر اساس نیاز واقعی شما ساخته می‌شود؛ با ساختاری قابل نگهداری و فضایی برای توسعهٔ قابلیت‌های آینده.',\RadmanCT\media_id('services-marquee-2'),0),
  array('تجربهٔ واقعی در اجرا','نمونه‌ای از اجرای یک تجربهٔ دیجیتال؛ از ایده و طراحی تا محصولی که روی دستگاه‌های مختلف قابل استفاده است.',23,92),
  array('جزئیاتی برای یک تجربهٔ بهتر','خوانایی، دسترسی‌پذیری و نمایش درست در موبایل، بخشی از طراحی و اجرای هر صفحه‌اند.',\RadmanCT\media_id('services-marquee-3'),0),
 );
 $items=array();foreach($copy as $row)$items[]=array('title'=>$row[0],'desc'=>$row[1],'image'=>$row[2],'video'=>$row[3],'link'=>'دربارهٔ پروژه‌ات بگو','url'=>$url);
 return array('label'=>'از ایده تا اجرا','title'=>'وب‌سایتی که برای کسب‌وکار شما ساخته می‌شود','items'=>$items);
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
add_filter('radmanct/services/faq',static function():array {
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
// Current customer's editorial data. Replace this file for the next project;
// shared renderers, assets and animation code remain independent of this content.
add_filter('body_class',static function(array $classes):array {
 if(is_page(array('services','custom-website-design-development')))$classes[]='rdc-inv';
 return $classes;
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
