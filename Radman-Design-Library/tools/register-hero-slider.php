<?php
/** Idempotent local registration; no import, remote media or customer writes. */
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready')||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination.');
function hero_term(string $slug,string $name,int $parent=0):int {
 $term=get_term_by('slug',$slug,'rdl_group');
 $result=$term?wp_update_term($term->term_id,'rdl_group',array('name'=>$name,'parent'=>$parent)):wp_insert_term($name,'rdl_group',array('slug'=>$slug,'parent'=>$parent));
 if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
 return (int)$result['term_id'];
}
$hero=hero_term('hero','هیرو');
hero_term('hero-static','هیروی ثابت',$hero);
hero_term('hero-slider','هیروی اسلایدری',$hero);
foreach(array('drag'=>'کشیدن و سوایپ','gsap'=>'GSAP','entrance'=>'ورود متن')as$slug=>$name){
 if(!term_exists($slug,'rdl_fx')){$result=wp_insert_term($name,'rdl_fx',array('slug'=>$slug));if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
}
$slug='hero-slider';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);
foreach(array('rdl_group'=>array('hero','hero-slider'),'rdl_kind'=>array('section'),'rdl_fx'=>$module['effects'])as$taxonomy=>$terms){$result=wp_set_object_terms($id,$terms,$taxonomy);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
// Classify the existing hero without touching its content, media or animation.
$static=get_page_by_path('home-hero',OBJECT,'rdl_design');
if($static){$result=wp_set_object_terms($static->ID,array('hero','hero-static'),'rdl_group');if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
echo get_permalink($id).PHP_EOL;
require_once get_template_directory().'/modules/hero-slider/data.php';
foreach(RadmanLibrary\Designs\HeroSlider\data()['slides'] as $slide){
 $image=wp_get_attachment_image_src($slide['image'],'full');
 if(!$image)throw new RuntimeException('Missing local hero image.');
 echo 'Media '.$slide['image'].': '.$image[1].'x'.$image[2].PHP_EOL;
}
