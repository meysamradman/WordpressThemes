<?php
namespace RadmanCT\Footer;
defined('ABSPATH')||exit;
function data():array {
    static $data;
    if(null!==$data)return $data;
    $pages=get_posts(array('post_type'=>'page','post_status'=>'publish','has_password'=>false,'post_name__in'=>array('about-us','services','contact-us'),'posts_per_page'=>3,'no_found_rows'=>true,'update_post_meta_cache'=>false,'update_post_term_cache'=>false));
    $by_slug=array();foreach($pages as $page)$by_slug[$page->post_name]=$page;
    $contact=isset($by_slug['contact-us'])?get_permalink($by_slug['contact-us']):'';
    $links=array(array('title'=>__('Home','radmanct'),'url'=>home_url('/')));
    foreach(array('about-us','services') as $slug)if(isset($by_slug[$slug]))$links[]=array('title'=>$by_slug[$slug]->post_title,'url'=>get_permalink($by_slug[$slug]));
    $links[]=array('title'=>__('Projects','radmanct'),'url'=>get_post_type_archive_link('project'));
    $blog=(int)get_option('page_for_posts');if($blog&&'publish'===get_post_status($blog))$links[]=array('title'=>__('Blog','radmanct'),'url'=>get_permalink($blog));
    if($contact)$links[]=array('title'=>__('Contact us','radmanct'),'url'=>$contact);
    return $data=apply_filters('radmanct/footer/data',array(
        'links'=>$links,'services'=>\RadmanCT\Services\items(),'contact'=>$contact,'phone'=>'','messenger_phone'=>'','email'=>'',
        'address'=>'','hours'=>'','socials'=>array(),
    ));
}
