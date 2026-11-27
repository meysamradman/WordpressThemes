<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function brand_svg(string $source,bool $motion=true):string {
 if(strlen($source)>32768||preg_match('/<!DOCTYPE|<!ENTITY/i',$source))return '';
 $xml=new \DOMDocument();$previous=libxml_use_internal_errors(true);
 try{$valid=$xml->loadXML($source,LIBXML_NONET);}finally{libxml_clear_errors();libxml_use_internal_errors($previous);}
 if(!$valid||$xml->documentElement->localName!=='svg')return '';
 $tags=array('svg','defs','path','mask','clipPath','linearGradient','radialGradient','stop','use');
 $attributes=array('xmlns','width','height','viewBox','id','d','fill','stroke','stroke-opacity','stroke-width','stroke-linejoin','stroke-linecap','pathLength','mask','maskUnits','clip-path','x','y','href','gradientUnits','gradientTransform','x1','x2','y1','y2','cx','cy','r','offset','stop-color','stop-opacity','transform','fill-rule','data-rdc-logo-kind','data-rdc-logo-part','data-rdc-logo-tone','data-rdc-logo-write','data-rdc-logo-step');
 $prefix=wp_unique_id('rdc-logo-');$ids=array();
 foreach($xml->getElementsByTagName('*') as $node){
  if($node->namespaceURI!=='http://www.w3.org/2000/svg'||!in_array($node->tagName,$tags,true))return '';
  foreach($node->attributes as $attribute){
   $name=$attribute->nodeName;$value=$attribute->nodeValue;
   if(!in_array($name,$attributes,true))return '';
   if($name==='transform'&&!preg_match('/^translate\(-?\d+(?:\.\d+)?(?:[, ]+-?\d+(?:\.\d+)?)?\)$/D',$value))return '';
   if($name==='fill-rule'&&!in_array($value,array('evenodd','nonzero'),true))return '';
   if($name==='data-rdc-logo-kind'&&$value!=='personal')return '';
   if($name==='data-rdc-logo-part'&&!in_array($value,array('foil','ink','body','word','arc-left','arc-right','dot','sign'),true))return '';
   if($name==='data-rdc-logo-write'&&!in_array($value,array('body','word','arc-left','arc-right','dot','sign'),true))return '';
   if($name==='data-rdc-logo-step'&&!preg_match('/^(?:[1-9]|1[0-9])$/D',$value))return '';
   if(in_array($name,array('mask','clip-path'),true)&&!preg_match('/^url\(#[a-zA-Z][a-zA-Z0-9_-]*\)$/D',$value))return '';
   if($name==='maskUnits'&&$value!=='userSpaceOnUse')return '';
   if($name==='pathLength'&&$value!=='1')return '';
   if($name==='stroke-linecap'&&!in_array($value,array('round','butt','square'),true))return '';
   if($name==='data-rdc-logo-tone'&&!in_array($value,array('gold','light'),true))return '';

   if($name==='href'&&!preg_match('/^#[a-zA-Z][a-zA-Z0-9_-]*$/D',$value))return '';
   if(in_array($name,array('fill','stroke','stop-color'),true)&&!preg_match('/^(none|#[a-fA-F0-9]{3,8}|url\(#[a-zA-Z][a-zA-Z0-9_-]*\))$/D',$value))return '';
   if($name==='id')$ids[$value]=$prefix.$value;
  }
 }
 foreach($xml->getElementsByTagName('*') as $node){
  foreach($node->attributes as $attribute){
   if($attribute->nodeName==='id')$attribute->nodeValue=$ids[$attribute->nodeValue];
   elseif(in_array($attribute->nodeName,array('href','fill','stroke','mask','clip-path'),true))$attribute->nodeValue=strtr($attribute->nodeValue,array_combine(array_map(static fn($id)=>'#'.$id,array_keys($ids)),array_map(static fn($id)=>'#'.$id,array_values($ids))));
  }
 }
 $root=$xml->documentElement;$root->setAttribute('class','rdc-logo');if($motion)$root->setAttribute('data-rdc-logo','');$root->setAttribute('aria-hidden','true');$root->setAttribute('focusable','false');
 foreach($root->childNodes as $node){
  if(!$node instanceof \DOMElement)continue;
  if($root->getAttribute('data-rdc-logo-kind')==='personal')continue;
  if($node->tagName==='path')$node->setAttribute('data-rdc-logo-dmnd','');
  elseif($node->tagName==='use'&&preg_match('/-(left|right)$/D',$node->getAttribute('href'),$match))$node->setAttribute('data-rdc-logo-rbn',$match[1]);
 }
 return $xml->saveXML($root);
}
function brand_logo(int $id,bool $motion=true):string {
 $file=get_attached_file($id);$uploads=wp_upload_dir(null,false);$path=$file?realpath($file):false;$base=realpath($uploads['basedir']);
 if($path&&$base&&str_starts_with(wp_normalize_path($path),trailingslashit(wp_normalize_path($base)))&&get_post_mime_type($id)==='image/svg+xml'&&is_readable($path)&&filesize($path)<=32768){
  $svg=brand_svg(file_get_contents($path),$motion);if($svg)return $svg;
 }
 return wp_get_attachment_image($id,'medium',false,array('alt'=>get_bloginfo('name'),'decoding'=>'async'));
}
