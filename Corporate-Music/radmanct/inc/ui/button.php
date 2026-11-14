<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function button(string $label,string $url,array $args=array()):string {
 // All variants share the same markup and motion; solid preserves existing calls.
 $variant=$args['variant']??'solid';
 $variant=in_array($variant,array('solid','outline','ghost'),true)?$variant:'solid';
 $classes='rdc-btn';
 if($variant!=='solid')$classes.=' rdc-btn--'.$variant;
 foreach(preg_split('/\s+/',trim($args['class']??''))as$class)if($class!=='')$classes.=' '.sanitize_html_class($class);
 $attributes=!empty($args['download'])?' download':'';
 foreach(array('data-rdc-load-more','data-rdc-pnl-rvl','data-rdc-en')as$key)if(!empty($args[$key]))$attributes.=' '.$key;
 $icon=icon($args['icon']??'arrow-up-right');
 if($icon==='')$icon=icon('arrow-up-right');
 $label_html='<span class="rdc-btn__lbl"><span>'.esc_html($label).'</span><span aria-hidden="true">'.esc_html($label).'</span></span>';
 $icon_html='<span class="rdc-btn__ico" aria-hidden="true">'.$icon.$icon.'</span>';
 $content=($args['icon_position']??'end')==='start'?$icon_html.$label_html:$label_html.$icon_html;
 if(!empty($args['audio'])&&is_array($args['audio'])){
  $audio=$args['audio'];return '<button type="button" class="'.esc_attr($classes).'" data-rdc-audio-key="'.(int)($audio['key']??0).'" data-rdc-audio-src="'.esc_url($audio['url']??'').'" aria-pressed="false" aria-label="'.esc_attr($label).'">'.$content.'</button>';
 }
 return '<a class="'.esc_attr($classes).'" href="'.esc_url($url).'"'.$attributes.'>'.$content.'</a>';
}
