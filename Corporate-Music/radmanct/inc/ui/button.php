<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function button(string $label,string $url,array $args=array()):string {
 // All variants share the same markup and motion; solid preserves existing calls.
 $variant=$args['variant']??'solid';
 $variant=in_array($variant,array('solid','outline','ghost','download'),true)?$variant:'solid';
 if($variant==='download')$args+=array('icon'=>'download','leading_icon'=>'music');
 $classes='rdc-btn';
 if($variant!=='solid')$classes.=' rdc-btn--'.$variant;
 foreach(preg_split('/\s+/',trim($args['class']??''))as$class)if($class!=='')$classes.=' '.sanitize_html_class($class);
 $download=$args['download']??false;
 $attributes=$download?(is_string($download)?' download="'.esc_attr($download).'"':' download'):'';
 if(!empty($args['new_tab']))$attributes.=' target="_blank" rel="noopener noreferrer"';
 foreach(array('data-rdc-pnl-rvl','data-rdc-en')as$key)if(!empty($args[$key]))$attributes.=' '.$key;
 $icon=icon($args['icon']??'arrow-up-right');
 if($icon==='')$icon=icon('arrow-up-right');
 $label_html='<span class="rdc-btn__lbl"><span>'.esc_html($label).'</span><span aria-hidden="true">'.esc_html($label).'</span></span>';
 $icon_html='<span class="rdc-btn__ico" aria-hidden="true">'.$icon.$icon.'</span>';
 $content=!empty($args['icon_only'])?$icon_html:(($args['icon_position']??'end')==='start'?$icon_html.$label_html:$label_html.$icon_html);
 if(!empty($args['leading_icon']))$content=icon((string)$args['leading_icon']).$content;
 if(!empty($args['disabled']))return '<button type="button" class="'.esc_attr($classes).'" disabled aria-disabled="true">'.$content.'</button>';
 if(!empty($args['audio'])&&is_array($args['audio'])){
  $audio=$args['audio'];return '<button type="button" class="'.esc_attr($classes).'" data-rdc-audio-key="'.(int)($audio['key']??0).'" data-rdc-audio-src="'.esc_url($audio['url']??'').'" data-rdc-audio-title="'.esc_attr(get_the_title((int)($audio['key']??0))).'" data-rdc-audio-artist="'.esc_attr($audio['artist']??'').'" data-rdc-audio-cover="'.esc_url($audio['cover']??'').'" data-rdc-audio-duration="'.esc_attr($audio['duration']??'').'" aria-pressed="false" aria-label="'.esc_attr($label).'">'.$content.'</button>';
 }
 if(!empty($args['icon_only']))$attributes.=' aria-label="'.esc_attr($label).'"';
 return '<a class="'.esc_attr($classes).'" href="'.esc_url($url).'"'.$attributes.'>'.$content.'</a>';
}
