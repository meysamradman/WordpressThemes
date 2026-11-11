<?php
namespace RadmanLibrary\Samples {
    defined('ABSPATH')||exit;
    function fixture():array { static $data;return $data??=get_option('rdl_sample_data',[]); }
    function link(string $slug):string {
        static $links=[];
        if(!isset($links[$slug])){$post=get_page_by_path($slug,OBJECT,'rdl_design');$links[$slug]=$post?get_permalink($post):home_url('/');}
        return $links[$slug];
    }
    function selected():?array {
        if(\RadmanLibrary\Catalog\native())return \RadmanLibrary\modules()[is_singular('radman_product')?'catalog-single':'catalog-archive']??null;
        if(\RadmanLibrary\Music\native())return \RadmanLibrary\modules()[is_singular('track')?'music-single':'music-archive']??null;
        if(\RadmanLibrary\Blog\native())return \RadmanLibrary\modules()[is_singular('post')?'blog-single':'blog-archive']??null;
        if(is_post_type_archive('project')||is_tax('project_category'))return \RadmanLibrary\modules()['portfolio-archive']??null;
        if(is_singular('project'))return \RadmanLibrary\modules()['portfolio-single']??null;
        return is_singular('rdl_design')?\RadmanLibrary\module(get_queried_object_id()):null;
    }
    function raw():bool { return (!isset($_GET['rdl_info'])||'1'!==$_GET['rdl_info'])&&(\RadmanLibrary\Portfolio\native()||\RadmanLibrary\Blog\native()||\RadmanLibrary\Music\native()||\RadmanLibrary\Catalog\native()||!post_password_required())&&isset(selected()['source']); }
    function cover_data():array {
        return \RadmanCT\PageTitle\data([
            'title'=>'خدمات','label'=>'آنچه می‌سازیم','desc'=>'طراحی فکرشده، توسعهٔ قابل اتکا و فضایی برای رشد کسب‌وکارت.',
            'image'=>\RadmanCT\media_id('hero'),'video'=>0,
            'button'=>['label'=>'شروع همکاری','url'=>link('services-list')],
            'trail'=>[['title'=>'کتابخانه','url'=>home_url('/')],['title'=>'خدمات','url'=>'']],
        ]);
    }
}
namespace RadmanCT {
    function media_id(string $name):int {static $layout;$layout??=get_option('rdl_layout_media',[]);return (int)($layout[$name]??\RadmanLibrary\Samples\fixture()['map'][$name]??0); }
    function page_heading():string {
        if((\RadmanLibrary\Blog\native()||\RadmanLibrary\Blog\rendering())&&!is_singular('post'))return is_category()?single_cat_title('',false):__('Blog','radmanct');
        if(is_post_type_archive('project'))return __('Projects','radmanct');
        if(is_tax('project_category'))return single_term_title('',false);
        return get_the_title();
    }
}
namespace RadmanCT\Footer {
    function data():array {
        return [
            'contact'=>\RadmanLibrary\Samples\link('services-list'),
            'links'=>[['title'=>'صفحهٔ اول','url'=>\RadmanLibrary\Samples\link('home-hero')],['title'=>'خدمات','url'=>\RadmanLibrary\Samples\link('services-list')],['title'=>'نمونه‌کارها','url'=>get_post_type_archive_link('project')?:home_url('/')]],
            'services'=>\RadmanCT\Services\items(),'phone'=>'','messenger_phone'=>'','email'=>'',
            'address'=>'اطلاعات نمونه؛ قابل جایگزینی برای مشتری','hours'=>'شنبه تا پنجشنبه، ۹ تا ۱۸',
            'socials'=>[['title'=>'اینستاگرام','icon'=>'instagram','url'=>''],['title'=>'لینکدین','icon'=>'linkedin','url'=>''],['title'=>'تلگرام','icon'=>'telegram','url'=>''],['title'=>'واتس‌اپ','icon'=>'whatsapp','url'=>'']],
        ];
    }
}
namespace RadmanCT\Services {
    function items():array {
        return array_map(static function(array $item):array{$item['url']=\RadmanLibrary\Samples\link('home-services');return $item;},\RadmanLibrary\Samples\fixture()['services']??[]);
    }
}
