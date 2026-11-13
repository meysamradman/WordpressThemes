<?php
namespace RadmanLibrary\Preview;
defined('ABSPATH')||exit;

/** Workshop-only viewport controls; never part of a selected customer module. */
function devices():array {
 return array(
  'desktop'=>array('label'=>'آزاد','icon'=>'monitor','width'=>0,'height'=>0),
  'laptop'=>array('label'=>'لپ‌تاپ','icon'=>'laptop','width'=>1440,'height'=>900),
  'tablet'=>array('label'=>'تبلت','icon'=>'tablet','width'=>768,'height'=>1024),
  'mobile'=>array('label'=>'موبایل','icon'=>'smartphone','width'=>390,'height'=>844),
 );
}
function canvas():bool {
 return (isset($_GET['rdl_canvas'])&&is_string($_GET['rdl_canvas'])&&$_GET['rdl_canvas']==='1')||($_SERVER['HTTP_SEC_FETCH_DEST']??'')==='iframe';
}
function mode():string {
 $mode=isset($_GET['rdl_view'])&&is_string($_GET['rdl_view'])?sanitize_key(wp_unslash($_GET['rdl_view'])):'desktop';
 return !canvas()&&isset(devices()[$mode])?$mode:'desktop';
}
function framed():bool {return mode()!=='desktop';}
function url(string $mode='desktop',bool $canvas=false):string {
 $path=(string)wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']??'/'),PHP_URL_PATH);
 $url=is_singular()?get_permalink():home_url('/'.ltrim($path,'/'));
 $query=array();
 foreach(array('rdl_dir','rdl_layout','paged','page') as $key)if(isset($_GET[$key])&&is_scalar($_GET[$key]))$query[$key]=sanitize_text_field(wp_unslash((string)$_GET[$key]));
 if($canvas)$query['rdl_canvas']='1';
 elseif($mode!=='desktop'&&isset(devices()[$mode]))$query['rdl_view']=$mode;
 return add_query_arg($query,$url);
}
