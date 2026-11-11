<?php
namespace RadmanCT\Renderers { function context() { return $GLOBALS['context']; } }
namespace RadmanCT\Settings { function values() { return $GLOBALS['settings']; } }
namespace {
 define('ABSPATH','/');
 $context=[]; $settings=[]; $styles=[];
 function __($text,$domain) { return $text; }
 function add_action(...$args) {}
 function is_admin() { return false; }
 function is_feed() { return false; }
 function get_post_meta(...$args) { throw new \RuntimeException('Item overrides must not be read.'); }
 function wp_add_inline_style($handle,$css) { $GLOBALS['styles'][$handle]=$css; }
 function expect(bool $ok,string $message): void { if (!$ok) { throw new \RuntimeException($message); } }
 require __DIR__.'/../radmanct/inc/content-colors.php';
 $settings=['blog_single_color_background'=>'#112233','catalog_single_color_background'=>'#445566','blog_archive_color_background'=>'#ABCDEF','blog_single_color_title'=>'#999999'];
 expect(6===count(\RadmanCT\ContentColors\defaults()) && ['background']===array_keys(\RadmanCT\ContentColors\fields()),'Only six view backgrounds should remain.');
 \RadmanCT\ContentColors\enqueue();
 expect(!$styles,'Unrelated page emitted CSS.');
 foreach ([['blog','single','#112233'],['blog','archive','#abcdef'],['catalog','single','#445566']] as $view) {
  $context=array_slice($view,0,2); \RadmanCT\ContentColors\enqueue();
  expect('#main-content{background-color:'.$view[2].';}'===$styles['rdct-theme'],'Wrong scope or unrelated styles emitted.');
 }
 expect([]===\RadmanCT\ContentColors\resolve('unknown',$settings),'Unknown scope accepted.');
 expect(''===\RadmanCT\ContentColors\css(['background'=>'red;background:url(//remote)','title'=>'#123456']),'Unsafe or removed colors emitted CSS.');
 expect(['background'=>'#112233']===\RadmanCT\ContentColors\normalize(['background'=>[],'text'=>'#ffffff'],['background'=>'#112233']),'Invalid value lost previous background.');
 expect([]===\RadmanCT\ContentColors\normalize(['background'=>''],['background'=>'#112233']),'Clearing background did not restore inheritance.');
 echo "Passed: settings-only backgrounds, independent views, no metadata reads, removed colors ignored and safe CSS.\n";
}
