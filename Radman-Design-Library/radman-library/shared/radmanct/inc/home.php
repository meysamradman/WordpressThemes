<?php
namespace RadmanCT\Home;
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/home/art.php';
require_once __DIR__ . '/home/projects.php';
function image_id( int $post_id = 0 ): int { return \RadmanCT\media_id( 'hero' ); }
function data( int $post_id = 0 ): array {
 return apply_filters('radmanct/home/data',array(
  'heading'=>get_bloginfo('name'),
  'subheading'=>get_bloginfo('description')?:get_bloginfo('name'),
  'accent'=>'',
  'description'=>'',
  'button_url'=>\RadmanCT\Footer\data()['contact'],'button_label'=>__('Start a project','radmanct'),
 ),$post_id);
}
function offerings():array { return apply_filters('radmanct/home/offerings',array()); }
function media( int $post_id = 0 ): void {
 $id=image_id();if($id)echo wp_get_attachment_image($id,'full',false,array('class'=>'rdc-im__im','alt'=>'','loading'=>'eager','fetchpriority'=>'high','decoding'=>'async'));
}
function heading( string $text, string $tag, string $color = '', bool $rotating = false ): void {
 $tag=in_array($tag,array('h1','h2','h3','p'),true)?$tag:'p';
 if(!$rotating){echo '<'.$tag.'>'.esc_html($text).'</'.$tag.'>';return;}
 echo '<'.$tag.' class="rdc-tx" data-rdc-rot data-word-delay="3.5" data-word-duration=".8"><span class="rdc-tx__rtr" data-word-style="slide"><span class="rdc-tx__wds">';
 foreach(explode("\n",$text)as$line)echo '<span class="rdc-tx__wd">'.esc_html($line).'</span>';
 echo '</span></span><button type="button" class="rdc-tx__play" data-word-play hidden aria-pressed="true" aria-label="'.esc_attr__('Play or pause rotating words','radmanct').'">&#10074;&#10074;</button></'.$tag.'>';
}
add_filter('wp_preload_resources',static function(array $resources):array{
 if(!\RadmanLibrary\Samples\raw()||!in_array(\RadmanLibrary\module(get_queried_object_id())['slug']??'',['home-hero','page-home'],true)||post_password_required())return $resources;
 $id=image_id();$image=$id?wp_get_attachment_image_src($id,'full'):false;
 if($image)$resources[]=array('href'=>$image[0],'as'=>'image','fetchpriority'=>'high','imagesrcset'=>wp_get_attachment_image_srcset($id,'full'),'imagesizes'=>wp_get_attachment_image_sizes($id,'full'));
 return $resources;
});
