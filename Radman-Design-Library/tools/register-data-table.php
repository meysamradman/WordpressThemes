<?php
/** Register only the independent table sample in the local library. */
if(PHP_SAPI!=='cli')exit(1);
require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8097'||!get_option('rdl_local_ready')||get_stylesheet()!=='radman-library')throw new RuntimeException('Wrong destination.');
if(!term_exists('table','rdl_group')){$term=wp_insert_term('جدول‌ها','rdl_group',array('slug'=>'table'));if(is_wp_error($term))throw new RuntimeException($term->get_error_message());}
$slug='data-table';$module=RadmanLibrary\modules()[$slug];$post=get_page_by_path($slug,OBJECT,'rdl_design');
$id=wp_insert_post(array('ID'=>$post?$post->ID:0,'post_type'=>'rdl_design','post_status'=>'publish','post_name'=>$slug,'post_title'=>$module['title'],'post_excerpt'=>$module['motion']),true);
if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
update_post_meta($id,'_rdl_module',$slug);
foreach(array('rdl_group'=>array('table'),'rdl_kind'=>array('section'),'rdl_fx'=>$module['effects']) as $taxonomy=>$terms){$result=wp_set_object_terms($id,$terms,$taxonomy);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
echo get_permalink($id).PHP_EOL;
