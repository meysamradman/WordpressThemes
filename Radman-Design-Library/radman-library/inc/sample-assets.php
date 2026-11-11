<?php
namespace RadmanLibrary;
defined('ABSPATH')||exit;
require_once __DIR__.'/samples.php';
foreach(['assets','icons','ui/button','page-title','home','brand','navigation'] as $part)require_once get_template_directory().'/shared/radmanct/inc/'.$part.'.php';
add_filter('radmanct/home/data',static function(array $data):array{
    $data=array_replace($data,Samples\fixture()['home']??[]);$data['button_url']=Samples\link('services-list');return $data;
});
add_filter('radmanct/home/offerings',static fn()=>Samples\fixture()['offerings']??[]);
add_filter('radmanct/home/selected-work',static fn()=>Samples\fixture()['projects']??[]);
add_filter('radmanct/services/strip',static function():array{
    return array_map(static fn($n)=>\RadmanCT\media_id('services-strip-'.$n),[48,45,46,47,49]);
});
add_action('after_setup_theme',static function(){load_textdomain('radmanct',get_template_directory().'/shared/radmanct/languages/fa_IR.mo');});
add_filter('template_include',static function(string $template):string{
    return Samples\raw()?get_template_directory().'/preview.php':$template;
});
add_filter('show_admin_bar',static fn(bool $show):bool=>Samples\raw()?false:$show);
add_action('wp_enqueue_scripts',static function():void{
    if(!Samples\raw())return;
    $module=Samples\selected();
    $base=\RadmanCT\Assets\entry('src/css/sample-base.css');
    if($base)wp_enqueue_style('rdl-sample-base',\RadmanCT\Assets\url($base['file']),[],null);
    $asset=\RadmanCT\Assets\entry('modules/'.$module['slug'].'/style.css');
    if($asset)wp_enqueue_style('rdl-design',\RadmanCT\Assets\url($asset['file']),['rdl-sample-base'],null);
    $script=\RadmanCT\Assets\entry('modules/'.$module['slug'].'/script.js');
    if($script)wp_enqueue_script_module('rdl-design',\RadmanCT\Assets\url($script['file']),\RadmanCT\Assets\module_dependencies($script),null);
});
