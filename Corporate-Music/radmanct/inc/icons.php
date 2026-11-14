<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function icon(string $name,array $options=array()):string {
 static $social=[];
 $social_names=array('telegram','messenger','instagram','website','rubika','aparat');
 if(str_starts_with($name,'social-')&&in_array(substr($name,7),$social_names,true)){
  if(!isset($social[$name])){
   $file=dirname(__DIR__).'/images/icons/social/'.substr($name,7).'.svg';$source=is_readable($file)?file_get_contents($file):'';
   $social[$name]=preg_match('~<svg\b[^>]*>(.*)</svg>~s',$source,$match)?$match[1]:'';
  }
  return $social[$name]?'<svg class="rdc-ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'.$social[$name].'</svg>':'';
 }
 static $catalog;if(null===$catalog)$catalog=require __DIR__.'/icons/catalog.php';
 if(!isset($catalog[$name]))return '';
 $stroke=1.5;
 $direction=in_array($name,array('arrow-right','arrow-up-right'),true)?' data-directional="true"':'';
 return '<svg class="rdc-ico"'.$direction.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="'.esc_attr((string)$stroke).'" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.$catalog[$name].'</svg>';
}
