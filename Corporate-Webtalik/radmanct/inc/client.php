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
add_filter('radmanct/services/strip',static function():array {
 return array_map(static fn(int $number):int=>\RadmanCT\media_id('services-strip-'.$number),array(48,45,46,47,49));
});
// Current customer's editorial data. Replace this file for the next project;
// shared renderers, assets and animation code remain independent of this content.
add_filter('body_class',static function(array $classes):array {
 if(is_page('services'))$classes[]='rdc-inv';
 return $classes;
});
add_filter('radmanct/page-title/enabled',static function(bool $enabled):bool {
 $services=array_column(apply_filters('radmanct/services/specs',array()),'slug');
 return $enabled&&is_page(array_merge(array('services','contact-us','about-us'),$services));
});
add_filter('radmanct/page-title/data',static function(array $data):array {
 if(!$data['image'])$data['image']=\RadmanCT\media_id('hero');
 $data['label']=__('What we build','radmanct');
 $description=__('Thoughtful design, reliable development, and room for your business to grow.','radmanct');
 $data['button']=array('label'=>__('Start a project','radmanct'),'url'=>\RadmanCT\Footer\data()['contact']);
 foreach(apply_filters('radmanct/services/specs',array()) as $service){
  if(is_page($service['slug'])){$description=$service['description'];break;}
 }
 if(is_page('about-us')){
  $data['label']=__('Who we are','radmanct');
  $description=__('A focused team for thoughtful design and reliable development.','radmanct');
 }elseif(is_page('contact-us')){
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
        array('slug'=>'custom-website-design-development','title'=>__('Custom website development','radmanct'),'description'=>__('Fast, tailored websites designed around your brand and business needs.','radmanct'),'media'=>'offering-web'),
        array('slug'=>'specialized-wordpress-design-development','title'=>__('Specialized WordPress development','radmanct'),'description'=>__('Custom WordPress design, development and optimization with a maintainable architecture.','radmanct'),'media'=>'offering-wordpress'),
        array('slug'=>'admin-panel-web-systems','title'=>__('Admin panels and web systems','radmanct'),'description'=>__('Purpose-built dashboards and connected systems that simplify your daily workflows.','radmanct'),'media'=>'offering-systems'),
        array('slug'=>'mobile-app-design-development','title'=>__('Mobile application development','radmanct'),'description'=>__('Thoughtful mobile experiences with reliable performance and integrated services.','radmanct'),'media'=>'offering-apps'),
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
