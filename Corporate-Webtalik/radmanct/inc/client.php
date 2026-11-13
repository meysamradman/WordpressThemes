<?php
namespace RadmanCT\Client;
defined('ABSPATH')||exit;
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
  array('M0 187.25 H111.75 V255.5',20,.3),
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
   array('M242.16 209 C310 144 390 124 454 129 C521 131 578 152 622 193',30,.3),
   array('M82.39 346.65 V396.89',22,.6),
   array('M830 655 C817 677 801 697 784 713',26,.35)
  );
  $data['art_nodes']=array(array(155.74,134.64),array(155.74,241.15),array(796.79,228.09),array(158.76,693.3),array(848.04,615.93),array(781.72,713.4));
 }elseif(is_page('admin-panel-web-systems')){
  $data['media_inset']=true;
  $data['media_flat']=true;
  $data['art_tracks']=array(
   array('M214.02 240.14 C272.3 177.85 359.71 130.62 450.14 130.62 C523.49 130.62 591.82 157.75 644.07 204.98',30,.3),
   array('M748.56 738.52 C713.4 773.68 673.21 799.81 633.01 815.89',26,.6),
   array('M411.96 840 C321.53 823.92 241.15 781.72 190.91 713.4',32,.4)
  );
  $data['art_nodes']=array(array(145.69,183.88),array(330.57,183.88),array(859.09,205.98),array(777.7,699.33),array(145.69,773.68),array(776.7,773.68));
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
