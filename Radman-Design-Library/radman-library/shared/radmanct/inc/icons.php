<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function icon(string $name,array $options=array()):string {
 static $catalog;if(null===$catalog)$catalog=require __DIR__.'/icons/catalog.php';
 if(!isset($catalog[$name]))return '';
 $stroke=1.5;
 $direction=in_array($name,array('arrow-right','arrow-up-right'),true)?' data-directional="true"':'';
 return '<svg class="rdc-ico"'.$direction.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="'.esc_attr((string)$stroke).'" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.$catalog[$name].'</svg>';
}
